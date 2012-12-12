<?php

/**
 * @ignore
 */
defined('IN_APPLICATION') or exit;

/**
 * Configuration array of details
 */
return array(
    /**
     */
    'timezone' => 'America/Toronto',
    
    /**
     * Connection details for database connection using the
     * Doctrine library package.
     */
    'database' => array(
        'dbname'    => '',
        'user'      => '',
        'password'  => '',
        'host'      => 'localhost',
        'driver'    => 'pdo_mysql'
    )
    
); /* end array( */