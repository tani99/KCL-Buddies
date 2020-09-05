<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeJoinCode extends Model
{
    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
}
