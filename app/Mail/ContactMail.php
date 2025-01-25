<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Http\Request;

class ContactMail extends Mailable
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function build()
    {
        return $this->subject('お問い合わせがありました')
                    ->view('emails.contact')
                    ->with([
                        'name' => $this->request->name,
                        'email' => $this->request->email,
                        'message' => $this->request->message,
                    ]);
    }
}
