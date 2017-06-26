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
class Template
{
    use Meta;

    public static function Display($post, $args = null)
    {
        global $action, $wpdb;

        $instance = self::getInstance();
        $instance::$field = '_wp_page_template';

        $permission = $instance::adminPermissionCheck();

        if (!current_user_can($permission)) {
            printf('<p>%s</p>', __('You do not have access to these settings.', __NAMESPACE__));
            return;
        }
        $v = get_post_meta($post->ID, '_wp_page_template', true);
        print_r($v);
        ?>
        <select name="<?php echo $instance::$field; ?>" class="widefat">
            <?php
            printf('<option value="false">%s</option>', __('Default', __NAMESPACE__));
            $rows = [];
            foreach ($rows as $row) {
                printf('<option value="%s" %s>%s</option>',
                    'a',
                    selected('a', 'current', false),
                    htmlentities('name')
                );
            }
            ?>
        </select>
        <?php
   }

    /**
     * Handle template save
     * @param $post_id
     */
    public static function Save($post_id) {

        $instance = self::getInstance();
        $instance::$field = '_wp_page_template';
        if (
            !array_key_exists($instance::$field, $_POST)
        ||
            !$_POST[$instance::$field]
            // Maybe add in a check for abspath position to make sure we are using a WP file?

        ) {
        } else if (file_exists($_POST[$instance::$field])) {
            update_post_meta( $post_id, $instance::$field, $_POST[$instance::$field] );
            return true;
        }
        delete_post_meta($post_id, $instance::$field);
    }

}