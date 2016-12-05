<?php

/*
 * Gezmo
 *
 * PHP 5.3+ / Zend Framework 2.+
 *
 * @category  PHP
 * @package   Gezmo
 * @author    Gezmo <gezmo@gezmo.info>
 * @copyright 2016 Gezmo
 * @link      http://www.gezmo.info
 */

return array(
    'router' => array(
        'routes' => array(
            'gimager-rest' => array(
				'type'    => 'Segment',
				'options' => array(
				'route'    => '/gimager-rest[/:id]',
					'constraints' => array(
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'GimagerRest\Controller\GimagerRest',
					),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'GimagerRest\Controller\GimagerRest' => 'GimagerRest\Controller\GimagerRestController',
        ),
    ),
    'view_manager' => array(
		'strategies' => array(
			'ViewJsonStrategy',
        ),
    ),
);
