<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SchemeUser extends Model
{
    /**
     * @param string $key The name of the preference
     * @return mixed|null The default value for the specified preference.
     */
    private function getDefaultPreference(string $key)
    {
        if ($key == 'max_newbies') {
            return $this->scheme()->first()->getMaxNewbies();
        } elseif ($key == 'subscribed') {
            return true;
        } else {
            return null;
        }
    }

    /**
     * @param string $key The name of the preference
     * @return mixed|null The value of the specified preference.
     */
    public function getPreference(string $key)
    {
        $preferences = $this->getPreferences();
        if (!isset($preferences)) {
            return $this->getDefaultPreference($key);
        }
        if (array_key_exists($key, $preferences)) {
            return $preferences[$key];
        } else {
            return $this->getDefaultPreference($key);
        }
    }

    /**
     * @return array|null A mapping of preference names to their values.
     */
    public function getPreferences(): ?array
    {
        if (isset($this->preferences)) {
            $decodedPreferences = json_decode($this->preferences, true);
            if ($decodedPreferences !== null && gettype($decodedPreferences) === 'array') {
                return $decodedPreferences;
            }
        }
        return null;
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    /**
     *
     *
     * @param int $schemeID
     * @param int|null $userTypeID
     * @param bool $approvedOnly Set to true to fetch approved users only
     * @return array A list of Users belonging to the specified scheme and optionally belonging to the user type specified.
     */
    public static function users(int $schemeID, int $userTypeID = null, bool $approvedOnly = true): array
    {
        if (!isset($schemeID)) return [];
        $schemeUserBuilder = SchemeUser::whereSchemeId($schemeID);
        if (isset($userTypeID)) {
            $schemeUserBuilder = $schemeUserBuilder->whereUserTypeId($userTypeID);
        }
        if ($approvedOnly) {
            $schemeUserBuilder = $schemeUserBuilder->whereApproved(true);
        }
        $userIDs = $schemeUserBuilder->pluck('user_id')->all();
        return User::whereIn('id', $userIDs)->get()->all();
    }
}
