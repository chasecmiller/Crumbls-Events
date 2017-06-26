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
class Page
{
    protected static $_instance = NULL;
    /**
     * Prevent direct object creation
     */
    final private function __construct()
    {
        echo 'set it up and run it!';
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
    private static function adminPermissionCheck($key = null)
    {
        // check user capabilities
        global $submenu;
        if (!$key || !is_string($key)) {
            $key = array_key_exists('page', $_REQUEST) ? $_REQUEST['page'] : false;
        }
        if (!$key) {
            // We should end up throwing an error here instead.
            return 'manage_options';
        }
        $permission = array_filter($submenu['edit.php?post_type=event'], function ($e) use ($key) {
            return array_key_exists(2, $e) && is_string($e[2]) && $e[2] == $key;
        });
        if ($permission) {
            $permission = array_values($permission)[0][1];
        } else {
            $permission = 'manage_options';
        }
        return $permission;
    }

    public static function Display() {
        global $submenu;

        $instance = self::getInstance();

        $permission = self::adminPermissionCheck();

        if (!current_user_can($permission)) {
            return;
        }

        // Quick trick to get the page title.
        $title = array_values(array_filter($submenu['edit.php?post_type=event'], function ($e) {
            return array_key_exists(2, $e) && is_string($e[2]) && $e[2] == $_REQUEST['page'];
        }))[0][0];

        ?>
        <div class="wrap">
            <h1><?php echo esc_html($title); ?></h1>
        </div>
        <?php
   }

}