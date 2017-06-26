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
class Location
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

        if (!$v = get_post_meta($post->ID, 'event_location', true)) {
            $v = false;
        }

        $possible = new \WP_Query([
            'post_type' => 'event_location',
            'post_status' => 'publish',
            'ignore_sticky' => true,
            'order' => 'post_title',
            'order' => 'asc'
        ]);

        if (!$possible->have_posts()) {
            // Maybe add in a dynamic creation here.
            printf('<p>%s</p>', __('Please setup a location.', __NAMESPACE__));
            return;
        }

        printf('<select name="%s" class="widefat">', self::$field);

        printf('<option value="false">%s</option>', __('None', __NAMESPACE__));

        while ($possible->have_posts()) {
            $possible->the_post();
            printf('<option value="%d"%s>%s</option>',
                get_the_ID(),
                $v == get_the_ID() ? ' selected' : '',
                htmlspecialchars(get_the_title())
            );
        }
        wp_reset_postdata();

        echo '</select>';

    }


    /**
     * Save handler
     */
    public static function Save($post_id)
    {
        $instance = self::getInstance();
        self::getField();
        if (
            array_key_exists(self::$field, $_POST)
        &&
            is_numeric($_POST[self::$field])
            &&
            $temp = get_post($_POST[self::$field])
        ) {
            if ($temp->post_type == 'event_location') {
                update_post_meta( $post_id, self::$field, $temp->ID );
                return true;
            }
        }
        delete_post_meta($post_id, self::$field);
        return true;
    }
}