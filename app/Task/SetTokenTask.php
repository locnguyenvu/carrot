<?php
namespace App\Task;

class SetTokenTask implements Task
{
    public function exec() : void
    {
        $line = readline("Token: ");
        
        $accessTokenFile = ROOT_PATH.'/access_token';
        // Clear
        file_put_contents($accessTokenFile, '');

        // Write
        file_put_contents($accessTokenFile, $line);
    }
}