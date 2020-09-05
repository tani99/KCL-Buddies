<?php

namespace App\Mail;

use App\Scheme;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PairingsNotify extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userID;
    public $userName;
    public $schemeID;
    public $schemeName;
    public $users;
    public $userTypeNames;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Scheme $scheme, array $users, array $userTypeNames)
    {
        $this->userID = $user->id;
        $this->userName = $user->getFullName();
        $this->schemeID = $scheme->id;
        $this->schemeName = $scheme->name;
        $this->users = $users;
        $this->userTypeNames = $userTypeNames;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))->subject('Pairings notification')->view('emails.pairings');
    }
}
