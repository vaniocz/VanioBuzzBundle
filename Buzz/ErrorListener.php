<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Client\BatchClientInterface;
use Buzz\Client\MultiCurl;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class ErrorListener implements BatchListener, MultiCurlListener
{
    private $client;
    private $failedMessages = [];

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
        if ($response->isClientError() || $response->isServerError() || $response->getHeader('X-Curl-Error-Result')) {
            if ($this->client instanceof BatchClientInterface) {
                $this->failedMessages[] = [$request, $response];

                return;
            }

            throw new RequestFailedException($request, $response);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function preFlush(BatchClientInterface $client)
    {
        $this->client = $client;
        $this->failedMessages = [];
    }

    /**
     * {@inheritDoc}
     */
    public function postFlush(BatchClientInterface $client)
    {
        if ($this->failedMessages) {
            throw new BatchRequestFailedException($this->failedMessages);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onResult(MultiCurl $client, RequestInterface $request, Response $response, array $options, $error)
    {
        if ($error !== CURLE_OK) {
            $response->addHeader(sprintf('X-Curl-Error-Result: %d', $error));
        }
    }
}
