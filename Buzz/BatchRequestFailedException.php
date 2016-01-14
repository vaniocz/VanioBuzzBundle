<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class BatchRequestFailedException extends RequestFailedException
{
    private $messages;

    /**
     * @param array $messages - array of [$request, $response]
     * @param string|null $errorMessage
     */
    public function __construct(array $messages, $errorMessage = null)
    {
        $this->messages = $messages;
        $errorMessages = [];

        if ($errorMessage === null) {
            foreach ($this->messages as $messages) {
                /** @var RequestInterface $request */
                /** @var Response $response */
                list($request, $response) = $messages;
                $curlError = $response->getHeader('X-Curl-Error-Result');
                $errorMessages[] = sprintf(
                    '%s "%s%s": %d - "%s"',
                    $request->getMethod(),
                    $request->getHost(),
                    $request->getResource(),
                    $curlError ?: $response->getStatusCode(),
                    $curlError ? curl_strerror($curlError) : $response->getReasonPhrase()
                );
            }

            $errorMessage = sprintf("Batch HTTP requests failed.\n - %s.", implode("\n - ", $errorMessages));
        }

        list($request, $response) = current($this->messages);
        parent::__construct($request, $response, $errorMessage);
    }

    /**
     * @return array of [$request, $response]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
