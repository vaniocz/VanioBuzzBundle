<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class RequestFailedException extends \RuntimeException
{
    private $request;
    private $response;

    /**
     * @param string|null $message
     */
    public function __construct(RequestInterface $request, Response $response, $message = null)
    {
        $this->request = $request;
        $this->response = $response;

        if ($message === null) {
            $curlError = $response->getHeader('X-Curl-Error-Result');
            $message = sprintf(
                'HTTP %s request to "%s%s" failed: %d - "%s".',
                $request->getMethod(),
                $request->getHost(),
                $request->getResource(),
                $curlError ?: $response->getStatusCode(),
                $curlError ? curl_strerror($curlError) : $response->getReasonPhrase()
            );
        }

        parent::__construct($message);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
