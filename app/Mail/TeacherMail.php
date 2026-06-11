<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeacherMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $password;

    public function __construct($teacher, $password)
    {
        $this->teacher = $teacher;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Teacher Login Details')
            ->view('emails.teachercreate');
    }
    
}


// public function __construct()
    // {
    //     //
    // }

    // /**
    //  * Get the message envelope.
    //  */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Account Created Mail',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
