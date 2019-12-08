<?php
namespace Carrot\Shared;

abstract class EntityCollection extends Collection
{
    abstract function getEntityClass() : string;

    public function hydrate(array $dataArray) 
    {
        $entityClass = $this->getEntityClass();
        foreach ($dataArray as $data) {
            $entityInstance = new $entityClass;
            $entityInstance->hydrate($data);
            $this->append($entityInstance);
        }
    }
}