<?php

namespace LaravelFlare\MailDebug;

use Swift_Mime_Message;
use Illuminate\Mail\Transport\Transport;
use LaravelFlare\MailDebug\MailDebugManager;

class DebugTransport extends Transport
{
    /**
     * The Mail Debug instance.
     *
     * @var \LaravelFlare\MailDebug\MailDebug
     */
    protected $debug;

    /**
     * Create a new preview transport instance.
     *
     * @param  \LaravelFlare\MailDebug\MailDebug $debug
     *
     * @return void
     */
    public function __construct(MailDebugManager $debug)
    {
        $this->debug = $debug;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param \Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $this->debug->mail($message);
    }
}
