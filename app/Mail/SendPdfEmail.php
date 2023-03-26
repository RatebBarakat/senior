<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPdfEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $body
     * @param string $pdf
     * @return void
     */
    public function __construct($subject, $body, $pdf)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.pdf')
                    ->attachData($this->pdf, 'my-pdf.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
