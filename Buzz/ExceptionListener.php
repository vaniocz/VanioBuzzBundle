<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class ExceptionListener implements ListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function preSend(RequestInterface $request)
    {}

    /**
     * {@inheritDoc}
     * @param Response $response
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        if ($response->isClientError() || $response->isServerError()) {
            throw new RequestFailedException(sprintf(
                'HTTP %s request to "%s%s" failed: %d - %s.',
                $request->getMethod(),
                $request->getHost(),
                $request->getResource(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }
    }
}
