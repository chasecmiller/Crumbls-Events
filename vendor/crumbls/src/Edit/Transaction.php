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
class Transaction
{
    protected static $_class = __CLASS__;
    private static $_option = false;
    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    public function __construct()
    {
        add_filter('manage_edit-event_transaction_columns', [__CLASS__, 'columnDefinition']);
        add_action('manage_event_transaction_posts_custom_column', [__CLASS__, 'columnData'], 10, 2);

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
            'post_date' => __('Transaction Date', __NAMESPACE__),
            'total' => __('Total', __NAMESPACE__),
            'paid' => __('Paid', __NAMESPACE__),
            'registrant' => __('Registrant', __NAMESPACE__),
            'event' => __('Event', __NAMESPACE__),
            'actions' => __('Actions', __NAMESPACE__)
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
    public static function _columnDataPostDate($column, $post_id)
    {
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
    public static function _columnDataTotal($column, $post_id)
    {
        // How much should this have cost?
        echo 'How much should this have cost?';
        return;

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
    public static function _columnDataPaid($column, $post_id)
    {
        // How to set currency level?

        $v = get_post_meta($post_id, 'cost');
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
    public static function _columnDataRegistrant($column, $post_id)
    {
        global $post;
        // How to set currency level?
        $v = array_filter((array)get_post_meta($post_id, 'registrant', true));
        if (!$v) {
            $v =  [get_post_field( 'post_author', $post_id )];
        }

        $users = get_users([
            'include' => $v
        ]);

        echo '<ul>';
        foreach($users as $user) {
            printf('<li>%s</li>',
                $user->display_name);
        }
        echo '</ul>';
        return;
        print_r($users);

        print_r($v);
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
                admin_url('edit.php?post_type=event_transaction&post_parent='.get_the_ID()),
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