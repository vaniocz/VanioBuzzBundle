<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Browser;
use Buzz\Client\BatchClientInterface;
use Buzz\Client\ClientInterface;
use Buzz\Client\MultiCurl;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

/**
 * @method BatchListenerChain|null getListener
 * @method ClientInterface|BatchClientInterface getClient
 */
class BatchBrowser extends Browser
{
    private $lastRequest;
    private $lastResponse;

    public function __construct(BatchClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client ?: new MultiCurl, $factory);
        $this->setListener(new BatchListenerChain);
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestInterface $request, MessageInterface $response = null)
    {
        if (!$response) {
            $response = $this->getMessageFactory()->createResponse();
        }

        if ($listener = $this->getListener()) {
            $listener->preSend($request);
        }

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->getClient()->send($request, $response, ['callback' => function (...$arguments) {
            if ($listener = $this->getListener()) {
                $listener->onResult(...$arguments);
            }
        }]);

        $this->lastRequest = $request;
        $this->lastResponse = $response;

        if ($listener = $this->getListener()) {
            $listener->postSend($request, $response);
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * {@inheritDoc}
     */
    public function setListener(ListenerInterface $listener)
    {
        if (!$listener instanceof BatchListenerChain) {
            $listener = new BatchListenerChain([$listener]);
        }

        parent::setListener($listener);
    }

    public function flush()
    {
        $client = $this->getClient();

        if (!$client instanceof BatchClientInterface) {
            throw new \InvalidArgumentException('Unable to flush Buzz browser. The client does not support it.');
        }

        if ($listener = $this->getListener()) {
            $listener->preFlush($client);
        }

        $client->flush();

        if ($listener = $this->getListener()) {
            $listener->postFlush($client);
        }
    }
}
