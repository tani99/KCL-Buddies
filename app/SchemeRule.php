<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeRule extends Model
{
    public function getValue(): string
    {
        return isset($this->value) ? $this->value : $this->rule()->first()->default_value;
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }
}
