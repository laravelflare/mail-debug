<?php

namespace LaravelFlare\MailDebug\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use LaravelFlare\MailDebug\MailDebugManager;

class MailDebug
{
    /**
     * MailDebugManager Instance
     * 
     * @var \LaravelFlare\MailDebug\MailDebugManager
     */
    protected $debug;

    /**
     * __construct
     * 
     * @param MailDebugManager $debug
     */
    public function __construct(MailDebugManager $debug)
    {
        $this->debug = $debug;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * 
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $response = $next($request);
        } catch (Exception $e) {
            $response = $this->handleException($request, $e);
        }

        if ($this->sentMail() && $response instanceof Response) {
            $this->appendToResponse($response);
        }

        return $response;
    }

    /**
     * Used to determine if the application sent an email
     * in the last request.
     * 
     * @return boolean
     */
    protected function sentMail()
    {
        return $this->debug->wasSent();
    }

    /**
     * Injects the Mail Debug Popup into the Response.
     *
     * @param \Illuminate\Http\Response
     */
    protected function appendToResponse(Response $response)
    {
        $existingContent = $response->getContent();
        $closeBodyPosition = strripos($existingContent, '</body>');
        $appendedContent = "
            <script>
                window.open('".route('mail-debug', ['file' => $this->debug->preview()])."','width=680,height=800,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
            </script>";

        if ($closeBodyPosition !== false) {
            $response->setContent(substr($existingContent, 0, $closeBodyPosition).$appendedContent.substr($existingContent, $closeBodyPosition));

            return;
        }

        $response->setContent($existingContent.$appendedContent);
    }
}
