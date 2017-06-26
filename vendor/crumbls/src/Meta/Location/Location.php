<?php

namespace Crumbls\Plugins\Events\Meta\Location;

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

        $v = get_post_meta($post->ID, $field, true);

        if (!$v) {
            $v = [
                'physical' => [
                    'enabled' => false
                ],
                'digital' => [
                    'enabled' => false
                ]
            ];
        }

        ?>
        <a href="#physical" class="btn btn-default" data-toggle="collapse">Toggle Foo</a>
        <button href="#Bar" class="btn btn-default" data-toggle="collapse">Toggle Bar</button>
        <div id="physical" class="collapse in">
            This is only shown if there is a physical location.
            <?php print_r($v['physical']); ?>
        </div>
        <div id="digital" class="collapse">
            This is only shown if there is a Digital location.
            <?php print_r($v['digital']); ?>
        </div>
<?php

        echo $field;

//        echo get_class($instance).'\\'.__FUNCTION__;
    }
}