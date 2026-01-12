<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        string $subject,
        string $html,
        public string $pdfPath,
        public string $filename,
        public ?string $logoPath = null,
    ) {
        $this->subject = $subject;
        $this->html = $html;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->html, // Use htmlString instead of view
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as($this->filename)
                ->withMime('application/pdf'),
        ];
    }

    public function build()
    {
        // Manipulate the underlying Symfony Email to embed the logo
        $this->withSymfonyMessage(function (Email $message): void {
            $cidSrc = '';

            // Only embed logo if it exists
            if ($this->logoPath) {
                $message->embedFromPath($this->logoPath, 'invoice-logo');
                $cidSrc = 'cid:invoice-logo';
            }

            // Get current HTML body and replace placeholder
            $htmlWithLogo = str_replace(
                '%%INVOICE_LOGO_SRC%%',
                $cidSrc,
                $this->html
            );

            // Set final HTML
            $message->html($htmlWithLogo);
        });

        return $this;
    }
}
