<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Client\MultiCurl;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

interface MultiCurlListener extends ListenerInterface
{
    /**
     * @param int $error
     */
    public function onResult(MultiCurl $client, RequestInterface $request, Response $response, array $options, $error);
}
