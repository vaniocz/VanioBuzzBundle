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
     * @param int|null $clientErrorCode
     * @param string|null $clientErrorMessage
     */
    public function postSend(
        RequestInterface $request,
        MessageInterface $response,
        $clientErrorCode = null,
        $clientErrorMessage = null
    ) {
        $curlErrorResult = $response->getHeader('X-Curl-Error-Result');

        if ($response->isClientError() || $response->isServerError() || $curlErrorResult) {
            throw new RequestFailedException(sprintf(
                'HTTP %s request to "%s%s" failed: %d - %s.',
                $request->getMethod(),
                $request->getHost(),
                $request->getResource(),
                $curlErrorResult ?: $response->getStatusCode(),
                $curlErrorResult
                    ? 'see http://curl.haxx.se/libcurl/c/libcurl-errors.html'
                    : $response->getReasonPhrase()
            ));
        }
    }
}
