<?php

/**
 * Plugin Name: Email Man Logger
 * Description: Easily keep track of what emails your WordPress sends
 * Author: Maxim Ogirok
 * Version: 0.0.1
 * Author URI: https://github.com/maxim-ogirok
 * Domain: domain_email_man
 */

if (!defined('ABSPATH')) {
	exit;
}

// Define important data
define('EMAIL_MAN_PATH', __DIR__);
define('EMAIL_MAN_URL', plugin_dir_url(__FILE__));
define('EMAIL_MAN_VERSION', '0.0.1');

/**
 * Include DB
 */
include_once(EMAIL_MAN_PATH . '/includes/class-email-man-db.php');

/**
 * Include table template
 */
include_once(EMAIL_MAN_PATH . '/includes/class-email-man-template.php');

/**
 * Include pages
 */
include_once(EMAIL_MAN_PATH . '/includes/class-email-man-pages.php');

/**
 * Include sniffing
 */
include_once(EMAIL_MAN_PATH . '/includes/class-email-man-sniffing.php');

/**
 * Include logic
 */
include_once(EMAIL_MAN_PATH . '/includes/class-email-man-logic.php');

/**
 * Register scripts and styles
 */
include_once(EMAIL_MAN_PATH . '/includes/enqueue.php');

/**
 * Plugin functions
 */
include_once(EMAIL_MAN_PATH . '/includes/functions.php');

/**
 * Create table
 */
register_activation_hook(__FILE__, ['Email_Man_DB', 'registration']);