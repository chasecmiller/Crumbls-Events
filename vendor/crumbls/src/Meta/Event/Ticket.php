<?php


namespace Crumbls\Plugins\Events\Meta\Event;

use Crumbls\Plugins\Events\Meta;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class Ticket
{
    use Meta;


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

        $v = get_post_meta($post->ID, $field);

        foreach($v as $row) {
            print_r($row);
        }

//        printf('<p>Ticket Selector?</p>')
        echo get_class($instance).'\\'.__FUNCTION__;
    }
}