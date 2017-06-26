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
class Payment
{
    use Meta;

    public static function Display($p, $args = null)
    {
        global $post;

        $instance = self::getInstance();
        $instance::$field = $args['id'];

        $permission = self::adminPermissionCheck();

        if (!current_user_can($permission)) {
            return;
        }

        $possible = new \WP_Query([
            'post_type' => 'event_payment_method',
            'post_status' => 'publish',
            'ignore_sticky' => true,
            'order' => 'post_title',
            'order' => 'asc'
        ]);

        // Has the event already happened?  If so, disable these.
        if ($v = get_post_meta($p->ID, $instance::$field)) {
            // Simplify this.
        }

        $empty = false;

        // Clean up.

        if (!metadata_exists('post', $p->ID, $instance::$field)) {
            // Meta data has never been defined, set to all by default.
            $empty = true;
            $v = array_map(function($e) {
                return $e->ID;
            }, $possible->posts);
        } else {
            // Meta data has been set to nothing.
            $empty = false;
            echo 'b';
        }

        if (!array_key_exists('all', $v)) {
            $v['all'] = true;
        }

        ?>
        <div class="checkbox">
            <label>
            <input type="checkbox" name="<?php echo $instance::$field; ?>" value="all"
                          value="1" <?php checked(true, $v['all'], true ); ?>>
                <?php
                _e('Use Any', __NAMESPACE__);
                ?>
            </label>
        </div><?php

        if ($possible) {
            while ($possible->have_posts()) {
                $possible->the_post();
                ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="<?php echo $instance::$field; ?>" value="all"
                               value="1" <?php
                        if (in_array(get_the_ID(), $v)) {
                            echo 'checked';
                        }
                        ?>>

                        <?php the_title(); ?></label>
                </div>
                <?php
            }
            wp_reset_postdata();
        } else {
            printf('<p>%s</p>', __('Please setup a valid payment method.', __NAMESPACE__));
        }
    }
}

