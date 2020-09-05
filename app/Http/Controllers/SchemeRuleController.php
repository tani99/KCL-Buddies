<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request;
use Illuminate\Http\Response as Response;
use App\Scheme as Scheme;
use App\Rule as Rule;
use App\SchemeRule as SchemeRule;

class SchemeRuleController extends Controller
{
    use SchemeAuthentication;
    use RuleValidation;
    use RedirectMessages;

    private static $defaultNoAccessMessage = 'You must be an administrator for that scheme to perform that action.';

    /**
     * Create a new scheme-rule controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function index(int $schemeID, Request $request)
    {
        $user = $request->user();
        $schemeAccess = $this->getSchemeAccess($schemeID, $user);
        if ($schemeAccess !== -1 && $schemeAccess !== 1) {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'A scheme with that ID does not exist.']);
        }

        $rulesData = []; // An array of arrays containing the rule instance of the scheme rule and their value.
        foreach (Rule::all() as $rule) {
            $schemeRule = SchemeRule::whereRuleId($rule->id)->whereSchemeId($schemeID)->first();
            if (isset($schemeRule)) {
                $rulesData[] = [$rule, $schemeRule->value];
            } else {
                $rulesData[] = [$rule, $rule->default_value];
            }
        }

        $data = $this->applySessionToData($request);
        $data['scheme'] = $scheme;
        $data['rulesData'] = $rulesData;
        $data['accessLevel'] = $schemeAccess === -1 ? 'sysadmin' : 'user';
        return view('rule.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     */
    public function edit(int $schemeID, Request $request)
    {
        $user = $request->user();
        $accessLevel = null;
        if ($this->isSystemAdministrator($user)) {
            $accessLevel = 'sysadmin';
        } else if ($this->isSchemeAdministrator($schemeID, $user)) {
            $accessLevel = 'user';
        } else {
            return redirect()->back();
        }
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect()->route('schemes.index')->withErrors(['msg' => 'A scheme with that ID does not exist.']);
        }

        $rulesData = []; // A mapping of rule IDs to their data (including the name and description)
        foreach (Rule::all() as $rule) {
            $schemeRule = SchemeRule::whereRuleId($rule->id)->whereSchemeId($schemeID)->first();
            $ruleValidation = isset($rule->validation) ? json_decode($rule->validation) : null;
            $ruleData = [
                'name' => $rule->name,
                'description' => $rule->description,
                'validation' => $ruleValidation
            ];
            if (isset($schemeRule)) {
                $ruleData['value'] = $schemeRule->value;
            } else {
                $ruleData['value'] = $rule->default_value;
            }
            $rulesData[$rule->id] = $ruleData;
        }

        $data = [];
        $data['scheme'] = $scheme;
        $data['rulesData'] = $rulesData;
        $data['accessLevel'] = $accessLevel;
        return view('rule.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $schemeID
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(int $schemeID, Request $request)
    {
        $user = $request->user();
        if (!$this->checkAccessToScheme($schemeID, $user)) {
            return redirect('schemes')->withErrors(['msg' => self::$defaultNoAccessMessage]);
        }
        $scheme = Scheme::find($schemeID);
        if (!isset($scheme)) {
            return redirect('schemes')->withErrors(['msg' => 'A scheme with that ID does not exist.']);
        }

        $rules = Rule::all();
        $this->validate($request, $this->createRulesInputValidation($rules));

        $schemeRulesData = [];
        foreach ($rules as $rule) {
            $schemeRule = SchemeRule::whereSchemeId($schemeID)->whereRuleId($rule->id)->first();
            if (!isset($schemeRule)) {
                $inputValue = $request->input(strtolower(str_replace(' ', '_', $rule->name)));
                if ($inputValue != $rule->default_value) {
                    $schemeRulesData[] = [
                        'scheme_id' => $schemeID,
                        'rule_id' => $rule->id,
                        'value' => $request->input(strtolower(str_replace(' ', '_', $rule->name)))
                    ];
                }
            } else {
                $schemeRule->value = $request->input(strtolower(str_replace(' ', '_', $rule->name)));
                if ($schemeRule->value != $rule->default_value) {
                    $schemeRule->save();
                } else {
                    $schemeRule->delete();
                }
            }
        }
        SchemeRule::insert($schemeRulesData);

        return redirect()->route('rules.index', ['scheme_id' => $schemeID])->with('success', 'Successfully updated the rules for scheme \'' . $scheme->name . '\'!');
    }
}
