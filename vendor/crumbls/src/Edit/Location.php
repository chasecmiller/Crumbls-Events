<?php

namespace Crumbls\Plugins\Events\Edit;

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
    protected static $_class = __CLASS__;
    private static $_option = false;
    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    public function __construct()
    {
        add_filter('manage_edit-event_location_columns', [__CLASS__, 'columnDefinition']);
        add_action('manage_event_location_posts_custom_column', [__CLASS__, 'columnData'], 10, 2);

        // Register Post Status Pending
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
     * Define columns
     * @param $columns
     * @return array
     */
    public static function columnDefinition($columns)
    {
        unset($columns['cb']);
        $columns = [
            'ID' => __('ID', __NAMESPACE__),
            'post_title' => __('Name', __NAMESPACE__),
            'location' => __('Location', __NAMESPACE__),
            'capacity' => __('Capacity', __NAMESPACE__)
        ];
        return $columns;
    }

    /**
     * Get column data
     * @param $column
     * @param $post_id
     */
    public static function columnData($column, $post_id)
    {
        $method = '_columnData' . str_replace(' ', '', ucwords(str_replace('_', ' ', $column)));
        if (method_exists(__CLASS__, $method)) {
            call_user_func([__CLASS__, $method], $column, $post_id);
            return;
        }
        echo $column;
    }

    /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataID($column, $post_id)
    {
        printf('<a href="%s" title="%s %d">%d</a>',
            get_edit_post_link($post_id),
            __('View transaction', __NAMESPACE__),
            $post_id,
            $post_id
        );
    }

    /**
     * Show transaction date column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataPostTitle($column, $post_id)
    {
        $temp = get_post_type_object('event_location');
        printf('<a href="%s" title="%s %d">%s</a>',
            get_edit_post_link($post_id),
            $temp->labels->view_item,
            $post_id,
            get_the_title($post_id)
        );
        return;


        global $post, $wp_post_statuses;
        printf('<a href="%s" title="%s %d">%s</a>',
            get_edit_post_link($post_id),
            __('View transaction', __NAMESPACE__),
            $post_id,
            get_the_date('F jS, Y g:ia', $post_id)
        );
        echo '<br />';
        $s = get_post_status($post_id);
        $s = $wp_post_statuses[$s];
        echo $s->label;
        return;
        print_r($s);
        echo 'paid in full, pending, etc.';
    }

    /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataLocation($column, $post_id)
    {
        // How to set currency level?
        $v = get_post_meta($post_id);
        print_r($v);
        $v = get_post_meta($post_id, 'total');
        if (!$v) {
        } else {
            print_r($v);
            return;
        }
        echo __METHOD__;
        return;
        printf('<a href="%s" title="%s %d">%d</a>',
            get_edit_post_link($post_id),
            __('View transaction', __NAMESPACE__),
            $post_id,
            $post_id
        );
    }


    /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataCapacity($column, $post_id)
    {
        // How to set currency level?
        $v = get_post_meta($post_id, 'capacity', true);
        if ($v) {
            $v = intval($v);
            echo 'proper number format...';
            return;
        }
        printf('<p>%s</p>', __('No limit', __NAMESPACE__));
    }

    /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataRegistrant($column, $post_id)
    {
        global $post;
        // How to set currency level?
        $v = array_filter((array)get_post_meta($post_id, 'registrant', true));
        if (!$v) {
            $v =  [get_post_field( 'post_author', $post_id )];
        }
        // List all users in $v
        echo __METHOD__;
        return;
        printf('<a href="%s" title="%s %d">%d</a>',
            get_edit_post_link($post_id),
            __('View transaction', __NAMESPACE__),
            $post_id,
            $post_id
        );
    }


    /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataEvent($column, $post_id)
    {
        global $post;
        $post = wp_get_post_parent_id($post_id);
        if ($post) {
            $post = get_post($post);
            setup_postdata($post);
            printf('<a href="%s" title="%s">%s</a>',
                admin_url('a'),
                __('Limit to this event.', __NAMESPACE__),
                get_the_title()
            );
            wp_reset_postdata();
            return;
        }
        printf('<p>%s</p>', __('Unknown event.', __NAMESPACE__));
        // List all users in $v
        echo __METHOD__;
        return;
        printf('<a href="%s" title="%s %d">%d</a>',
            get_edit_post_link($post_id),
            __('View transaction', __NAMESPACE__),
            $post_id,
            $post_id
        );
    }



}