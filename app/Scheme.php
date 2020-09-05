<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    /**
     * @return array An array of SchemeUsers who are buddies in this scheme.
     */
    public function getBuddies(): array
    {
        return SchemeUser::users($this->id, 2);
    }

    /**
     * @return array|null A list of departments this scheme belongs to.
     */
    public function getDepartments(): ?array
    {
        if (isset($this->departments)) {
            $decodedDepartments = json_decode($this->departments, true);
            if ($decodedDepartments !== null && is_array($decodedDepartments)) {
                return $decodedDepartments;
            }
        }
        return null;
    }

    /**
     * @return string The file path for the icon of this scheme.
     */
    public function getIcon(): string
    {
        return isset($this->icon) ? $this->icon : self::getDefaultIcon();
    }

    /**
     * @return array An array of SchemeUsers who are newbies in this scheme.
     */
    public function getNewbies(): array
    {
        return SchemeUser::users($this->id, 1);
    }

    /**
     * @return int The maximum number of newbies in a pairing.
     */
    public function getMaxNewbies(): int
    {
        $newbies = $this->getRuleValue(2);
        if (!isset($newbies)) return 1;
        settype($newbies, 'integer');
        return $newbies;
    }

    /**
     * @return int The maximum number of buddies in a pairing.
     */
    public function getMaxBuddies()
    {
        $buddies = $this->getRuleValue(1);
        if (!isset($buddies)) return 1;
        settype($buddies, 'integer');
        return $buddies;
    }

    /**
     * @param int $ruleID
     * @return string|null The value of the specified rule set for this scheme.
     */
    public function getRuleValue(int $ruleID): ?string
    {
        $schemeRule = $this->schemeRules()->whereRuleId($ruleID)->first();
        if (isset($schemeRule)) {
            return $schemeRule->value;
        } else {
            $rule = Rule::find($ruleID)->first();
            if (isset($rule)) {
                return $rule->default_value;
            } else {
                return null;
            }
        }
    }

    public function schemeAdmins()
    {
        return $this->hasMany(SchemeAdmin::class, 'scheme_id');
    }

    public function schemeJoinCodes()
    {
        return $this->hasMany(SchemeJoinCode::class, 'scheme_id');
    }

    public function schemePairings()
    {
        return $this->hasMany(SchemePairing::class, 'scheme_id');
    }

    public function schemeRules()
    {
        return $this->hasMany(SchemeRule::class, 'scheme_id');
    }

    public function schemeType()
    {
        return $this->belongsTo(SchemeType::class, 'type_id');
    }

    public function schemeUsers()
    {
        return $this->hasMany(SchemeUser::class, 'scheme_id');
    }

    /**
     * @return string The file path of the default scheme icon.
     */
    public static function getDefaultIcon(): string
    {
        return 'default.png';
    }
}
