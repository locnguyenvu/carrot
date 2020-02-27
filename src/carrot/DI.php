<?php
namespace Carrot;

use Carrot\Exception\{ServiceNotFoundException};

class DI
{
    private $services = [];
    private $alias = [];
    private $parameters = [];
    private $binds = [];
    private $serviceStore;
    private $serviceAliasMap = [];

    private $appConfigs = [];

    public function __construct()
    {
        $this->loadBootstrap();
    }

    private function loadBootstrap()
    {
        $appBootstrap = require_once(ROOT_PATH.'/bootstrap/app.php');
        $services = array_get($appBootstrap, 'services');

        foreach ($services as $alias => $construction) {
            $arguments = $construction['arguments'] ?? [];
            $reflector = new \ReflectionClass($construction['class']);
            $service = $this->create($construction['class'], $arguments);
            // $reflector->newInstanceArgs($arguments);
            
            $this->services[] = $alias;
            $this->serviceAliasMap[get_class($service)] = $alias;
            $this->serviceStore[$alias] = $service;
        }

        $this->appConfigs['console_namespace'] = array_get($appBootstrap, 'console_namespace');
    }

    public function get(string $name) {
        if (!$this->has($name)) {
            throw new ServiceNotFoundException();
        }
        return $this->serviceStore[$name];
    }

    public function has(string $name) {
        return in_array($name, $this->services);
    }

    public function create(string $className, array $arguments = null) {
        $reflector = new \ReflectionClass($className);
        if ($reflector->getConstructor() == null) { dump($className); die;}
        $constructorParams = $reflector->getConstructor()->getParameters();
        $dependencies = [];
        foreach ($constructorParams as $param) {
            $dependencies[] = $this->constructParam($param);
        }

        $instance = $reflector->newInstanceArgs($dependencies);

        return $instance;
    }

    private function constructParam(\ReflectionParameter $param) {
        $paramClassName = $param->getClass()->name;
        if (isset($this->serviceAliasMap[$paramClassName])) {
            $instance = $this->get($this->serviceAliasMap[$paramClassName]);
        } else {
            $instance = new $paramClassName;
        }
        return $instance;
    }

    public function getConfig(string $key) : string 
    {
        return $this->appConfigs[$key] ?? null;
    }
}
