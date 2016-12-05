<?php
namespace Gimager;

use Gimager\Model\Gimager;
use Gimager\Model\GimagerTable;
use Gimager\Model\ProcessQueue;
use Gimager\Model\ProcessQueueTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Gimager\Model\GimagerTable' =>  function($sm) {
                    $tableGateway = $sm->get('GimagerTableGateway');
                    $table = new GimagerTable($tableGateway);
                    return $table;
                },
                'GimagerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Gimager());
                    return new TableGateway('gimager', $dbAdapter, null, $resultSetPrototype);
                },
                'Gimager\Model\ProcessQueueTable' =>  function($sm) {
                    $tableGateway = $sm->get('ProcessQueueTableGateway');
                    $table = new ProcessQueueTable($tableGateway);
                    return $table;
                },
                'ProcessQueueTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProcessQueue());
                    return new TableGateway('process_queue', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}