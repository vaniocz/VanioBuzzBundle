<?php
namespace DoucovaniUserBundle\Buzz;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Nette\Utils\Json;
use Nette\Utils\Strings;

class JsonListener implements ListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function preSend(RequestInterface $request)
    {
        $content = $request->getContent();

        if (is_array($content) || $content instanceof \JsonSerializable) {
            $request->setContent(Json::encode($content));
            $request->addHeader('Content-Type: application/json');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        if (Strings::endsWith($response->getHeader('Content-Type'), '/json')) {
            $response->setContent(Json::decode($response->getContent(), Json::FORCE_ARRAY));
        }
    }
}
