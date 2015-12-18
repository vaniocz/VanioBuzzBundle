<?php
namespace VanioBuzzBundle\Buzz;

use Buzz\Client\MultiCurl as BaseMultiCurl;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class MultiCurl extends BaseMultiCurl
{
    private $callback;

    /**
     * {@inheritDoc}
     */
    public function setOption($option, $value)
    {
        if ($option === 'callback') {
            $this->callback = $value;
        } else {
            parent::setOption($option, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestInterface $request, MessageInterface $response, array $options = [])
    {
        if ($this->callback) {
            $options += ['callback' => $this->callback];
        }

        parent::send($request, $response, $options);
    }
}
