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
class Submit
{
    use Meta;

    public static function Display($post, $args = null)
    {
        global $action, $wpdb;

        $instance = self::getInstance();
        $instance::$field = $args['id'];

        $permission = $instance::adminPermissionCheck();

        if (!current_user_can($permission)) {
            printf('<p>%s</p>', __('You do not have access to these settings.', __NAMESPACE__));
            return;
        }

        $post_type = $post->post_type;
        $post_type_object = get_post_type_object($post_type);
        $can_publish = current_user_can($post_type_object->cap->publish_posts);
        ?>
        <div class="submitbox" id="submitpost">

            <div id="minor-publishing">

                <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key
                ?>
                <div style="display:none;">
                    <?php submit_button(__('Save'), '', 'save'); ?>
                </div>

                <div id="minor-publishing-actions">
                    <div id="save-action">
                        <?php if ('publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status) { ?>
                            <input <?php if ('private' == $post->post_status) { ?>style="display:none"<?php } ?>
                                   type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>"
                                   class="button"/>
                            <span class="spinner"></span>
                        <?php } elseif ('pending' == $post->post_status && $can_publish) { ?>
                            <input type="submit" name="save" id="save-post"
                                   value="<?php esc_attr_e('Save as Pending'); ?>" class="button"/>
                            <span class="spinner"></span>
                        <?php } ?>
                    </div>
                    <?php if (is_post_type_viewable($post_type_object)) : ?>
                        <div id="preview-action">
                            <?php
                            $preview_link = esc_url(get_preview_post_link($post));
                            if ('publish' == $post->post_status) {
                                $preview_button = __('Preview Changes');
                            } else {
                                $preview_button = __('Preview');
                            }
                            ?>
                            <a class="preview button" href="<?php echo $preview_link; ?>"
                               target="wp-preview-<?php echo (int)$post->ID; ?>"
                               id="post-preview"><?php echo $preview_button; ?></a>
                            <input type="hidden" name="wp-preview" id="wp-preview" value=""/>
                        </div>
                    <?php endif; // public post type
                    ?>
                    <?php
                    /**
                     * Fires before the post time/date setting in the Publish meta box.
                     *
                     * @since 4.4.0
                     *
                     * @param WP_Post $post WP_Post object for the current post.
                     */
                    do_action('post_submitbox_minor_actions', $post);
                    ?>
                    <div class="clear"></div>
                </div><!-- #minor-publishing-actions -->

                <div id="misc-publishing-actions">

                    <div class="misc-pub-section misc-pub-post-status">
                        <?php _e('Status:') ?> <span id="post-status-display"><?php

                            switch ($post->post_status) {
                                case 'private':
                                    _e('Privately Published');
                                    break;
                                case 'publish':
                                    _e('Published');
                                    break;
                                case 'future':
                                    _e('Scheduled');
                                    break;
                                case 'pending':
                                    _e('Pending Review');
                                    break;
                                case 'draft':
                                case 'auto-draft':
                                    _e('Draft');
                                    break;
                            }
                            ?>
</span>
                        <?php if ('publish' == $post->post_status || 'private' == $post->post_status || $can_publish) { ?>
                            <a href="#post_status" <?php if ('private' == $post->post_status) { ?>style="display:none;"
                               <?php } ?>class="edit-post-status hide-if-no-js" role="button"><span
                                    aria-hidden="true"><?php _e('Edit'); ?></span> <span
                                    class="screen-reader-text"><?php _e('Edit status'); ?></span></a>

                            <div id="post-status-select" class="hide-if-js">
                                <input type="hidden" name="hidden_post_status" id="hidden_post_status"
                                       value="<?php echo esc_attr(('auto-draft' == $post->post_status) ? 'draft' : $post->post_status); ?>"/>
                                <label for="post_status" class="screen-reader-text"><?php _e('Set status') ?></label>
                                <select name="post_status" id="post_status">
                                    <?php if ('publish' == $post->post_status) : ?>
                                        <option<?php selected($post->post_status, 'publish'); ?>
                                            value='publish'><?php _e('Published') ?></option>
                                    <?php elseif ('private' == $post->post_status) : ?>
                                        <option<?php selected($post->post_status, 'private'); ?>
                                            value='publish'><?php _e('Privately Published') ?></option>
                                    <?php elseif ('future' == $post->post_status) : ?>
                                        <option<?php selected($post->post_status, 'future'); ?>
                                            value='future'><?php _e('Scheduled') ?></option>
                                    <?php endif; ?>
                                    <option<?php selected($post->post_status, 'pending'); ?>
                                        value='pending'><?php _e('Pending Review') ?></option>
                                    <?php if ('auto-draft' == $post->post_status) : ?>
                                        <option<?php selected($post->post_status, 'auto-draft'); ?>
                                            value='draft'><?php _e('Draft') ?></option>
                                    <?php else : ?>
                                        <option<?php selected($post->post_status, 'draft'); ?>
                                            value='draft'><?php _e('Draft') ?></option>
                                    <?php endif; ?>
                                </select>
                                <a href="#post_status"
                                   class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
                                <a href="#post_status"
                                   class="cancel-post-status hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
                            </div>

                        <?php } ?>
                    </div><!-- .misc-pub-section -->

                    <div class="misc-pub-section misc-pub-visibility" id="visibility">
                        <?php _e('Visibility:'); ?> <span id="post-visibility-display"><?php

                            if ('private' == $post->post_status) {
                                $post->post_password = '';
                                $visibility = 'private';
                                $visibility_trans = __('Private');
                            } elseif (!empty($post->post_password)) {
                                $visibility = 'password';
                                $visibility_trans = __('Password protected');
                            } elseif ($post_type == 'post' && is_sticky($post->ID)) {
                                $visibility = 'public';
                                $visibility_trans = __('Public, Sticky');
                            } else {
                                $visibility = 'public';
                                $visibility_trans = __('Public');
                            }

                            echo esc_html($visibility_trans); ?></span>
                        <?php if ($can_publish) { ?>
                            <a href="#visibility" class="edit-visibility hide-if-no-js" role="button"><span
                                    aria-hidden="true"><?php _e('Edit'); ?></span> <span
                                    class="screen-reader-text"><?php _e('Edit visibility'); ?></span></a>

                            <div id="post-visibility-select" class="hide-if-js">
                                <input type="hidden" name="hidden_post_password" id="hidden-post-password"
                                       value="<?php echo esc_attr($post->post_password); ?>"/>
                                <?php if ($post_type == 'post'): ?>
                                    <input type="checkbox" style="display:none" name="hidden_post_sticky"
                                           id="hidden-post-sticky"
                                           value="sticky" <?php checked(is_sticky($post->ID)); ?> />
                                <?php endif; ?>
                                <input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility"
                                       value="<?php echo esc_attr($visibility); ?>"/>
                                <input type="radio" name="visibility" id="visibility-radio-public"
                                       value="public" <?php checked($visibility, 'public'); ?> /> <label
                                    for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br/>
                                <?php if ($post_type == 'post' && current_user_can('edit_others_posts')) : ?>
                                    <span id="sticky-span"><input id="sticky" name="sticky" type="checkbox"
                                                                  value="sticky" <?php checked(is_sticky($post->ID)); ?> /> <label
                                            for="sticky"
                                            class="selectit"><?php _e('Stick this post to the front page'); ?></label><br/></span>
                                <?php endif; ?>
                                <input type="radio" name="visibility" id="visibility-radio-password"
                                       value="password" <?php checked($visibility, 'password'); ?> /> <label
                                    for="visibility-radio-password"
                                    class="selectit"><?php _e('Password protected'); ?></label><br/>
                                <span id="password-span"><label
                                        for="post_password"><?php _e('Password:'); ?></label> <input type="text"
                                                                                                     name="post_password"
                                                                                                     id="post_password"
                                                                                                     value="<?php echo esc_attr($post->post_password); ?>"
                                                                                                     maxlength="255"/><br/></span>
                                <input type="radio" name="visibility" id="visibility-radio-private"
                                       value="private" <?php checked($visibility, 'private'); ?> /> <label
                                    for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br/>

                                <p>
                                    <a href="#visibility"
                                       class="save-post-visibility hide-if-no-js button"><?php _e('OK'); ?></a>
                                    <a href="#visibility"
                                       class="cancel-post-visibility hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
                                </p>
                            </div>
                        <?php } ?>

                    </div><!-- .misc-pub-section -->

                    <?php
                    /* translators: Publish box date format, see https://secure.php.net/date */
                    $datef = __('M j, Y @ H:i');
                    if (0 != $post->ID) {
                        if ('future' == $post->post_status) { // scheduled for publishing at a future date
                            /* translators: Post date information. 1: Date on which the post is currently scheduled to be published */
                            $stamp = __('Scheduled for: <b>%1$s</b>');
                        } elseif ('publish' == $post->post_status || 'private' == $post->post_status) { // already published
                            /* translators: Post date information. 1: Date on which the post was published */
                            $stamp = __('Published on: <b>%1$s</b>');
                        } elseif ('0000-00-00 00:00:00' == $post->post_date_gmt) { // draft, 1 or more saves, no date specified
                            $stamp = __('Publish <b>immediately</b>');
                        } elseif (time() < strtotime($post->post_date_gmt . ' +0000')) { // draft, 1 or more saves, future date specified
                            /* translators: Post date information. 1: Date on which the post is to be published */
                            $stamp = __('Schedule for: <b>%1$s</b>');
                        } else { // draft, 1 or more saves, date specified
                            /* translators: Post date information. 1: Date on which the post is to be published */
                            $stamp = __('Publish on: <b>%1$s</b>');
                        }
                        $date = date_i18n($datef, strtotime($post->post_date));
                    } else { // draft (no saves, and thus no date specified)
                        $stamp = __('Publish <b>immediately</b>');
                        $date = date_i18n($datef, strtotime(current_time('mysql')));
                    }

                    if (!empty($args['args']['revisions_count'])) : ?>
                        <div class="misc-pub-section misc-pub-revisions">
                            <?php
                            /* translators: Post revisions heading. 1: The number of available revisions */
                            printf(__('Revisions: %s'), '<b>' . number_format_i18n($args['args']['revisions_count']) . '</b>');
                            ?>
                            <a class="hide-if-no-js"
                               href="<?php echo esc_url(get_edit_post_link($args['args']['revision_id'])); ?>"><span
                                    aria-hidden="true"><?php _ex('Browse', 'revisions'); ?></span> <span
                                    class="screen-reader-text"><?php _e('Browse revisions'); ?></span></a>
                        </div>
                    <?php endif;


                    if ($can_publish) : // Contributors don't get to choose the date of publish
                        ?>
                        <div class="misc-pub-section curtime misc-pub-curtime">
                        <span id="timestamp">
	<?php printf($stamp, $date); ?></span>
                        <a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button"><span
                                aria-hidden="true"><?php _e('Edit'); ?></span> <span
                                class="screen-reader-text"><?php _e('Edit date and time'); ?></span></a>
                        <fieldset id="timestampdiv" class="hide-if-js">
                            <legend class="screen-reader-text"><?php _e('Date and time'); ?></legend>
                            <?php touch_time(($action === 'edit'), 1); ?>
                        </fieldset>
                        </div><?php // /misc-pub-section
                        ?>
                    <?php endif; ?>

                    <?php
                    /*
                                        $count = $wpdb->get_results(sprintf(
                                            'SELECT `meta_key`, count(*) as `total` FROM %s WHERE meta_value = %d GROUP BY meta_key',
                                            $wpdb->postmeta,
                                            $post->ID
                                        ));
                                        print_r($count);
                    */
                    echo 'show subscription count here.';


                    ?>

                    <?php
                    /**
                     * Fires after the post time/date setting in the Publish meta box.
                     *
                     * @since 2.9.0
                     * @since 4.4.0 Added the `$post` parameter.
                     *
                     * @param WP_Post $post WP_Post object for the current post.
                     */
                    do_action('post_submitbox_misc_actions', $post);
                    ?>
                </div>
                <div class="clear"></div>
            </div>

            <div id="major-publishing-actions">
                <?php
                /**
                 * Fires at the beginning of the publishing actions section of the Publish meta box.
                 *
                 * @since 2.7.0
                 */
                do_action('post_submitbox_start');
                ?>
                <div id="delete-action">
                    <?php
                    if (current_user_can("delete_post", $post->ID)) {
                        if (!EMPTY_TRASH_DAYS)
                            $delete_text = __('Delete Permanently');
                        else
                            $delete_text = __('Move to Trash');
                        ?>
                        <a class="submitdelete deletion"
                           href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
                    } ?>
                </div>

                <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php
                    if (!in_array($post->post_status, array('publish', 'future', 'private')) || 0 == $post->ID) {
                        if ($can_publish) :
                            if (!empty($post->post_date_gmt) && time() < strtotime($post->post_date_gmt . ' +0000')) : ?>
                                <input name="original_publish" type="hidden" id="original_publish"
                                       value="<?php esc_attr_e('Schedule') ?>"/>
                                <?php submit_button(__('Schedule'), 'primary large', 'publish', false); ?>
                            <?php else : ?>
                                <input name="original_publish" type="hidden" id="original_publish"
                                       value="<?php esc_attr_e('Publish') ?>"/>
                                <?php submit_button(__('Publish'), 'primary large', 'publish', false); ?>
                            <?php endif;
                        else : ?>
                            <input name="original_publish" type="hidden" id="original_publish"
                                   value="<?php esc_attr_e('Submit for Review') ?>"/>
                            <?php submit_button(__('Submit for Review'), 'primary large', 'publish', false); ?>
                            <?php
                        endif;
                    } else { ?>
                        <input name="original_publish" type="hidden" id="original_publish"
                               value="<?php esc_attr_e('Update') ?>"/>
                        <input name="save" type="submit" class="button button-primary button-large" id="publish"
                               value="<?php esc_attr_e('Update') ?>"/>
                        <?php
                    } ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <?php
    }


}