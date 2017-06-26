<?php
/*
	Plugin Name: Crumbls Event System
	Plugin URI: http://crumbls.com
	Description: A free event system for WordPress.
	Author: Chase C. Miller
	Version: 2.0.1a
	Author URI: http://crumbls.com
	Text Domain: crumbls\plugins\events
	Domain Path: /assets/lang
 */

namespace Crumbls\Plugins\Events;

defined('ABSPATH') or exit(1);

$s = require_once('vendor/autoload.php');

$s->addPsr4(__NAMESPACE__.'\\', __DIR__.'/vendor/crumbls/src', true);

$plugin = false;
if (is_admin()) {
    $plugin = Admin::getInstance();
} else {
    $plugin = Common::getInstance();
}
$plugin::build();