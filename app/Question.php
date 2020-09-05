<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /**
     * @return array|null An associative array of validations.
     */
    public function getValidation(): ?array
    {
        if (isset($this->validation)) {
            $decodedValidation = json_decode($this->validation, true);
            if ($decodedValidation !== null && is_array($decodedValidation)) {
                return $decodedValidation;
            }
        }
        return null;
    }

    public function questionType()
    {
        return $this->belongsTo(QuestionType::class, 'type_id');
    }

    /**
     * @return Question[]|\Illuminate\Database\Eloquent\Collection A list of all questions, sorted by insertion order then further by ID.
     */
    public static function getAllOrdered()
    {
        return self::all()->sort(function ($a, $b) {
            if ($a->insertion_order === $b->insertion_order) {
                return $a->id < $b->id ? -1 : ($a->id === $b->id ? 0 : 1);
            } else {
                return $a->insertion_order > $b->insertion_order ? -1 : 1;
            }
        });
    }
}
