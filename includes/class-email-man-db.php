<?php

if (!defined('ABSPATH')) {
    exit;
}

define('EMAIL_MAN_TABLE_NAME', 'email_man_logs');

class Email_Man_DB
{
    /**
     * Create DB
     */
    public static function registration()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . EMAIL_MAN_TABLE_NAME;

        $sql = "CREATE TABLE " . $table_name . " (
            id int(11) NOT null AUTO_INCREMENT,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT null,
            email_from VARCHAR(256) null,
            email_to VARCHAR(256) null,
            user_id int(11) null,
            subject VARCHAR(256) null,
            headers LONGTEXT null,
            body LONGTEXT null,
            attachments LONGTEXT null,
            status LONGTEXT null,
            status_name LONGTEXT null,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Get all items
     * @param string $sql
     * @return array
     */
    public static function get_items($sql = false)
    {
        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}" . EMAIL_MAN_TABLE_NAME . ' WHERE 1';

        if ($sql) {
            $query .= $sql;
        }

        $orderby    = empty($_GET['orderby']) ? ''    : $_GET['orderby'];
        $order      = empty($_GET['order'])   ? 'ASC' : $_GET['order'];

        if (!empty($orderby) && !empty($order)) {
            $query .= ' ORDER BY ' . sanitize_sql_orderby("{$orderby} {$order}");
        } else {
            $query .= ' ORDER BY id DESC';
        }

        $results = $wpdb->get_results(
            $query,
            ARRAY_A
        );

        if (false === $results) {
            return array();
        }

        return $results;
    }

    /**
     * Save log to database
     * @param array $args {
     *     @param string[] $email_from   Email addresses from send message.
     *     @param string   $email_to     Email addresses to send message.
     *     @param string   $subject      Email subject.
     *     @param string[] $headers      Additional headers.
     *     @param string   $body         Message contents.
     *     @param string[] $attachments  Paths to files to attach.
     *     @param string   $status  		Status of sending email.
     *     @param string   $status_name  Description of sending a email.
     *     @param string   $user_id		User ID.
     * }
     */
    public static function create_item($args)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . EMAIL_MAN_TABLE_NAME;
        $data = array(
            'email_from'    => sanitize_email($args['from']),
            'email_to'      => (is_array($args['to'])) ? sanitize_email(implode(', ', $args['to'])) : sanitize_email($args['to']),
            'subject'       => sanitize_text_field($args['subject']),
            'headers'       => maybe_serialize($args['headers']),
            'body'          => wp_specialchars_decode($args['message'], ENT_QUOTES),
            'attachments'   => maybe_serialize($args['attachments']),
            'status'        => $args['status'],
            'status_name'   => sanitize_text_field($args['status_name']),
            'user_id'       => sanitize_key($args['user_id'])
        );

        return $wpdb->insert(
            $table_name,
            $data
        );
    }
}
