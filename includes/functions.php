<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adding settings link
 * 
 * @param array $links 
 * @return array
 */
function plugin_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=email-man-settings">' . __('Settings') . '</a>';

    array_unshift($links, $settings_link);

    return $links;
}
add_filter("plugin_action_links_email-man/email-man.php", 'plugin_add_settings_link');

/**
 * Show row meta on the plugin screen.
 *
 * @param mixed $links Plugin Row Meta.
 * @param mixed $file  Plugin Base file.
 *
 * @return array
 */
function email_man_plugin_row_meta($links, $file)
{
    return array_merge($links, array('<a href="https://github.com/maxim-ogirok/emal-man" target="_blank">Github</a>'));
}
add_filter('plugin_row_meta', 'email_man_plugin_row_meta', 10, 2);
