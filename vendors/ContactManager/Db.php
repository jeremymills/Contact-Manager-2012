<?php
/**
 *
 */
namespace ContactManager
{
    /**
     * @ignore
     */
    defined('IN_APPLICATION') or exit;
    
    class Db
    {
        private static $instance = null;
        
        public static function instance(array $config = array())
        {
            if( null === self::$instance )
            {
                self::$instance = \Doctrine\DBAL\DriverManager::getConnection($config);
            }
            
            return self::$instance;
        }
        
        final private function __construct()
        {
            throw new \Exception('Cannot instantiate a static class.');
        }
        
        final private function __destruct()
        {
            throw new \Exception('Cannot instantiate a static class, therefore cannot destruct.');
        }
        
        final private function __clone()
        {
            throw new \Exception('Cannot instantiate a static class, therefore cannot clone.');
        }
    }
}