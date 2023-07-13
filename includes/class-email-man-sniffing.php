<?php

if (!defined('ABSPATH')) {
    exit;
}

class Email_Man_Sniffing
{
    private static $instance = null;

    protected static $email_from = null;
    protected static $email_from_name = null;

    function __construct()
    {

        // Sniffing sending email
        add_action('wp_mail', [$this, 'sniffing'], 10, 9999);

        // Sniffing email from
        add_filter('wp_mail_from', [$this, 'sniffing_from']);

        // Sniffing name from
        add_filter('wp_mail_from_name', [$this, 'sniffing_from_name']);

        // Set status success
        add_action('wp_mail_succeeded', [$this, 'sniffing_success']);

        // Set status failed
        add_action('wp_mail_failed', [$this, 'sniffing_failed']);
    }

    /**
     * Get instance
     * @return mixed 
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function sniffing()
    {
    }

    /**
     * Success
     * @param array $mail_data {
     *     An array containing the email recipient(s), subject, message, headers, and attachments.
     *
     *     @param string[] $to          Email addresses to send message.
     *     @param string   $subject     Email subject.
     *     @param string   $message     Message contents.
     *     @param string[] $headers     Additional headers.
     *     @param string[] $attachments Paths to files to attach.
     * }
     */
    public static function sniffing_success($mail_data)
    {
        $email_info = Email_Man_Logic::get_user_info_by_email($mail_data['to']);

        $mail_data['status']      = true;
        $mail_data['status_name'] = null;
        $mail_data['user_id']     = $email_info['user_id'];
        $mail_data['from']        = self::$email_from;

        self::processing($mail_data);
    }

    /**
     * Failed
     * @param WP_Error $error A WP_Error object with the PHPMailer\PHPMailer\Exception message, and an array
     *                        containing the mail recipient, subject, message, headers, and attachments.
     */
    public static function sniffing_failed($mail_data)
    {
        $email_args  = $mail_data->error_data['wp_mail_failed'];
        $email_info  = Email_Man_Logic::get_user_info_by_email($email_args['to']);

        $status      = implode(' ', $mail_data->errors['wp_mail_failed']);
        $to          = $email_info['emails'];
        $subject     = $email_args['subject'];
        $message     = $email_args['message'];
        $headers     = $email_args['headers'];
        $attachments = $email_args['attachments'];
        $user_id     = $email_info['user_id'];

        $args = array(
            'from'        => self::$email_from,
            'to'          => $to,
            'subject'     => $subject,
            'headers'     => $headers,
            'message'     => $message,
            'attachments' => $attachments,
            'status'      => false,
            'status_name' => $status,
            'user_id'     => $user_id,
        );

        self::processing($args);
    }

    /**
     * Processing
     * @param array $args{
     *     @param string[] $from        Email addresses from send message.
     *     @param string[] $to          Email addresses to send message.
     *     @param string   $subject     Email subject.
     *     @param string[] $headers     Additional headers.
     *     @param string   $message     Message contents.
     *     @param string[] $attachments Paths to files to attach.
     *     @param bool     $status      Status.
     *     @param string   $status_name Status name.
     *     @param int      $user_id 	   User ID.
     * }
     */
    public static function processing($args)
    {
        $logics = new Email_Man_Logic($args);
        if ($logics->processing()) {
            Email_Man_DB::create_item($args);
        }
    }

    /**
     * Set email from
     * 
     * @param string $from_email 
     * @return string 
     */
    public static function sniffing_from($from_email = null)
    {
        self::$email_from = $from_email;
        return $from_email;
    }

    /**
     * Set name from
     * 
     * @param string $from_name 
     * @return string
     */
    public static function sniffing_from_name($from_name = null)
    {
        self::$email_from_name = $from_name;
        return $from_name;
    }
}

$Email_Man_Sniffing = Email_Man_Sniffing::get_instance();
