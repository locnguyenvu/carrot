<?php
namespace Carrot\Shared;

interface Entity
{
    public function hydrate(array $params) : void;
}