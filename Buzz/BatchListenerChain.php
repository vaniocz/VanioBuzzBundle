<?php
namespace VanioBuzzBundle\Buzz;

use Buzz\Listener\ListenerChain;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

/**
 * @method ListenerInterface[] getListeners
 */
class BatchListenerChain extends ListenerChain
{
    private $client;
    private $messages = [];

    /**
     * @param ListenerInterface[] $listeners
     */
    public function __construct(MultiCurl $client, array $listeners = [])
    {
        parent::__construct($listeners);
        $this->client = $client;
        $client->setOption('callback', [$this, 'postFlush']);
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $this->messages[] = [$request, $response];
    }

    /**
     * @internal
     */
    public function postFlush()
    {
        foreach ($this->messages as $messages) {
            list($request, $response) = $messages;

            foreach ($this->getListeners() as $listener) {
                $listener->postSend($request, $response);
            }
        }

        $this->messages = [];
    }
}
