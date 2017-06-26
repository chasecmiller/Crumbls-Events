<?php

namespace Crumbls\Plugins\Events;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
trait Meta
{
    protected static $field = null;
    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    final private function __construct()
    {
//        echo 'set it up and run it!';
    }

    /**
     * Prevent object cloning
     */
    final private function __clone()
    {
    }

    /**
     * Returns new or existing Singleton instance
     * @return Singleton
     */
    public static function getInstance()
    {
        if (null !== static::$_instance) {
            return static::$_instance;
        }
        static::$_instance = new static();

        return static::$_instance;
    }


    /**
     * Check permission for current admin page.
     * @param null $key
     * @return string
     */
    protected static function adminPermissionCheck($key = null)
    {
        // check user capabilities
        global $wp_meta_boxes;
        // This needs to be built.
        $permission = false;
        if ($permission) {
            $permission = array_values($permission)[0][1];
        } else {
            $permission = 'manage_options';
        }
        return $permission;
    }

    /**
     * Generic Display.  Must be overridden.
     * @param $post
     * @param null $args
     */
    public static function Display($post, $args = null) {
        $instance = self::getInstance();
        $field = self::getField();

        $permission = self::adminPermissionCheck();

        if (!current_user_can($permission)) {
            return;
        }
        echo get_class($instance).'\\'.__FUNCTION__;
   }

    /**
     * Get field name
     */
    public static function getField() {
        $instance = self::getInstance();
        if (!$instance::$field) {
            // Setup field
            preg_match('#^.*?_Meta_(.*?)$#', str_replace('\\', '_', __CLASS__), $instance::$field);
            $instance::$field = strtolower($instance::$field[1]);
        }
        return $instance::$field;
    }


    /**
     * Save handler
     */
    public static function Save($post_id) {
        $instance = self::getInstance();
        self::getField();
        return;
        echo $instance::$field;
        exit;
        // Check for save data
        return;
        print_r($_POST);
        exit;
        $instance::$field = $args['id'];

        echo __METHOD__;
        exit;
        $instance = self::getInstance();
        echo $instance::$field;
        exit;
    }
}