<?php
/**
 * @author Jeremy Keeler <jeremykeeler@ku.edu>
 * @copyright 2017 The University of Kansas
 */

namespace ScholarWorks\StatsBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener
 * @package ScholarWorks\StatsBundle\EventListener
 *
 * This class captures thrown exceptions and returns a simple
 * page.
 */

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response = new Response();
        $response->setContent($exception->getMessage());

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}