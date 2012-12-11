<?php
/**
 *
 *
 */

namespace ContactManager
{
    /**
     * @ignore
     */
    defined('IN_APPLICATION') or exit;
    
    /**
     * SplLoader
     *
     */
    class SplLoader
    {
        protected $_include_directories = array();
        protected $_loaded_items = array();
        
        protected $_dir_sep;
        protected $_php_ext;
        protected $_ns_sep;
        
        /**
         * Initializes the constructor
         *
         */
        public function __construct(array $include_directories = array(), $auto_register = true)
        {
            $this->_dir_sep = DIRECTORY_SEPARATOR;
            $this->_php_ext = '.php';
            $this->_ns_sep  = '\\';
            
            foreach( $include_directories as $inc_dir )
            {
                $this->addIncludeDirectory($inc_dir);
            }
            
            if( $auto_register )
            {
                $this->register();
            }
        }
        
        public function register()
        {
            spl_autoload_register(array($this, 'load'));
        }
        
        public function unregister()
        {
            spl_autoload_unregister(array($this, 'load'));
        }
        
        public function addIncludeDirectory($directory, $package = null)
        {
            $directory = rtrim($directory, DS) . DS;
            if( !in_array($directory, $this->_include_directories) )
            {
                $this->_include_directories[] = $directory;
                return $this;
            }
            
            return false;
        }
        
        public function addIncludeDirectories(array $directories)
        {
            foreach( $directories as $directory )
            {
                $this->addIncludeDirectory($directory);
            }
            
            return $this;
        }
        
        public function removeIncludeDirectory($directory)
        {
            if( false !== ($exists = array_search($directory, $this->_include_directories)) )
            {
                unset($this->_include_directories[$exists]);
                return true;
            }
            
            return false;
        }
        
        public function includeDirectoryExists($directory)
        {
            return in_array($directory, $this->_include_directories);
        }
        
        public function load($class_name)
        {
            if( in_array($class_name, $this->_loaded_items) )
            {
                return true;
            }
            
            if( false !== strpos($class_name, $this->_ns_sep) )
            {
                $pieces = explode($this->_ns_sep, $class_name);
                $class_name = array_pop($pieces);
                
                $pieces_size = count($pieces);
                $pieces_string = implode($this->_dir_sep, $pieces);
                
                foreach( $this->_include_directories as $lookup_path )
                {
                    $dir_path = $lookup_path . $this->_dir_sep . $pieces_string . $this->_dir_sep;
                    if( !is_dir($dir_path) )
                    {
                        continue;
                    }
                    
                    $filepath = $dir_path . $class_name . $this->_php_ext;
                    if( $this->_loadItem($class_name, $filepath) )
                    {
                        return true;
                    }
                    else
                    {
                        $filepath = $dir_path . $class_name . $this->_dir_sep . $class_name . $this->_php_ext;
                        if( $this->_loadItem($class_name, $filepath) )
                        {
                            return true;
                        }
                    }
                }
            }
            else
            {
                foreach( $this->_include_directories as $lookup_path )
                {
                    $filepath = $lookup . $this->_dir_sep . $class_name . $this->_php_ext;
                    if( $this->_loadItem($class_name, $filepath) )
                    {
                        return true;
                    }
                    else
                    {
                        $filepath = $lookup_path . $this->_dir_sep . $class_name . $this->_dir_sep . $class_name . $this->_php_ext;
                        if( $this->_loadItem($class_name, $filepath) )
                        {
                            return true;
                        }
                    }
                }
            }
            
            return false;
        }
        
        /**
         * Handles the loading of a single class based on the file path that was found.
         *
         * @access protected
         * @param string Contains the name of the class to load.
         * @param string Contains the absolute file path that has been found to be included for this class.
         * @return boolean
         */
        protected function _loadItem($class_name, $filepath)
        {
            if( file_exists($filepath) && is_file($filepath) )
            {
                require_once $filepath;
                $this->_loaded_items[] = $class_name;
                
                return true;
            }
            
            return false;
        }
    }
}