<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Client\BatchClientInterface;
use Buzz\Client\MultiCurl;
use Buzz\Listener\ListenerChain;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

/**
 * @method ListenerInterface[] getListeners
 */
class BatchListenerChain extends ListenerChain implements BatchListener, MultiCurlListener
{
    private $messages = [];

    /** @var BatchListener[] */
    private $batchListeners = [];

    /** @var MultiCurlListener[] */
    private $multiCurlListeners = [];

    /**
     * {@inheritDoc}
     */
    public function addListener(ListenerInterface $listener)
    {
        parent::addListener($listener);

        if ($listener instanceof BatchListener) {
            $this->batchListeners[] = $listener;
        }

        if ($listener instanceof MultiCurlListener) {
            $this->multiCurlListeners[] = $listener;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $this->messages[] = [$request, $response];
    }

    /**
     * {@inheritDoc}
     */
    public function preFlush(BatchClientInterface $client)
    {
        foreach ($this->batchListeners as $batchListener) {
            $batchListener->preFlush($client);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postFlush(BatchClientInterface $client)
    {
        foreach ($this->messages as $messages) {
            list($request, $response) = $messages;

            foreach ($this->getListeners() as $listener) {
                $listener->postSend($request, $response);
            }
        }

        foreach ($this->batchListeners as $batchListener) {
            $batchListener->postFlush($client);
        }

        $this->messages = [];
    }

    /**
     * {@inheritDoc}
     */
    public function onResult(MultiCurl $client, RequestInterface $request, Response $response, array $options, $error)
    {
        foreach ($this->multiCurlListeners as $multiCurlListener) {
            $multiCurlListener->onResult($client, $request, $response, $options, $error);
        }
    }
}
