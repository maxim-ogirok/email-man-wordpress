<?php

if (!defined('ABSPATH')) {
    exit;
}

function email_man_enqueue()
{
    wp_register_style('select2css', EMAIL_MAN_URL . '/assets/css/select2.min.css', array(), EMAIL_MAN_VERSION, 'all');
    wp_enqueue_style('select2css');

    wp_register_script('select2', EMAIL_MAN_URL . '/assets/js/select2.min.js', array('jquery'), EMAIL_MAN_VERSION, true);
    wp_enqueue_script('select2');

    wp_register_style('email-mancss', EMAIL_MAN_URL . '/assets/css/main.css', array(), EMAIL_MAN_VERSION, 'all');
    wp_enqueue_style('email-mancss');

    wp_register_script('email-man', EMAIL_MAN_URL . '/assets/js/main.js', array('jquery'), EMAIL_MAN_VERSION, true);
    wp_enqueue_script('email-man');
}
add_action('admin_enqueue_scripts', 'email_man_enqueue');
