<?php
/*
 * PHP / Zend Framework 2
 *
 * @category  PHP
 * @package   gezmo
 * @author    Ge Zuidema <gezmo@gezmo.info>
 * @copyright 2016 gezmo
 * @link      http://www.gezmo.info
 */
namespace Application;

use Zend\Mvc\ModuleRouteListener;

class Module
{
    public function onBootstrap($e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
