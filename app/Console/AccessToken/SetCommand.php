<?php
namespace App\Console\AccessToken;

use Carrot\Console\Command;
use Carrot\Console\Input\InputInterface;

class SetCommand extends Command {

    protected static $pattern = 'accessToken:set {token}';

    public function exec()
    {
        $accessToken = $this->app->getService('access_token');
        $accessToken->setJwt($jwt);
    }

}