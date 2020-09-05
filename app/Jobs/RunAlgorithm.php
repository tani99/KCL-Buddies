<?php

namespace App\Jobs;

use App\Algorithm as Algorithm;
use App\Http\Controllers\SchemePairingController as SchemePairingController;
use App\Scheme as Scheme;
use App\SchemePairing as SchemePairing;
use App\SchemeUser as SchemeUser;
use App\Http\Controllers\EmailValidation as EmailValidation;
use DateTime as DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RunAlgorithm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use EmailValidation;

    private $currentDate = null;
    private $schemeID = null;

    /**
     * Create a new job instance.
     *
     * @param int|null $schemeID The specific scheme to run the algorithm for. If not specified, all schemes will be checked to see if the algorithm should be run.
     * @return void
     * @throws \Exception
     */
    public function __construct(int $schemeID = null)
    {
        $this->currentDate = new DateTime();
        $this->schemeID = $schemeID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        if (isset($this->schemeID)) {
            $this->runSingle();
        } else {
            $this->runAll();
        }
    }

    private function runSingle(): void
    {
        $scheme = Scheme::find($this->schemeID);
        if (isset($scheme)) {
            $this->runAlgorithm($scheme, $this->canEmail(config('mail.host')));
        }
    }

    private function runAll(): void
    {
        $canEmail = $this->canEmail(config('mail.host'));
        $schemes = Scheme::all();
        foreach ($schemes as $scheme) {
            $mapping = null;
            if ($scheme->type_id == 1) {
                if (!isset($scheme->last_run)) {
                    $endDate = date_create_from_format('Y-m-d h:i:s', $scheme->date_end . ' 11:59:00');
                    if ($this->currentDate >= $endDate) {
                        $this->runAlgorithm($scheme, $canEmail);
                    }
                }
            } else if ($scheme->type_id == 2) {
                $lastRunDate = isset($scheme->last_run) ? date_create_from_format('Y-m-d', $scheme->last_run) : null;
                $shouldRun = !isset($lastRunDate);
                if (!$shouldRun) {
                    $weeksBetween = $scheme->getRuleValue(3);
                    $shouldRun = $this->currentDate >= $lastRunDate->add(date_interval_create_from_date_string(($weeksBetween * 7) . ' days'));
                }
                if ($shouldRun) {
                    $this->runAlgorithm($scheme, $canEmail);
                }
            }
        }
    }

    /**
     * Run the algorithm for a particular scheme.
     *
     * @param Scheme $scheme
     * @param bool $shouldEmail
     * @return array The mapping produced by the algorithm.
     */
    private final function runAlgorithm(Scheme $scheme, bool $shouldEmail = false): array
    {
        $scheme->last_run = $this->currentDate->format('Y-m-d');
        $scheme->save();

        // Run the algorithm
        $allPairings = Algorithm::newAlgorithmByScheme($scheme)->createMapping();
        if (!empty($allPairings)) {
            $buddyPairings = $allPairings[0];
            $newbiesPairings = $allPairings[1];
            if (!empty($buddyPairings)) {
                $pairingUserIDs = [];
                for ($i = 0; $i < count($buddyPairings); ++$i) {
                    $buddies = $buddyPairings[$i];
                    $newbies = $newbiesPairings[$i];
                    if (empty($buddies) || !isset($newbies) || empty($newbies)) continue;
                    $schemePairing = new SchemePairing();
                    $schemePairing->scheme_id = $scheme->id;
                    $schemePairing->save();

                    foreach ($buddies as $buddy) {
                        $buddySchemeUser = SchemeUser::whereSchemeId($scheme->id)->whereUserId($buddy->id)->first();
                        if (isset($buddySchemeUser)) {
                            $buddySchemeUser->pairing_id = $schemePairing->id;
                            $buddySchemeUser->save();
                            $pairingUserIDs[] = $buddySchemeUser->user_id;
                        }
                    }
                    foreach ($newbies as $newbie) {
                        $newbieSchemeUser = SchemeUser::whereSchemeId($scheme->id)->whereUserId($newbie->id)->first();
                        if (isset($newbieSchemeUser)) {
                            $newbieSchemeUser->pairing_id = $schemePairing->id;
                            $newbieSchemeUser->save();
                            $pairingUserIDs[] = $newbieSchemeUser->user_id;
                        }
                    }
                }
                if ($shouldEmail) {
                    SchemePairingController::sendEmailToPairings($scheme, $pairingUserIDs, $buddyPairings, $newbiesPairings);
                }
            }
        }
        return $allPairings;
    }
}
