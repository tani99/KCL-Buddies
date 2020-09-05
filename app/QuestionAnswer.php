<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    /**
     * @return array|null The answer represented as an array.
     */
    public function getAnswer(): ?array
    {
        $answerDecoded = json_decode($this->answer, true);
        if ($answerDecoded !== null && is_array($answerDecoded)) {
            return $answerDecoded;
        } else {
            return null;
        }
    }

    public function question()
    {
        return $this->schemeQuestion()->first()->question();
    }

    public function scheme()
    {
        return $this->schemeQuestion()->first()->scheme();
    }

    public function schemeQuestion()
    {
        return $this->belongsTo('SchemeQuestion', 'scheme_question_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }
}
