<?php

if (!defined('ABSPATH')) {
    exit;
}

// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * todo:
 * - Bulk editor: Remove items
 * - Add emails to the ignore list
 */
class Email_Man_Template extends WP_List_Table
{
    static $template = null;

    protected static $table_data = [];

    public function __construct()
    {
        $this->plugin_name = 'email_man';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        parent::__construct([
            'singular' => __('Log', 'email_man_domain'),
            'plural'   => __('Logs', 'email_man_domain'),
            'ajax'     => false,
            'screen'   => isset($args['screen']) ? $args['screen'] : null,
        ]);
    }

    public function prepare_items()
    {
        $data     = $this->table_data();
        $this::$table_data = $data;

        $per_page     = $this->get_items_per_page('email_man_per_page');
        $current_page = $this->get_pagenum();
        $total_items  = count($data);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));

        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);

        $this->items = $data;
    }

    /**
     * Add extra markup in the toolbars before or after the list
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */
    public function extra_tablenav($which)
    {
        // Get template filters
        load_template(EMAIL_MAN_PATH . '/templates/table-filters.php', false, array('which' => $which));
        if ($which == 'bottom') {
            // Add modal view
            add_thickbox();

            // Add modal html
            print '<div id="modal-window" style="display:none;"></div>';

            // Json data
            print $this->get_json_data();
        }
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = array(
            'cb'             => __('ID',      'email_man_domain'),
            'email_data'     => __('General', 'email_man_domain'),
            'date'           => __('Date',    'email_man_domain'),
            'subject'        => __('Subject', 'email_man_domain'),
            'body'           => __('Message', 'email_man_domain'),
            'status_name'    => __('Status',  'email_man_domain'),

        );
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return array
     */
    public function get_hidden_columns()
    {
        return array(
            'cb',
        );
    }

    /**
     * A change for the convenience of JS parsing
     * @param mixed $args 
     * @return string 
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * Define the sortable columns
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return array(
            'date'         => array('date', false),
            'subject'      => array('subject', false),
            'status_name'  => array('status', false),
        );
    }

    /**
     * Get the table data
     *
     * @return array
     */
    private function table_data()
    {
        $query = '';

        // Filter: Status
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status = (sanitize_text_field($_GET['status']) == 'true') ? true : false;

            $query .= ' AND `status` = ' . $status;
        }

        // Filter: email
        // Get by User ID or Email full/part
        if (isset($_GET['email']) && !empty($_GET['email'])) {
            $email_value = sanitize_text_field($_GET['email']);

            $query .= ' AND `email_to` LIKE "%' . $email_value . '%"';
        }

        // Filter: subject
        // Get by User ID or Email full/part
        if (isset($_GET['subject']) && !empty($_GET['subject'])) {
            $subject = sanitize_text_field($_GET['subject']);

            $query .= ' AND `subject` LIKE "%' . $subject . '%"';
        }

        // Filter: date
        if ((isset($_GET['date_from']) || isset($_GET['date_to'])) && (!empty($_GET['date_from']) || !empty($_GET['date_to']))) {

            $date_from = (!empty($_GET['date_from'])) ? strtotime($_GET['date_from']) : strtotime('1990-01-01 00:00:00');
            $date_to   = (!empty($_GET['date_to']))   ? strtotime($_GET['date_to']) : strtotime('now +1 day');

            $query .= ' AND `date` BETWEN "' . $date_from . '" AND "' . $date_to . '"';
        }

        return Email_Man_DB::get_items($query);
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  array $item        Data
     * @param  string $column_name - Current column name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'email_data':
                $user = (isset($item['user_id']) && $item['user_id'] > 0) ? get_user_by('ID', $item['user_id']) : null;
                $to   = (isset($user)) ? $item['email_to'] . ' (<a href="/wp-admin/user-edit.php?user_id=' . $user->ID . '" target="_blank">' . $user->display_name . '</a>)' : $item['email_to'];

                print sprintf(
                    '<p><b>%s</b>: %s</p>',
                    __('To', 'wordpress'),
                    $to
                );
                print sprintf(
                    '<p><b>%s</b>: %s</p>',
                    __('From', 'wordpress'),
                    $item['email_from']
                );
                return;
            case 'body':
                return wp_trim_words($item[$column_name], 10, '<br/><br/><a href="#TB_inline?width=600&height=500&inlineId=modal-window" id="emailman-readmore" class="thickbox" data-id="' . $item['id'] . '">' . __('Read more', 'wordpress') . '</a>');
            default:
                return $item[$column_name];
        }
    }

    /**
     * Return json data
     * @return string
     */
    private function get_json_data()
    {
        $table_data = self::$table_data;
        $output = [];
        foreach ($table_data as $data) {
            $output[$data['id']] = $data;
        }

        return '<script> var email_man_data = ' . json_encode($output) . '</script>';
    }
}
