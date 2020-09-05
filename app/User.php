<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'department'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return int|null The age of the user. Returns null if the user has not set their birthdate.
     * @throws \Exception
     */
    public function getAge(): ?int
    {
        if (!isset($this->birthdate)) {
            return null;
        }
        $birthDate = \DateTimeImmutable::createFromFormat('Y-m-d', $this->birthdate);
        return $birthDate->diff(new \DateTimeImmutable())->y;
    }

    /**
     * @param int $schemeID
     * @param int $questionID
     * @return array|null The answer to the specified question, specific to the specified scheme.
     */
    public function getAnswer(int $schemeID, int $questionID)
    {
        $schemeQuestion = SchemeQuestion::whereSchemeId($schemeID)->whereQuestionId($questionID)->first();
        if (!isset($schemeQuestion)) return null;
        $questionAnswer = QuestionAnswer::whereSchemeQuestionId($schemeQuestion->id)->whereUserId($this->id)->first();
        if (!isset($questionAnswer)) return null;
        return $questionAnswer->getAnswer();
    }

    /**
     * @param int $schemeID
     * @return array An array of answers the user had entered in the questionnaire for the specified scheme.
     */
    public function getAnswers(int $schemeID): array
    {
        $answers = [];
        foreach (SchemeQuestion::whereSchemeId($schemeID)->get() as $schemeQuestion) {
            $questionAnswer = QuestionAnswer::whereSchemeQuestionId($schemeQuestion->id)->whereUserId($this->id)->first();
            $answers[$schemeQuestion->question_id] = isset($questionAnswer) ? $questionAnswer->getAnswer() : [];
        }
        return $answers;
    }

    /**
     * @return string The file path of this user's avatar.
     */
    public function getAvatar(): string
    {
        return isset($this->avatar) ? $this->avatar : $this->getDefaultAvatar();
    }

    /**
     * @return string The file path of the default user avatar.
     */
    public function getDefaultAvatar(): string
    {
        return 'default.png';
    }

    public function getFullName(): string
    {
        return $this->name;
    }

    public function getGender(): string
    {
        switch ($this->gender) {
            case 1:
                return 'Male';
            case 2:
                return 'Female';
            case 3:
                return 'Other';
            case 4:
                return 'Unspecified';
            default:
                return 'Unknown';
        }
    }

    /**
     * @param int $schemeID
     * @param string $key The name of the preference
     * @return mixed|null The value of the specified preference.
     */
    public function getPreference(int $schemeID, string $key)
    {
        $schemeUser = SchemeUser::whereSchemeId($schemeID)->whereUserId($this->id)->first();
        if (!isset($schemeUser)) return null;
        return $schemeUser->getPreference($key);
    }

    /**
     * @param int $schemeID
     * @return array|null A mapping of preference names to their values for the specified scheme.
     */
    public function getPreferences(int $schemeID): ?array
    {
        $schemeUser = SchemeUser::whereSchemeId($schemeID)->whereUserId($this->id)->first();
        if (!isset($schemeUser)) return null;
        return $schemeUser->getPreferences();
    }

    /**
     * @param array $roles An array of role names
     * @return bool True if this user has any one of the specified roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->first() !== null;
    }

    /**
     * @param string $role
     * @return bool True if this user has the specified role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->whereName($role)->first() !== null;
    }

    /**
     * @return bool True if this user belongs to a Microsoft account.
     */
    public function isMicrosoftAccount(): bool
    {
        return !isset($this->password);
    }

    public function preferences()
    {
        return $this->hasOne(UserPreferences::class, 'user_id');
    }

    public function questionAnswers()
    {
        return $this->hasMany(QuestionAnswer::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function schemeUsers()
    {
        return $this->hasMany(SchemeUser::class, 'user_id');
    }
}
