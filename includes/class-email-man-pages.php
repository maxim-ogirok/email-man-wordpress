<?php

if (!defined('ABSPATH')) {
    exit;
}

class Email_Man_Pages
{
    private static $instance = null;

    protected static $table = null;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Registration pages
        add_action('admin_menu', [$this, 'registration_page']);

        // Add action for settings page
        add_action('admin_init', [$this, 'register_settings']);

        // Save pre_page
        add_filter('set-screen-option', [$this, 'save_per_page'], 10, 3);
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

    /**
     * Registration page
     */
    function registration_page()
    {
        $hook = add_menu_page(
            __('Email Man', 'email_man_domain'),
            __('Email Man', 'email_man_domain'),
            'manage_options',
            'email-man',
            [$this, 'page_load'],
            'dashicons-businessman'
        );

        add_submenu_page(
            'email-man',
            __('Settings', 'email_man_domain'),
            __('Settings', 'email_man_domain'),
            'manage_options',
            'email-man-settings',
            [$this, 'settings_page']
        );

        // screen option
        add_action("load-$hook", [$this, 'email_man_screen_options']);
    }

    function email_man_screen_options()
    {
        $option = 'per_page';

        $args = array(
            'label' => __('Per page'),
            'default' => 30,
            'option' => 'email_man_per_page'
        );

        add_screen_option($option, $args);
        self::$table = new Email_Man_Template();
    }


    /**
     * Page callback
     * @return void
     */
    function page_load()
    {
        $table = new Email_Man_Template();
        $table->prepare_items();
        $table->screen_options();

?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2>Email Man</h2>
            <?php $table->display(); ?>
        </div>
    <?php
    }

    /**
     * Register settings page
     * @return void
     */
    function settings_page()
    {
    ?>
        <h1>Settings</h1>
        <form id="email-man-settings-form" action="options.php" method="post">
            <?php
            settings_fields('email_man_plugin_options');
            do_settings_sections('email_man_plugin_section'); ?>
            <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
        </form>
<?php
    }

    /**
     * Register settings
     */
    function register_settings()
    {
        register_setting('email_man_plugin_options', 'email_man_plugin_options', [$this, 'email_man_plugin_options_validate']);

        // Don't store data with
        add_settings_section('email_man_setting_header', __("Don't store data with", 'email_man_domain'), array(), 'email_man_plugin_section');
        add_settings_field('dont_store_emails', __('With emails:', 'email_man_domain'), [$this, 'dont_store_emails'], 'email_man_plugin_section', 'email_man_setting_header');
        add_settings_field('dont_store_subjects', __('With subjects:', 'email_man_domain'), [$this, 'dont_store_subjects'], 'email_man_plugin_section', 'email_man_setting_header');
        add_settings_field('dont_store_message', __('With message:', 'email_man_domain'), [$this, 'dont_store_message'], 'email_man_plugin_section', 'email_man_setting_header');
    }

    /**
     * Setting: emails
     * @return void
     */
    function dont_store_emails()
    {
        $options = get_option('email_man_plugin_options');
        $values = (isset($options['dont_store_emails'])) ? $options['dont_store_emails'] : '';

        echo     '<select class="form-control select2" id="dont_store_emails" name="email_man_plugin_options[dont_store_emails][]" multiple="multiple">';
        if (!empty($values) && count($values) > 0) {
            foreach ($values as $value) {
                print '<option selected="selected">' . $value . '</option>';
            }
        }
        echo    '</select>';
    }

    /**
     * Setting: subjects 
     * @return void
     */
    function dont_store_subjects()
    {
        $options = get_option('email_man_plugin_options');
        $values = (isset($options['dont_store_subjects'])) ? $options['dont_store_subjects'] : '';

        echo     '<select class="form-control select2" id="dont_store_subjects" name="email_man_plugin_options[dont_store_subjects][]" multiple="multiple">';
        if (!empty($values) && count($values) > 0) {
            foreach ($values as $value) {
                print '<option selected="selected">' . $value . '</option>';
            }
        }
        echo    '</select>';
    }

    /**
     * Settings: message contains
     * @return void
     */
    function dont_store_message()
    {
        $options = get_option('email_man_plugin_options');
        $values = (isset($options['dont_store_message'])) ? $options['dont_store_message'] : '';

        echo     '<select class="form-control select2" id="dont_store_message" name="email_man_plugin_options[dont_store_message][]" multiple="multiple">';
        if (!empty($values) && count($values) > 0) {
            foreach ($values as $value) {
                print '<option selected="selected">' . $value . '</option>';
            }
        }
        echo    '</select>';
    }

    /**
     * Data validate
     */
    function email_man_plugin_options_validate($input)
    {
        return $input;
    }


    /**
     * Save per_page settings
     * @param mixed $status 
     * @param string $option 
     * @param int $value 
     * @return void 
     */
    public static function save_per_page($status, $option, $value)
    {
        return ($option === 'email_man_per_page') ? (int) $value : $status;
    }
}

$Email_Man_Pages = Email_Man_Pages::get_instance();
