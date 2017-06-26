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
class Details
{
    use Meta;

    /**
     * Generic Display.  Must be overridden.
     * @param $post
     * @param null $args
     */
    public static function Display($post, $args = null)
    {
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

        $v = get_post_meta($post->ID, $field, true);
        if (!$v) {
            $v = [];
        }

        // DEBUG
        $v['ApiLoginId'] = 'a';
        $v['TransactionKey'] = 'b';
        $v['DeveloperMode'] = true;
        $v['LiveEndpoint'] = null;
        $v['DeveloperEndpoint'] = null;
        $v['DuplicateWindow'] = null;
        $v['TestMode'] = true;
        $v['Currency'] = null;

        try {
            $gateway = Omnipay::create($type);
            // Methods
            $methods = get_class_methods($gateway);
            if ($v) {
                foreach ($v as $k => $v) {
                    if ($v === null) {
                        continue;
                    }
                    $method = 'set' . $k;
                    if (!in_array($method, $methods)) {
                        continue;
                    }
                    $gateway->$method($v);
                }
            }
            $fields = $gateway->getParameters();
            foreach ($fields as $field => $val) {
                preg_match_all('/((?:^|[A-Z])[a-z]+)/', $field, $name);
                $name = __(ucwords(implode(' ', $name[1])), __NAMESPACE__);
                switch($field) {
                    default:
                        printf('<label for="%s">%s</label>',
                            esc_attr($field),
                            $name
                        );
                        printf('<input type="text" name="%s" value="%s" class="widefat" />',
                            esc_attr($field),
                            esc_attr($val)
                        );
                }
            }
        } catch (\Exception $e) {
        }
    }


    /**
     * Remove siblings from the add new screen until this one has been chosen.
     */
    public static function RemoveSiblings()
    {
    }

}