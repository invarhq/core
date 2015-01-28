<?php
/** {license_text}  */ 
namespace Core\Support;

trait Singleton 
{
    static protected $instance;

    /**
     * Disable constructor
     */
    final protected function __construct()
    {
        
    }

    /**
     * @return static
     */
    static public function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        
        return self::$instance;
    }
}
