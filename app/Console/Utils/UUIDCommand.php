<?php
namespace App\Console\Utils;

use Ramsey\Uuid\Uuid;

class UuidCommand extends \Carrot\Console\Command
{

    protected static $pattern = 'utils:uuid';

    public function exec()
    {
        $uuid = Uuid::uuid4();
        echo $uuid->toString();
    }

}
