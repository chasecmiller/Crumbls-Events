<?php


namespace Crumbls\Plugins\Events\Meta\Method;

use Crumbls\Plugins\Events\Meta;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class Log
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

        $type = get_post_meta($post->ID, 'method_type', true);
        if (!$type) {
            printf('<p>%s</p>', __('Before you may use this, you must define a payment gateway.', __NAMESPACE__));
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