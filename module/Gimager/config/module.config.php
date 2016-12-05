<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Gimager\Controller\Gimager' => 'Gimager\Controller\GimagerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'gimager' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/gimager[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Gimager\Controller\Gimager',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'gimager' => __DIR__ . '/../view',
        ),
    ),
);