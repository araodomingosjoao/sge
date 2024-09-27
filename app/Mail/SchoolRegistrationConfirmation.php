<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\School;
use App\Models\User;

class SchoolRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $school;
    public $user;

    public function __construct(School $school, User $user)
    {
        $this->school = $school;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('School Registration Confirmation')
                    ->view('emails.school-registration-confirmation');
    }
}