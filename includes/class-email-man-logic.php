<?php

if (!defined('ABSPATH')) {
    exit;
}

class Email_Man_Logic
{
    protected static $options;
    protected static $args;

    function __construct($args)
    {
        self::$options = get_option('email_man_plugin_options');
        self::$args    = $args;
    }

    /**
     * Processing
     * 
     * @return bool 
     */
    function processing()
    {
        if (!self::check_emails()) {
            return false;
        }

        if (!self::check_subjecs()) {
            return false;
        }

        if (!self::check_message()) {
            return false;
        }

        return true;
    }

    /**
     * Check emails
     * 
     * @return bool 
     */
    function check_emails()
    {
        if (!isset(self::$args['to']) || empty(self::$args['to'])) {
            return true;
        }

        if (isset(self::$options['dont_store_emails']) && count(self::$options['dont_store_emails']) > 0) {
            if (in_array(self::$args['to'], self::$options['dont_store_emails'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check subjects
     * 
     * @return bool 
     */
    function check_subjecs()
    {
        if (!isset(self::$args['subject']) || empty(self::$args['subject'])) {
            return true;
        }

        if (isset(self::$options['dont_store_subjects']) && count(self::$options['dont_store_subjects']) > 0) {
            foreach (self::$options['dont_store_subjects'] as $subject) {
                if (str_contains(self::$args['subject'], $subject)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check messages
     * 
     * @return bool 
     */
    function check_message()
    {
        if (!isset(self::$args['message']) || empty(self::$args['message'])) {
            return true;
        }

        if (isset(self::$options['dont_store_message']) && count(self::$options['dont_store_message']) > 0) {
            foreach (self::$options['dont_store_message'] as $message) {
                if (str_contains(self::$args['message'], $message)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get user info by email
     * @param string|array $emails
     * @return array
     */
    public static function get_user_info_by_email($emails)
    {
        $user_id          = false;
        $formatted_emails = false;

        if (is_array($emails)) {
            foreach ($emails as $email) {
                if ($user_id = email_exists($email)) {
                    $formatted_emails = implode(', ', $emails);
                    break;
                }
            }
        } else {
            $user_id          = email_exists($emails);
            $formatted_emails = $emails;
        }

        return array('user_id' => $user_id, 'emails' => $formatted_emails);
    }
}
