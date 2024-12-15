<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event) : void
    {
        $exception = $event->getThrowable();

        //TODO: check exception type and return appropriate response
        //TODO: use template engine instead HEREDOC
        //TODO send exception to the custom logger
        //TODO: create fix command to fix the error (set ENV variable, setup database, etc)

        $message = sprintf(
<<<END
<!DOCTYPE html>
<html lang="%s">
<head>
    <meta charset="UTF-8" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>Something was broken: Internal Server Error</title>
    <style>
        body { background-color: #fff; color: #222; font: 12pt/1.5 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; }
        .container { margin: 30px; max-width: 600px; }
        h1 { color: grey; font-size: 24pt; }
        h2 { font-size: 16pt; }
    </style>
</head>
<body>
<div class="container">
    <h1>Something was broken</h1>
    <h2>The server returned a "%s Internal Server Error".</h2>
    <p>
       %s
    </p>
    <p><a href="/create-issue">Create an issue</a></p>
</div>
</body>
</html>
END
        , 'en', $exception->getCode(), $exception->getMessage());

        $response = new Response();
        $response->setContent($message);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
