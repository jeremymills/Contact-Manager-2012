<?php
/**
 */
namespace ContactManager
{
    /**
     * @ignore
     */
    defined('IN_APPLICATION') or exit;
    
    /**
     * View
     */
    class View extends Object
    {
        /**
         * @var array Static member variable containing global view variables.
         */
        protected static $_global_data = array();
        
        /**
         * factory
         *
         * Factory method for creating a new View instance.
         *
         * @access public
         * @param string Contains the absolute file path to the view file.
         * @param array Contains an array of data to initialize this view with.
         * @return View Returns the newly created instance.
         */
        public static function factory($file = '', array $data = array())
        {
            return new View($file, $data);
        }
        
        /**
         * set_global
         *
         * Setter method for adding/setting a new global view variable.
         *
         * @access public
         * @param mixed Contains the name of the variable usable within the view file. If an array, contains an array of
         *              variables to add to the global view scope.
         * @param mixed Contains the value of the variable. Should be null (ignored) if var is an array.
         * @return void
         */
        public static function set_global($var, $value = null)
        {
            if( is_array($var) )
            {
                foreach( $var as $v => $val )
                {
                    self::$_global_data[$v] = $val;
                }
            }
            else
            {
                self::$_global_data[$var] = $value;
            }
        }
        
        /**
         * get_global
         *
         * Accessor method for a global view variable.
         *
         * @access public
         * @param string Contains the variable to get.
         * @param mixed Contains a default value to return if variable does not exist.
         * @return mixed
         */
        public static function get_global($var, $default = null)
        {
            return isset(self::$_global_data[$var]) ? self::$_global_data[$var] : $default;
        }
        
        /**
         * global_exists
         *
         * Checks if a global variable exists.
         *
         * @access public
         * @param string Contains the global variable name to check.
         * @return bool Returns true if exists, otherwise false.
         */
        public static function global_exists($var)
        {
            return isset(self::$_global_data[$var]);
        }
        
        /**
         * remove_global
         *
         * Removes a global view variable.
         *
         * @access public
         * @param string Contains the name of the global view variable to remove.
         * @return bool Returns true if removed, otherwise false.
         */
        public static function remove_global($var)
        {
            if( isset(self::$_global_data[$var]) )
            {
                unset(self::$_global_data[$var]);
                return true;
            }
            
            return false;
        }
        
        /**
         * @var string Contains the rendered contents
         */
        private $_contents = null;
        
        /**
         * @var bool Is true if view has been rendered, otherwise false.
         */
        private $_has_rendered = false;
        
        /**
         * @var bool Is true if view is currently being rendered, otherwise false.
         */
        private $_is_rendering = false;
        
        /**
         * @var string Contains the view template file path.
         */
        protected $_tmpl_path;
        
        /**
         * @var string Contains the view template file name.
         */
        protected $_tmpl_file;
        
        /**
         * @var array Contains the variables for this view.
         */
        protected $_data = array();
        
        /**
         * __construct
         *
         * Creates a new instance of the View class.
         *
         * @access public
         * @param string Contains the absolute file path to the view template file.
         * @param array Contains an array of variables to pre-populate view with.
         */
        public function __construct($file = '', array $data = null)
        {
            if( !empty($file) )
            {
                $this->template_path(dirname($file));
                $this->template_file(basename($file));
            }
            
            if( !empty($data) )
            {
                $this->set($data);
            }
        }
        
        /**
         * set
         *
         * Setter for a view instance variable.
         *
         * @access public
         * @param string Contains the view variable name. May contain an array of view variables to assign.
         * @param mixed Contains the view variable value. If $var is array, this is ignored.
         * @return void
         */
        public function set($var, $value = null)
        {
            if( is_array($var) )
            {
                foreach( $var as $v => $val )
                {
                    $this->_data[$v] = $val;
                }
            }
            else
            {
                $this->_data[$var] = $value;
            }
        }
        
        /**
         * get
         *
         * Accessor method for a view instance variable.
         *
         * @access public
         * @param string Contains the view variable name to get.
         * @param mixed Contains a default value to return if variable does not exist.
         * @return mixed
         */
        public function get($var, $default = null)
        {
            return isset($this->_data[$var]) ? $this->_data[$var] : $default;
        }
        
        /**
         * exists
         *
         * Checks if a view instance variable exists.
         *
         * @access public
         * @param string Contains the view variable name to get.
         * @return bool Returns true if exists, otherwise false.
         */
        public function exists($var)
        {
            return isset($this->_data[$var]);
        }
        
        /**
         * remove
         *
         * Removes a view instance variable.
         *
         * @access public
         * @param string Contains the name of the variable to remove.
         * @return bool Returns true if successful, else false.
         */
        public function remove($var)
        {
            if( isset($this->_data[$var]) )
            {
                unset($this->_data[$var]);
                return true;
            }
            
            return false;
        }
        
        /**
         * has_rendered
         *
         * Checks if this view instance has already been previously rendered.
         *
         * @access public
         * @param void
         * @return bool
         */
        final public function has_rendered()
        {
            return $this->_has_rendered;
        }
        
