<?php

declare(strict_types=1);
namespace App\Mail;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class NacoNotificationMail extends Mailable implements ShouldQueue
{
 use Queueable, SerializesModels;
 public function __construct(public Notification $notification){}
 public function envelope():Envelope{return new Envelope(subject:'NACO: '.$this->notification->title);}
 public function content():Content{return new Content(view:'emails.naco-notification',with:['notification'=>$this->notification]);}
}
