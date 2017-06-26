<?php


namespace Crumbls\Plugins\Events\Meta\Method;

use Crumbls\Plugins\Events\Meta;
use Omnipay\Omnipay;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class Type
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

        $v = get_post_meta($post->ID, $field, true);

        if ($v) {
            $v = str_replace('_', ' ', $v);
            printf('<p>%s %s.</p>', __('This payment gateway is set to', __NAMESPACE__), $v);
            printf('<small class="italic">%s</small>', __('This may not be changed.', __NAMESPACE__));
        } else {
            printf('<p>%s</p>', __('Please select a payment gateway from the list below.', __NAMESPACE__));
            printf('<select name="%s">', $field);

            $gateway = Omnipay::getFactory();
            foreach($gateway->getSupportedGateways() as $row) {
                printf('<option value="%s">%s</option>',
                    esc_attr($row),
                    str_replace('_', ' ', $row)
                );
            }
            echo '</select>';
        }
    }

    /**
     * Save handler
     */
    public static function Save($post_id) {
        $instance = self::getInstance();
        self::getField();

        if (!array_key_exists($instance::$field, $_POST)) {
            return false;
        }

        $gateway = Omnipay::getFactory();
        $supported = $gateway->getSupportedGateways();
        $requested = $_POST[$instance::$field];
        $valid = in_array($requested, $supported);
        if (!$valid) {
            return false;
        }
        update_post_meta($post_id, $instance::$field, $requested);
    }

}