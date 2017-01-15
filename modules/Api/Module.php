<?php
namespace App\Api;

use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $di = null) {}
    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices(\Phalcon\DiInterface $di)
    {
        $config = include __DIR__ . "/Config/config.php";
        $di['config'] = $config;
        include __DIR__ . "/Config/services.php";
    }
}
?>