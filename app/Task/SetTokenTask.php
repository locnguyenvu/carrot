<?php
namespace App\Task;

class SetTokenTask extends AbstractTask implements Task
{
    public function exec() : void
    {
        $line = readline("Token: ");
        
        $accessTokenFile = ROOT_PATH.'/'.getenv('TOKEN_FILE');
        // Clear
        file_put_contents($accessTokenFile, '');

        // Write
        file_put_contents($accessTokenFile, $line);
    }
}
