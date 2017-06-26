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
class Event
{
    protected static $_class = __CLASS__;
    private static $_option = false;
    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    public function __construct()
    {
        add_filter('manage_edit-event_columns', [__CLASS__, 'columnDefinition']);
        add_action('manage_event_posts_custom_column', [__CLASS__, 'columnData'], 10, 2);

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
        $columns['location'] = __('Location', __NAMESPACE__);
        $columns['details'] = __('Details', __NAMESPACE__);
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
    public static function _columnDataDetails($column, $post_id)
    {
        global $wpdb;
        $sQuery = sprintf('SELECT post_type,post_status,count(*) as `total` FROM %s WHERE post_parent = %d AND post_type <> "revision" GROUP BY CONCAT(post_type,post_status)',
            $wpdb->posts,
            $post_id
        );
        $results = [
            'event_transaction_paid' => 0,
            'event_transaction_pending' => 0,
        ];
        foreach($wpdb->get_results($sQuery) as $row) {
            $results[$row->post_type.'_'.$row->post_status] = $row->total;
        }
        echo '<ul>';
        foreach($results as $k => $v) {
            printf('<li class="%s">%s %d</li>', $k, $k, $v);
        }
        echo '</ul>';

    }
   /**
     * Show transaction ID column
     * @param $column
     * @param $post_id
     */
    public static function _columnDataLocation($column, $post_id)
    {
        global $post;
        if ($v = get_post_meta($post_id, 'event_location', true)) {
            $post = get_post($v);
        }
        if (!$v || !$post) {
            printf('<p>%s</p>', __('No location set.', __NAMESPACE__));
        }
        setup_postdata($post);
        the_title();
        wp_reset_postdata();
        return;


        print_r($v);
        exit;
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