<?php
namespace Vanio\BuzzBundle\Buzz;

use Buzz\Browser as BaseBrowser;
use Buzz\Client\BatchClientInterface;

class Browser extends BaseBrowser
{
    public function flush()
    {
        $client = $this->getClient();

        if ($client instanceof BatchClientInterface) {
            $client->flush();
        }
    }
}
