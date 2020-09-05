<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemePairing extends Model
{
    public function scheme()
    {
        return $this->belongsTo('scheme_id');
    }
}
