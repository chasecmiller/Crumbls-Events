<?php

namespace Crumbls\Plugins\Events;

use Omnipay\Omnipay;


/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class Admin extends Common
{
    public static function build()
    {
        parent::build();

        // Initialize our admin menu
        add_action('admin_menu', [self::$_class, 'menuInit']);

        // Intialize meta boxes
        add_action('add_meta_boxes', [self::$_class, 'metaAdd']);

        // Initialize admin
        add_action('admin_init', [self::$_class, 'adminInit']);

        // Handle initilization of WP_Post in another way when needed.
        add_action('admin_head', [self::$_class, 'adminHeadCheck']);


        add_action('current_screen',
            function () {
                $current_screen = get_current_screen();
                if (
                    $current_screen->action != ''
                    ||
                    $current_screen->base != 'edit'
                    ||
                    strpos($current_screen->post_type, 'event') !== 0
                ) {
                    return;
                }
                $class = explode('_', $current_screen->post_type);
                $class = __NAMESPACE__.'\\Edit\\'.ucwords($class[sizeof($class)-1]);
                $class::getInstance();
            }
        );

    }

    /**
     * Setup the administrative menu
     */
    public static function menuInit()
    {
        global $menu, $submenu;
//print_r($temp);
//        exit;
        /**
         * Menu items that need completed
         * Registrations
         * Transactions
         * Settings
         *  General
         *  Payment Methods
         */

        // Add in locations
        foreach ([
                     'event_location',
                     'event_registration',
                     'event_transaction'
                 ] as $temp) {
            $temp = get_post_type_object($temp);

            $submenu['edit.php?post_type=event'][] = [
                $temp->labels->menu_name,
                'manage_options',
                admin_url('edit.php?post_type=' . $temp->name)
            ];

        }

        add_submenu_page(
            'edit.php?post_type=event',
            __('General Settings', __NAMESPACE__),
            __('General Settings', __NAMESPACE__),
            'manage_options',
            'general',
            ['\\' . __NAMESPACE__ . '\\PageGeneral', 'Display'],
            get_stylesheet_directory_uri('stylesheet_directory') . "/images/media-button-other.gif"
        );

        $temp = get_post_type_object('event_payment_method');
        $submenu['edit.php?post_type=event'][] = [
            $temp->labels->menu_name,
            'manage_options',
            admin_url('edit.php?post_type=event_payment_method')
        ];


        // Easy save post handler.
        add_action('save_post', [self::$_class, 'saveHandler'], 10, 1);

    }

    /**
     * Setup meta boxes
     */
    public static function metaAdd()
    {
        add_meta_box(
            'date',
            __('Dates', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Date', 'Display'],
            'event',
            'normal',
            'default'
        );
        add_meta_box(
            'ticket',
            __('Tickets', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Ticket', 'Display'],
            'event',
            'normal',
            'default'
        );
        add_meta_box(
            'notification',
            __('Notifications', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Notification', 'Display'],
            'event',
            'normal',
            'default'
        );


        /**
         * On to the sidebar
         */
        remove_meta_box('submitdiv', 'event', 'core'); // $item represents post_type
        add_meta_box(
            'submitdiv',
            __('Publish', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Submit', 'Display'],
            'event',
            'side',
            'high'
        ); // $value will be the output title in the box


        add_meta_box(
            'location',
            __('Location', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Location', 'Display'],
            'event',
            'side',
            'default'
        );

        add_meta_box(
            'template',
            __('Template', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Template', 'Display'],
            'event',
            'side',
            'default'
        );

        add_meta_box(
            'payment_method',
            __('Payment Methods', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Event\\Payment', 'Display'],
            'event',
            'side',
            'default'
        );


        /**
         * Meta boxes for locations
         */
        add_meta_box(
            'venue_details',
            __('Details', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Location\\Detail', 'Display'],
            'event_location',
            'side',
            'default'
        );

        add_meta_box(
            'location',
            __('Location', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Location\\Location', 'Display'],
            'event_location',
            'normal',
            'default'
        );

        /**
         * Registrations
         */

        add_meta_box(
            'contact_details',
            __('Details', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Registration\\Details', 'Display'],
            'event_registration',
            'side',
            'default'
        );


        /**
         * Transactions
         */

        /**
         * Payment Methods
         */

        add_meta_box(
            'method_type',
            __('Method Type', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Method\\Type', 'Display'],
            'event_payment_method',
            'normal',
            'default'
        );

        add_meta_box(
            'method_details',
            __('Details', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Method\\Details', 'Display'],
            'event_payment_method',
            'normal',
            'default'
        );


        add_meta_box(
            'method_log',
            __('Log', __NAMESPACE__),
            ['\\' . __NAMESPACE__ . '\\Meta\\Method\\Log', 'Display'],
            'event_payment_method',
            'normal',
            'default'
        );

    }

    /**
     * Administrative initialization
     */
    public static function adminInit()
    {
        register_setting(
            'event_settings', // Option group
            'my_option_name', // Option name
            array(self::$_class, 'sanitize') // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'My Custom Settings', // Title
            array(self::$_class, 'print_section_info'), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'id_number', // ID
            'ID Number', // Title
            array(self::$_class, 'id_number_callback'), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'title',
            'Title',
            array(self::$_class, 'title_callback'),
            'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Meta box to allow enabling or disabling of payment methods.
     * @param null $post
     */
    public static function metaPayment($post = null)
    {
        $v = get_post_meta($post->ID, 'payment_methods', true);
        print_r($v);
    }


    /**
     * Meta box to show all event dates
     * @param null $post
     */
    public static function metaDate($post = null)
    {
        echo __METHOD__;
        $meta = get_post_meta($post->ID);
        echo '<pre>';
        print_r($meta);
        echo '</pre>';
        /*
         * repeat_start
         * repeat_end
         */
        print_r($post);
    }


    public static function metaTicket($post = null)
    {
        echo __METHOD__;
        print_r($post);
    }

    /**
     * Meta box for location selection.
     * @param null $post
     */
    public static function metaLocation($post = null)
    {
        $v = get_post_meta($post->ID, 'location', true);
        echo __METHOD__;

        // Show all location information.  Choose which data to display
        if (!$v) {
            echo 'location not yet chosen.';
        }
    }


    /**
     * Meta box that allows customization of notifications.
     * @param null $post
     */
    public static function metaNotification($post = null)
    {
        $v = get_post_meta($post->ID, 'location', true);
        echo __METHOD__;

        // Show all location information.  Choose which data to display
        if (!$v) {
//            echo 'location not yet chosen.';
        }
        echo 'admins!';
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public static function sanitize($input)
    {
        $new_input = array();
        if (isset($input['id_number']))
            $new_input['id_number'] = absint($input['id_number']);

        if (isset($input['title']))
            $new_input['title'] = sanitize_text_field($input['title']);

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public static function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public static function id_number_callback()
    {
        printf(
            '<input type="text" id="id_number" name="event_settings[id_number]" value="%s" />',
            self::option('id_number')

        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public static function title_callback()
    {
        printf(
            '<input type="text" id="title" name="event_settings[title]" value="%s" />',
            self::option('title')
        );
    }

    /**
     * Make sure our WP_Post is setup properly.
     */
    public static function adminHeadCheck()
    {
        global $post;
        if (
            !$post
            ||
            !property_exists($post, 'post_type')
            ||
            $post->post_type != 'event'
        ) {
            return true;
        }
        // Force a setup_postdata.
        setup_postdata($post);
        return true;
    }

    public static function saveHandler($post_id)
    {
        global $post;
        if (!$post || $post->ID != $post_id) {
            $post = get_post($post_id);
        }
        if (!$post || strpos($post->post_type, 'event') !== 0) {
            return;
        }
        $obj = get_post_type_object($post->post_type);
        if (!current_user_can($obj->cap->edit_post, $post->ID)) {
            return;
        }

        $type = explode('_', $post->post_type);
        $type = ucwords($type[sizeof($type) - 1]);
        $path = __DIR__ . '/Meta/' . $type;
        if (!file_exists($path) || !is_dir($path)) {
            return;
        }
        $classes = array_map(function ($e) {
            return '\\' . __NAMESPACE__ . str_replace('/', '\\', substr($e, strlen(__DIR__), -4));
        }, glob($path . '/*.php'));
        foreach ($classes as $class) {
            call_user_func([$class, 'Save'], $post_id);
        }
    }

}