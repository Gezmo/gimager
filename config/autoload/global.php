<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overridding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Cache\Storage\Filesystem' => function($sm){
	            $cache = Zend\Cache\StorageFactory::factory(array(
	            'adapter' => 'filesystem',
	            'plugins' => array(
	                'exception_handler' => array('throw_exceptions' => false),
	                'serializer'
	            )
	            ));
	             
	            $cache->setOptions(array(
					'ttl'		=> 3600,	/* time to live in seconds */
					'namespace'	=> 'dbtable',
	                'cache_dir' => './data/cache'
	            ));
                return $cache;
	        },					
        ),
    ),
);
