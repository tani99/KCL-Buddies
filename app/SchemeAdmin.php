<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeAdmin extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }
}
