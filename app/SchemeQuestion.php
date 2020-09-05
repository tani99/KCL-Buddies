<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class SchemeQuestion extends Model
{
    function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    function questionAnswers()
    {
        return $this->hasMany(QuestionAnswer::class, 'scheme_question_id');
    }

    function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }

    /**
     * @param int $schemeID
     * @return Collection|static[] A collection of SchemeQuestions, ordered by descending priority then ascending ID, for the specified scheme.
     */
    public static function getOrderedQuestions(int $schemeID)
    {
        return SchemeQuestion::whereSchemeId($schemeID)->orderBy('priority', 'DESC')->orderBy('id', 'ASC');
    }
}
