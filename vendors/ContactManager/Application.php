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
    
    class Application
    {
        private static $_init_config = array();
        private static $_config = array();
        private static $_db = null;
        
        private static $_has_init = false;
        
        public static function init(array $init_config = array())
        {
            if( self::$_has_init )
            {
                return;
            }
            
            self::$_init_config = $init_config;
            
            if( !isset(self::$_init_config['config_file']) )
            {
                throw new \Exception('Configuration file parameter within the initializing config array must be specified at key "config_file".');
            }
            
            register_shutdown_function(__CLASS__ . '::shutdown');
            
            set_error_handler(__CLASS__ . '::error_handler');
            
            set_exception_handler(__CLASS__ . '::exception_handler');
            
            self::$_config = require_once self::$_init_config['config_file'];
            
            if( self::$_config['timezone'] )
            {
                date_default_timezone_set(self::$_config['timezone']);
            }
            
            self::$_db = Db::instance(self::$_config['database']);
            
            self::$_has_init = true;
        }
        
        public static function shutdown()
        {
            
        }
        
        public static function error_handler($errno, $errstr, $errfile, $errline, array $context = array())
        {
            print_r( func_get_args() );
            exit;
        }
        
        public static function exception_handler(\Exception $exception)
        {
            print_r( $exception );
            exit;
        }
        
        public static function config($key = null)
        {
            if( null !== $key )
            {
                return isset(self::$_config[$key]) ? self::$_config[$key] : null;
            }
            
            return self::$_config;
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