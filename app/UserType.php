<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    /**
     * @return array An associative array containing the singular and plural names of this user type.<br>e.g. ['singular' => 'Example', 'plural' => 'Examples']
     */
    public function getNames(): array
    {
        return [
            'singular' => $this->name_singular,
            'plural' => $this->name_plural
        ];
    }

    public function schemeJoinCodes()
    {
        return $this->hasMany(SchemeJoinCode::class, 'user_type_id');
    }

    public function schemeUsers()
    {
        return $this->hasMany(SchemeUser::class, 'user_type_id');
    }

    /**
     * @return array A mapping of user type IDs to an array of their names.
     */
    public static function getAllNames(): array
    {
        $userTypeNames = [];
        foreach (UserType::all() as $userType) {
            $userTypeNames[$userType->id] = $userType->getNames();
        }
        return $userTypeNames;
    }
}
