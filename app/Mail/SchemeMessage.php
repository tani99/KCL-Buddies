<?php

namespace App\Mail;

use App\Scheme;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SchemeMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $userEmail;
    public $schemeName;
    public $content;

    /**
     * Create a new message instance.
     */
    public function __construct(Request $request, Scheme $scheme)
    {
        $this->userEmail = $request->user()->email;
        $this->schemeName = $scheme->name;
        $this->content = $request->input('content') ?? '';
        $this->subject = $request->input('subject') ?? $this->schemeName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))->subject($this->subject)->view('emails.scheme');
    }
}
