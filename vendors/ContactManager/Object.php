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
    
    /**
     *
     */
    class Object
    {
        final public function __toString()
        {
            return $this->toString();
        }
        
        public function toString()
        {
            return sprintf('%s [ %s ]', $this->getClassName(), $this->getObjectHash());
        }
        
        final public function getClassName()
        {
            return get_class($this);
        }
        
        final public function getObjectHash()
        {
            return spl_object_hash($this);
        }
    }
}