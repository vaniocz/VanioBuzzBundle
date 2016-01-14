<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Client\BatchClientInterface;
use Buzz\Listener\ListenerInterface;

interface BatchListener extends ListenerInterface
{
    public function preFlush(BatchClientInterface $client);

    public function postFlush(BatchClientInterface $client);
}
