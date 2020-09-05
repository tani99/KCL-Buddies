<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannedSchemeUser extends Model
{
    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
