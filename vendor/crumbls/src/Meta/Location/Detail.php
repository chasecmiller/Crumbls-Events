<?php

namespace Crumbls\Plugins\Events\Meta\Location;

use Crumbls\Plugins\Events\Meta;

/**
 *  Map field for WordPress custom post type
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class Detail
{
    use Meta;

    /**
     * Display a map with location fields.
     * @param $post
     * @param null $args
     */
    public static function Display($post, $args = null) {
        self::$field = $args['id'];

        $permission = self::adminPermissionCheck();

        if (!current_user_can($permission)) {
            return;
        }

        $v = get_post_meta($post->ID);

        if (array_key_exists('website', $v)) {
            $v['website'] = array_filter($v['website']);
            $v['website'] = array_values($v['website'])[0];
        } else {
            $v['website'] = '';
        }


        if (array_key_exists('phone', $v)) {
            $v['phone'] = array_filter($v['phone']);
            $v['phone'] = array_values($v['phone'])[0];
        } else {
            $v['phone'] = '';
        }


        if (array_key_exists('capacity', $v)) {
            $v['capacity'] = array_filter($v['capacity']);
            $v['capacity'] = array_values($v['capacity'])[0];
        } else {
            $v['capacity'] = '';
        }

        printf('<label><input type="text" name="website" id="website" value="%s" /> %s</label>',
            esc_attr($v['website']),
            __('Website', __NAMESPACE__)
        );


        printf('<label><input type="text" name="phone" id="phone" value="%s" /> %s</label>',
            esc_attr($v['phone']),
            __('Phone NUmber', __NAMESPACE__)
        );


        printf('<label><input type="text" name="capacity" id="capacity" value="%s" /> %s</label>',
            esc_attr($v['capacity']),
            __('Capacity', __NAMESPACE__)
        );


        print_r($v);

        echo __METHOD__;
    }

}