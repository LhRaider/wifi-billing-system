<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $customer,
        public User $admin
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Customer Registration - '
                . ($this->admin->network_name
                    ?: $this->admin->business_name
                    ?: config('app.name'))
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer-registered',
            with: [
                'customer' => $this->customer,
                'admin' => $this->admin,
                'customersUrl' => route('admin.customers.index'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}