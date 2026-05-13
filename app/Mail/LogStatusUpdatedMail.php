<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $logOriginal;
    public $status;
    public $dados;

    public function __construct($user, $logOriginal, $status, $dados)
    {
        $this->user = $user;
        $this->logOriginal = $logOriginal;
        $this->status = $status;
        $this->dados = $dados;
    }

    public function build()
    {
        $subject = "O teu pedido de alteração foi " . ($this->status === 'approved' ? 'Aceite' : 'Recusado');
        
        return $this->subject($subject)
                    ->view('mails.log_status_updated');
    }
}