<?php
function itechglobal_create_contact_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(20),
        subject varchar(255),
        message longtext NOT NULL,
        company varchar(255),
        service varchar(255),
        ip_address varchar(100),
        user_agent longtext,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
        read_status tinyint(1) DEFAULT 0,
        PRIMARY KEY (id),
        INDEX (email),
        INDEX (submitted_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_switch_theme', 'itechglobal_create_contact_table');

function itechglobal_contact_table_exists() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';
    return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name))) === $table_name;
}

// Safely create table after WordPress is fully loaded
add_action('init', function() {
    if (get_option('itechglobal_contact_table_created') === false || !itechglobal_contact_table_exists()) {
        itechglobal_create_contact_table();
        update_option('itechglobal_contact_table_created', 1);
    }
});

function itechglobal_save_contact_enquiry($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';

    if (!itechglobal_contact_table_exists()) {
        itechglobal_create_contact_table();
    }

    $result = $wpdb->insert(
        $table_name,
        array(
            'name'       => sanitize_text_field($data['name']),
            'email'      => sanitize_email($data['email']),
            'phone'      => sanitize_text_field($data['phone'] ?? ''),
            'subject'    => sanitize_text_field($data['subject'] ?? ''),
            'message'    => sanitize_textarea_field($data['message']),
            'company'    => sanitize_text_field($data['company'] ?? ''),
            'service'    => sanitize_text_field($data['service'] ?? ''),
            'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    return $result ? $wpdb->insert_id : false;
}

function itechglobal_get_contact_enquiries($limit = 50, $offset = 0, $order = 'DESC') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

    if (!itechglobal_contact_table_exists()) {
        itechglobal_create_contact_table();
    }

    $query = $wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY submitted_at $order LIMIT %d OFFSET %d",
        $limit,
        $offset
    );

    return $wpdb->get_results($query);
}

function itechglobal_get_contact_enquiry($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';

    if (!itechglobal_contact_table_exists()) {
        itechglobal_create_contact_table();
    }

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $id
    ));
}

function itechglobal_mark_enquiry_read($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';

    return $wpdb->update(
        $table_name,
        array('read_status' => 1),
        array('id' => $id),
        array('%d'),
        array('%d')
    );
}

function itechglobal_delete_contact_enquiry($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';

    return $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );
}

function itechglobal_get_unread_enquiries_count() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_enquiries';

    if (!itechglobal_contact_table_exists()) {
        itechglobal_create_contact_table();
    }

    return $wpdb->get_var(
        "SELECT COUNT(*) FROM $table_name WHERE read_status = 0"
    );
}
