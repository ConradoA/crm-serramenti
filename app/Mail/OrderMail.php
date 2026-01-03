<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public \App\Models\Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ordine Fornitore ' . $this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order', // Need to create this too (simple body)
        );
    }

    public function attachments(): array
    {
        $company = \App\Models\Company::first();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order', [
            'order' => $this->order,
            'company' => $company,
        ]);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $pdf->output(),
                'Ordine_' . $this->order->number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
