<?php
namespace Tikivn\Authentication;

class AccessToken
{
    private $jwt = '';
    private $storeFile;

    public function __construct()
    {
        $this->storeFile = ROOT_PATH.'/'.getenv('TOKEN_FILE');

        $this->jwt = file_get_contents($this->storeFile);
    }

    public function getJwt() : string 
    {
        return $this->jwt;
    }

    public function setJwt(string $jwt) : void 
    {
        $this->jwt = $jwt;
        // Clear
        file_put_contents($this->storeFile , '');

        // Write
        file_put_contents($this->storeFile, $jwt);
    }
}