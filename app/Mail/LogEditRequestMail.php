<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LogEditRequestMail extends Mailable
{
    use Queueable, SerializesModels;

  
    public $solicitante;
    public $logOriginal;
    public $novosDados;
    public $approvalId;

    public function __construct($solicitante, $logOriginal, $novosDados, $approvalId)
    {
        $this->solicitante = $solicitante;
        $this->logOriginal = $logOriginal;
        $this->novosDados = $novosDados;
        $this->approvalId = $approvalId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pending Edit Request - ' . $this->solicitante->name,
        );
    }

    public function content(): Content
    {
       
        return new Content(
            view: 'mails.log_request',
        );
    }
}