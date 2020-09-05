<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeType extends Model
{
    public function schemes()
    {
        return $this->hasMany(Scheme::class, 'type_id');
    }
}
