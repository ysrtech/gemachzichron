<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoteNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $member;
    public $note;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Member $member, Note $note, $userName)
    {
        $this->member = $member;
        $this->note = $note;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Message')
                    ->view('emails.note-notification');
    }
}