        /**
         * is_rendering
         *
         * Checks if this view instance is currently being rendered.
         *
         * @access public
         * @param void
         * @return bool
         */
        final public function is_rendering()
        {
            return $this->_is_rendering;
        }
        
        /**
         * template_path
         *
         * Accessor/setter for the template view file path.
         *
         * @access public
         * @param string Contains the view file path to set for this instance. If null, method acts as a getter.
         * @return mixed Returns the set path if acting as a getter, otherwise this instance.
         */
        final public function template_path($path = null)
        {
            if( null === $path ) {
                return $this->_tmpl_path;
            }
            
            $this->_tmpl_path = $path;
            $this->_tmpl_path = rtrim($this->_tmpl_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            
            return $this;
        }
        
        /**
         * template_file
         *
         * Accessor/setter for the template view file name.
         *
         * @access public
         * @param string Contains the view file name to set for this instance. If null, method acts as a getter.
         * @return mixed Returns the set name if acting as a getter, otherwise this instance.
         */
        final public function template_file($file = null)
        {
            if( null === $file )
            {
                return $this->_tmpl_file;
            }
            
            $this->_tmpl_file = $file;
            return $this;
        }
        
        /**
         * contents 
         *
         * Accessor for the rendered contents of this view instance.
         *
         * @access public
         * @param bool If true, will render if contents are empty.
         * @return string Returns the rendered contents.
         */
        final public function contents($render = false) {
            if( $render && !$this->_contents )
            {
                $this->render(true);
            }
            
            return $this->_contents;
        }
        
        /**
         * render
         *
         * Renders this view instance.
         *
         * @access public
         * @param bool Used to determine if the contents should be returned. If true, contents are returned.
         * @return mixed
         */
        final public function render($return = true)
        {
            /* Has the view been rendered? If so, handle the return or print of them */
            if( $this->has_rendered() )
            {
                if( !$return )
                {
                    print $this->_contents;
                    return;
                }
                
                return $this->_contents;
            }
            
            /* Do not allow for recursive rendering of this view instance */
            if( $this->is_rendering() )
            {
                throw new \Exception('View is currently being rendered and you are not allowed to call View::render() in a recursive manner.');
            }
            
            $this->_is_rendering = true;
            $this->_data['view'] = $this;
            
            /* Run the beforeRender hook */
            $this->beforeRender();
            
            try
            {
                /* Render the view file within the static render_view method */
                $this->_contents = self::render_view($this->compile_path(), $this->_data);
            }
            catch( \Exception $exception )
            {
                $this->_has_rendered = true;
                $this->_is_rendering = false;
                if( !$return )
                {
                    throw $exception;
                }
                
                $this->afterRender(true, $exception);
                return (string) $exception;
            }
            
            /* Run the afterRender hook */
            $this->afterRender();
            
            $this->_has_rendered = true;
            $this->_is_rendering = false;
            
            if( !$return )
            {
                print $this->_contents;
                return;
            }
            
            return $this->_contents;
        }
        
        /**
         * @see Object::tostring
         */
        final public function toString()
        {
            return $this->contents(true);
        }
        
        /**
         * __set 
         *
         * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.set
         */
        final public function __set($key, $value)
        {
            $this->set($key, $value);
        }
        
        /**
         * __get
         *
         * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.get
         */
        final public function __get($key)
        {
            return $this->get($key);
        }
        
        /**
         * __isset
         * 
         * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.isset
         */
        final public function __isset($key)
        {
            return $this->exists($key);
        }
        
        /**
         * __unset 
         *
         * @see http://www.php.net/manual/en/language.oop5.overloading.php#object.unset
         */
        final public function __unset($key)
        {
            return $this->remove($key);
        }
        
        /**
         * beforeRender
         *
         * @access protected
         * @return void
         */
        protected function beforeRender()
        {
            /* void */
        }
        
        /**
         * afterRender
         *
         * @access protected
         * @param bool If true, during rendering an exception was caught.
         * @param \Exception Contains an instance of the exception caught during rendering, otherwise null.
         * @return void
         */
        protected function afterRender($has_exception = false, \Exception $exception = null)
        {
            /* void */
        }
        
        /**
         * compile_path
         *
         * @access protected
         * @return string Contains the compiled path ready to use for rendering
         */
        protected function compile_path()
        {
            return $this->_tmpl_path . $this->_tmpl_file;
        }
        
        /**
         * render_view
         *
         * Handles the rendering of the view file.
         *
         * @access private
         * @param string Contains the absolute view file path that should be rendered.
         * @param array Contains the instance view variables for this view that should be made available.
         * @return string Returns the rendered view file.
         * @throws \Exception Throws any exception caught.
         */
        final private static function render_view($view_filename, array $view_data)
        {
            extract($view_data, EXTR_SKIP);
            extract(self::$_global_data, EXTR_SKIP | EXTR_REFS);
            
            ob_start();
            
            try
            {
                include $view_filename;
            }
            catch( \Exception $exception )
            {
                ob_end_clean();
                
                throw $exception;
            }
            
            return ob_get_clean();
        }
    }
}