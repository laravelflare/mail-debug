<?php

namespace LaravelFlare\MailDebug;

use Config;
use Session;
use Swift_Mime_Message;
use Illuminate\Filesystem\Filesystem;

class MailDebugManager
{
    /**
     * File System Instance
     * 
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Message Instance
     * 
     * @var \Swift_Mime_Message
     */
    protected $message;

    /**
     * __construct 
     * 
     * @param Filesystem $files 
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        $this->createPreviewDirectory();
        $this->cleanOldPreviews();   
    }

    /**
     * Perform the Debug Mail Actions
     * 
     * @param  Swift_Mime_Message $message 
     * 
     * @return 
     */
    public function mail(Swift_Mime_Message $message)
    {
        $this->message = $message;

        $this->files->put($this->path(), $this->content());

        $this->setSession();
    }

    /**
     * Was an Email Sent on the Last Request?
     * 
     * @return boolean
     */
    public function wasSent()
    {
        if ($this->hasSession()) {
            return true;
        }

        return false;
    }

    /**
     * Return the Email Preview Path
     * 
     * @return string
     */
    public function preview()
    {
        return $this->getSession();
    }

    /**
     * Return the Debug Mail Storage Path
     * 
     * @return string
     */
    public function storage()
    {
        return Config::get('mail-debug.path');
    }
    
    /**
     * Returns the path to the email preview file.
     *
     * @return string
     */
    private function path()
    {
        return $this->storage().'/'.$this->filename();
    }

    /**
     * Get the HTML content for the preview file.
     *
     * @return string
     */
    private function content()
    {
        return $this->info().$this->message->getBody();
    }

    /**
     * Set the Flare Debug Sent Message Filename into the Session
     *
     * @return void
     */
    private function setSession()
    {
        Session::put('flare.maildebug.sent', $this->filename());
    }

    /**
     * Does the Session have an instance of the Flare Debug Email.
     * 
     * @return boolean
     */
    private function hasSession()
    {
        return Session::has('flare.maildebug.sent');
    }

    /**
     * Pull the Flare Debug Sent Message Filename if it is set, from the Session.
     * 
     * @return mixed
     */
    private function getSession()
    {
        return Session::pull('flare.maildebug.sent', false);
    }

    /**
     * Return the Debug Mail Filename
     * 
     * @return string
     */
    private function filename()
    {
        $to = str_replace(['@', '.'], ['_at_', '_'], array_keys($this->message->getTo())[0]);

        return str_slug($this->message->getDate().'_'.$to.'_'.$this->message->getSubject(), '_').'.html';
    }

    /**
     * Generate a human readable HTML comment with message info.
     *
     * @return string
     */
    private function info()
    {
        return sprintf(
            "<!--\nFrom:%s, \nTo:%s, \nReply-to:%s, \nCC:%s, \nBCC:%s, \nSubject:%s\n-->\n",
            json_encode($this->message->getFrom()),
            json_encode($this->message->getTo()),
            json_encode($this->message->getReplyTo()),
            json_encode($this->message->getCc()),
            json_encode($this->message->getBcc()),
            $this->message->getSubject()
        );
    }

    /**
     * Create the preview directory if necessary.
     *
     * @return void
     */
    private function createPreviewDirectory()
    {
        if (! $this->files->exists($this->storage())) {
            $this->files->makeDirectory($this->storage());

            $this->files->put($this->storage().'/.gitignore', "*\n!.gitignore");
        }
    }

    /**
     * Delete previews older than the given life time configuration.
     *
     * @return void
     */
    private function cleanOldPreviews()
    {
        $oldPreviews = array_filter($this->files->files($this->storage()), function ($file) {
            return (time() - $this->files->lastModified($file)) > (Config::get('mail-debug.lifetime') * 60);
        });

        if ($oldPreviews) {
            $this->files->delete($oldPreviews);
        }
    }
}