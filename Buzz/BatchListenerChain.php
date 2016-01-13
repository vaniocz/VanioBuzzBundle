<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Listener\ListenerChain;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

/**
 * @method ListenerInterface[] getListeners
 */
class BatchListenerChain extends ListenerChain
{
    private $client;
    private $messages = [];
    private $numberOfRemainingResults = 0;

    /**
     * @param ListenerInterface[] $listeners
     */
    public function __construct(MultiCurl $client, array $listeners = [])
    {
        parent::__construct($listeners);
        $this->client = $client;
        $client->setOption('callback', [$this, 'onResult']);
    }

    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        $this->numberOfRemainingResults++;
    }

    /**
     * @internal
     */
    public function onResult(
        MultiCurl $client,
        RequestInterface $request,
        Response $response,
        array $options,
        int $result
    ) {
        $this->messages[] = [$request, $response, $result];

        if (--$this->numberOfRemainingResults) {
            return;
        }

        foreach ($this->messages as $messages) {
            list($request, $response, $result) = $messages;

            if ($result !== CURLE_OK) {
                $response->addHeader(sprintf('X-Curl-Error-Result: %d', $result));
            }
        }

        try {
            foreach ($this->messages as $messages) {
                list($request, $response) = $messages;

                foreach ($this->getListeners() as $listener) {
                    $listener->postSend($request, $response);
                }
            }
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $this->messages = [];
            $this->numberOfRemainingResults = 0;
        }
    }
}
