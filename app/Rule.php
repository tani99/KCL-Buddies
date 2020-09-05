<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    public function schemeRules()
    {
        return $this->hasMany(SchemeRule::class, 'rule_id');
    }
}
