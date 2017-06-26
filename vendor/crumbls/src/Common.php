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
class Common
{
    protected static $_class = __CLASS__;
    private static $_option = false;
    protected static $_instance = NULL;

    /**
     * Prevent direct object creation
     */
    final private function __construct()
    {
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
     * Build
     */
    public static function build()
    {
        self::$_class = '\\' . get_class(self::getInstance());
        add_action('init', [self::$_class, 'init'], 0);
        add_filter('the_posts', [self::$_class, 'filterThePosts'], 9, 2);
        add_action('the_post', [self::$_class, 'actionThePost'], 10, 1);
    }

    /**
     * Common initializer
     */
    public static function init()
    {
        register_post_status( 'paid', array(
            'label'                     => _x( 'Paid', __NAMESPACE__ ),
            'label_count'                     => _n_noop( 'Paid (%s)',  'Paid (%s)', __NAMESPACE__ ),
            'public'                    => true,
            'internal'       			=> false,
            'private'       			=> false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
        ) );

        register_post_status( 'pending', array(
            'label'                     => _x( 'Pending', __NAMESPACE__ ),
            'label_count'                     => _n_noop( 'Pending (%s)',  'Pending (%s)', __NAMESPACE__ ),
            'public'                    => true,
            'internal'       			=> false,
            'private'       			=> false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => false,
        ) );


        $args = array(
            'label' => __('Event', __NAMESPACE__),
            'description' => __('Event Description', __NAMESPACE__),
            'labels' => array(
                'name' => _x('Events', 'Event General Name', __NAMESPACE__),
                'singular_name' => _x('Event', 'Event Singular Name', __NAMESPACE__),
                'menu_name' => __('Events', __NAMESPACE__),
                'name_admin_bar' => __('Event', __NAMESPACE__),
                'archives' => __('Item Archives', __NAMESPACE__),
                'attributes' => __('Item Attributes', __NAMESPACE__),
                'parent_item_colon' => __('Parent Item:', __NAMESPACE__),
                'all_items' => __('All Events', __NAMESPACE__),
                'add_new_item' => __('Add New Event', __NAMESPACE__),
                'add_new' => __('Add New', __NAMESPACE__),
                'new_item' => __('New Event', __NAMESPACE__),
                'edit_item' => __('Edit Event', __NAMESPACE__),
                'update_item' => __('Update Event', __NAMESPACE__),
                'view_item' => __('View Event', __NAMESPACE__),
                'view_items' => __('View Events', __NAMESPACE__),
                'search_items' => __('Search Event', __NAMESPACE__),
                'not_found' => __('Not found', __NAMESPACE__),
                'not_found_in_trash' => __('Not found in Trash', __NAMESPACE__),
                'featured_image' => __('Featured Image', __NAMESPACE__),
                'set_featured_image' => __('Set featured image', __NAMESPACE__),
                'remove_featured_image' => __('Remove featured image', __NAMESPACE__),
                'use_featured_image' => __('Use as featured image', __NAMESPACE__),
                'insert_into_item' => __('Insert into event', __NAMESPACE__),
                'uploaded_to_this_item' => __('Uploaded to this event', __NAMESPACE__),
                'items_list' => __('Events list', __NAMESPACE__),
                'items_list_navigation' => __('Events list navigation', __NAMESPACE__),
                'filter_items_list' => __('Filter events list', __NAMESPACE__),
            ),
            'supports' => array(),
            'taxonomies' => [
                'category',
                //'post_tag'
            ],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
        );
        register_post_type('event', $args);

        $args = array(
            'label' => __('Locations', __NAMESPACE__),
            'description' => __('Location Description', __NAMESPACE__),
            'labels' => array(
                'name' => _x('Locations', 'Location General Name', __NAMESPACE__),
                'singular_name' => _x('Location', 'Location Singular Name', __NAMESPACE__),
                'menu_name' => __('Locations', __NAMESPACE__),
                'name_admin_bar' => __('Location', __NAMESPACE__),
                'archives' => __('Location Archives', __NAMESPACE__),
                'attributes' => __('Location Attributes', __NAMESPACE__),
                'parent_item_colon' => __('Parent Location:', __NAMESPACE__),
                'all_items' => __('All Locations', __NAMESPACE__),
                'add_new_item' => __('Add New Location', __NAMESPACE__),
                'add_new' => __('Add New', __NAMESPACE__),
                'new_item' => __('New Location', __NAMESPACE__),
                'edit_item' => __('Edit Location', __NAMESPACE__),
                'update_item' => __('Update Location', __NAMESPACE__),
                'view_item' => __('View Location', __NAMESPACE__),
                'view_items' => __('View Location', __NAMESPACE__),
                'search_items' => __('Search Location', __NAMESPACE__),
                'not_found' => __('Not found', __NAMESPACE__),
                'not_found_in_trash' => __('Not found in Trash', __NAMESPACE__),
                'featured_image' => __('Featured Image', __NAMESPACE__),
                'set_featured_image' => __('Set featured image', __NAMESPACE__),
                'remove_featured_image' => __('Remove featured image', __NAMESPACE__),
                'use_featured_image' => __('Use as featured image', __NAMESPACE__),
                'insert_into_item' => __('Insert into event', __NAMESPACE__),
                'uploaded_to_this_item' => __('Uploaded to this event', __NAMESPACE__),
                'items_list' => __('Events list', __NAMESPACE__),
                'items_list_navigation' => __('Location list navigation', __NAMESPACE__),
                'filter_items_list' => __('Filter events list', __NAMESPACE__),
            ),
            'supports' => array('title','thumbnail'),
            'taxonomies' => [
//                'category',
                //'post_tag'
            ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'rewrite' => array(
                'slug' => 'location'
            )
        );
        register_post_type('event_location', $args);

        $args = array(
            'label' => __('Registrations', __NAMESPACE__),
            'description' => __('Registration Description', __NAMESPACE__),
            'labels' => array(
                'name' => _x('Registrations', 'Registration General Name', __NAMESPACE__),
                'singular_name' => _x('Registration', 'Registration Singular Name', __NAMESPACE__),
                'menu_name' => __('Registrations', __NAMESPACE__),
                'name_admin_bar' => __('Registration', __NAMESPACE__),
                'archives' => __('Registration Archives', __NAMESPACE__),
                'attributes' => __('Registration Attributes', __NAMESPACE__),
                'parent_item_colon' => __('Parent Registration:', __NAMESPACE__),
                'all_items' => __('All Registrations', __NAMESPACE__),
                'add_new_item' => __('Add New Registration', __NAMESPACE__),
                'add_new' => __('Add New', __NAMESPACE__),
                'new_item' => __('New Registration', __NAMESPACE__),
                'edit_item' => __('Edit Registration', __NAMESPACE__),
                'update_item' => __('Update Registration', __NAMESPACE__),
                'view_item' => __('View Registration', __NAMESPACE__),
                'view_items' => __('View Registration', __NAMESPACE__),
                'search_items' => __('Search Registration', __NAMESPACE__),
                'not_found' => __('Not found', __NAMESPACE__),
                'not_found_in_trash' => __('Not found in Trash', __NAMESPACE__),
                'featured_image' => __('Featured Image', __NAMESPACE__),
                'set_featured_image' => __('Set featured image', __NAMESPACE__),
                'remove_featured_image' => __('Remove featured image', __NAMESPACE__),
                'use_featured_image' => __('Use as featured image', __NAMESPACE__),
                'insert_into_item' => __('Insert into event', __NAMESPACE__),
                'uploaded_to_this_item' => __('Uploaded to this event', __NAMESPACE__),
                'items_list' => __('Events list', __NAMESPACE__),
                'items_list_navigation' => __('Registration list navigation', __NAMESPACE__),
                'filter_items_list' => __('Filter events list', __NAMESPACE__),
            ),
            'supports' => array(''),
            'taxonomies' => [
//                'category',
                //'post_tag'
            ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
        );
        register_post_type('event_registration', $args);

        $args = array(
            'label' => __('Transactions', __NAMESPACE__),
            'description' => __('Transaction Description', __NAMESPACE__),
            'labels' => array(
                'name' => _x('Transactions', 'Transaction General Name', __NAMESPACE__),
                'singular_name' => _x('Transaction', 'Transaction Singular Name', __NAMESPACE__),
                'menu_name' => __('Transactions', __NAMESPACE__),
                'name_admin_bar' => __('Transaction', __NAMESPACE__),
                'archives' => __('Transaction Archives', __NAMESPACE__),
                'attributes' => __('Transaction Attributes', __NAMESPACE__),
                'parent_item_colon' => __('Parent Transaction:', __NAMESPACE__),
                'all_items' => __('All Transactions', __NAMESPACE__),
                'add_new_item' => __('Add New Transaction', __NAMESPACE__),
                'add_new' => __('Add New', __NAMESPACE__),
                'new_item' => __('New Transaction', __NAMESPACE__),
                'edit_item' => __('Edit Transaction', __NAMESPACE__),
                'update_item' => __('Update Transaction', __NAMESPACE__),
                'view_item' => __('View Transaction', __NAMESPACE__),
                'view_items' => __('View Transaction', __NAMESPACE__),
                'search_items' => __('Search Transaction', __NAMESPACE__),
                'not_found' => __('Not found', __NAMESPACE__),
                'not_found_in_trash' => __('Not found in Trash', __NAMESPACE__),
                'featured_image' => __('Featured Image', __NAMESPACE__),
                'set_featured_image' => __('Set featured image', __NAMESPACE__),
                'remove_featured_image' => __('Remove featured image', __NAMESPACE__),
                'use_featured_image' => __('Use as featured image', __NAMESPACE__),
                'insert_into_item' => __('Insert into event', __NAMESPACE__),
                'uploaded_to_this_item' => __('Uploaded to this event', __NAMESPACE__),
                'items_list' => __('Events list', __NAMESPACE__),
                'items_list_navigation' => __('Transaction list navigation', __NAMESPACE__),
                'filter_items_list' => __('Filter events list', __NAMESPACE__),
            ),
            'supports' => array(''),
            'taxonomies' => [
//                'category',
                //'post_tag'
            ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'map_meta_cap'        => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => true
            )
        );
        register_post_type('event_transaction', $args);


        $args = array(
            'label' => __('Payment Methods', __NAMESPACE__),
            'description' => __('Payment Methods Description', __NAMESPACE__),
            'labels' => array(
                'name' => _x('Payment Methods', 'Registration General Name', __NAMESPACE__),
                'singular_name' => _x('Payment Method', 'Registration Singular Name', __NAMESPACE__),
                'menu_name' => __('Payment Methods', __NAMESPACE__),
                'name_admin_bar' => __('Payment Method', __NAMESPACE__),
                'archives' => __('Payment Method Archives', __NAMESPACE__),
                'attributes' => __('Payment Method Attributes', __NAMESPACE__),
                'parent_item_colon' => __('Parent Registration:', __NAMESPACE__),
                'all_items' => __('All Payment Methods', __NAMESPACE__),
                'add_new_item' => __('Add New Payment Method', __NAMESPACE__),
                'add_new' => __('Add New', __NAMESPACE__),
                'new_item' => __('New Payment Method', __NAMESPACE__),
                'edit_item' => __('Edit Payment Method', __NAMESPACE__),
                'update_item' => __('Update Payment Method', __NAMESPACE__),
                'view_item' => __('View Payment Method', __NAMESPACE__),
                'view_items' => __('View Payment Method', __NAMESPACE__),
                'search_items' => __('Search Payment Methods', __NAMESPACE__),
                'not_found' => __('Not found', __NAMESPACE__),
                'not_found_in_trash' => __('Not found in Trash', __NAMESPACE__),
                'featured_image' => __('Featured Image', __NAMESPACE__),
                'set_featured_image' => __('Set featured image', __NAMESPACE__),
                'remove_featured_image' => __('Remove featured image', __NAMESPACE__),
                'use_featured_image' => __('Use as featured image', __NAMESPACE__),
                'insert_into_item' => __('Insert into event', __NAMESPACE__),
                'uploaded_to_this_item' => __('Uploaded to this event', __NAMESPACE__),
                'items_list' => __('Events list', __NAMESPACE__),
                'items_list_navigation' => __('Registration list navigation', __NAMESPACE__),
                'filter_items_list' => __('Filter events list', __NAMESPACE__),
            ),
            'supports' => array(''),
            'taxonomies' => [
//                'category',
                //'post_tag'
            ],
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'map_meta_cap'        => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => true
            )
        );
        if (
        array_key_exists('overridekey', $_REQUEST)
        &&
        true // validate key
        ) {
            $args['capabilities']['create_posts'] = true;
        }
        register_post_type('event_payment_method', $args);




    }


    /**
     * Getter and setter for an option value.
     * @param null $key
     * @param null $value
     */
    public static function option($key = null, $value = null)
    {
        if (!self::$_option) {
            self::$_option = get_option('event_settings');
        }
        echo $key . ' ' . $value;
    }


    /**
     * Filter the posts data.
     * @param $posts
     * @param $query
     * @return mixed
     */
    public static function filterThePosts($posts, $query) {
        foreach($posts as $post) {
            if ($post->post_type == 'event') {
                $post->event_location = 'a';
                $post->event_dates = [
                    'b'
                ];
            }
        }
        return $posts;
    }

    /**
     * Add in extra data to $post when needed.
     * @param $post
     */
    public static function actionThePost(&$post) {
        global $wpdb;
        if ($post->post_type != 'event') {
            return $post;
        }
        if (!property_exists($post, 'event_location')) {
            $post->event_location = false;
        }
        if (!property_exists($post, 'event_dates')) {
            $post->event_dates = [];

            $temp = $wpdb->get_results(sprintf('SELECT meta_key,meta_value FROM %s WHERE post_id = %d AND meta_key LIKE "repeat\_%%"',
                $wpdb->postmeta,
                $post->ID
            ), ARRAY_A);
            if ($temp) {
                $groups = array_flip(array_unique(array_map(function($e) { return substr($e, 0, 8); }, array_column($temp, 'meta_key'))));
                foreach($groups as $k => &$group) {
                    $group = [
                        'start' => null,
                        'end' => null,
                        'interval' => 0,
                        'year' => null,
                        'month' => null,
                        'day' => null,
                        'week' => null
                    ];
                    $dt =  get_post_time('U', true);
                    $group['start'] = $dt - ($dt % 86400);
                    $group['end'] = $group['start']+ 86400;

                    $merge = array_filter($temp, function($e) use ($k) {
                       return strpos($e['meta_key'], $k) === 0;
                    });
                    foreach($merge as $i => $m) {
                        $group[substr($m['meta_key'], 9)] = $m['meta_value'];
                        unset($temp[$i]);
                    }

                    if ($group['end'] < $group['start']) {
                        $group['end'] = $group['start']+ 86400;
                    }

                    // Now, build out event dates.
                    // This is where we are at.
                }
                return true;
                print_r($groups);
                exit;
            }
        }
    }

}