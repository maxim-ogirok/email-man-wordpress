<div class="alignleft actions">
    <form id="email-man-<?php print $args['which']; ?>" name="email-man-<?php print $args['which']; ?>" class="wp-list-table__extra-tablenav-form" method="get">
        <input type="hidden" name="page" value="email-man">

        <select name="status">
            <option value=""><?php print __('Status'); ?></option>
            <option value="true" <?php print (isset($_GET['status']) && $_GET['status'] == 'true') ? ' selected="selected"' : ''; ?>><?php print __('Successfully', 'email_man_domain'); ?></option>
            <option value="false" <?php print (isset($_GET['status']) && $_GET['status'] == 'false') ? ' selected="selected"' : ''; ?>><?php print __('Unsuccessfully', 'email_man_domain'); ?></option>
        </select>

        <input type="text" name="email" class="" value="<?php print (isset($_GET['email'])) ? sanitize_text_field($_GET['email']) : ''; ?>" placeholder="<?php print __('Email'); ?>">
        <input type="text" name="subject" class="" value="<?php print (isset($_GET['subject'])) ? sanitize_text_field($_GET['subject']) : ''; ?>" placeholder="<?php print __('Subject'); ?>">

        <label for="date_from" class=" date_from label-date"><?php print __('From'); ?>:</label>
        <input type="date" id="date_from" name=" date_from" value="<?php print (array_key_exists('date_from', $_GET)) ? sanitize_text_field($_GET['date_from']) : ''; ?>" pattern="\d{4}-\d{2}-\d{2}">

        <label for="date_to" class=" date_to label-date"><?php print __('To', 'wordpress'); ?>:</label>
        <input type="date" id="date_to" name=" date_to" value="<?php print (array_key_exists('date_to', $_GET)) ? sanitize_text_field($_GET['date_to']) : ''; ?>" pattern="\d{4}-\d{2}-\d{2}">

        <input type="submit" class="button" value="<?php print __('Filter', 'wordpress'); ?>">
    </form>
</div>