<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $subject;
    public $document;
    public $ccEmail;
    public $bccEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(private $data)
    {

        $emailTemplate   = EmailTemplate::firstWhere('slug',$data['slug']);
        $mailMessage = str_replace(array_keys($data['replace']), array_values($data['replace']), $emailTemplate->content);

        $this->content = $mailMessage;
        $this->document = !empty($data['document'])?$data['document']:'';
        $this->ccEmail = !empty($data['cc'])?$data['cc']:'';
        $this->bccEmail = !empty($data['bcc'])?$data['bcc']:'';
        $this->subject = $emailTemplate->subject;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            cc: $this->ccEmail,
            bcc: $this->bccEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.common',
            with: ['content' => $this->content],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
