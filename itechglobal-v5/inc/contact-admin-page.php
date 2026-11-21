<?php
/**
 * Contact Enquiries Admin Page
 * Display contact enquiries in WordPress admin
 */

// Add admin menu
add_action('admin_menu', 'itechglobal_add_contact_menu');

function itechglobal_add_contact_menu() {
    add_menu_page(
        'Contact Enquiries',
        'Contact Enquiries',
        'manage_options',
        'contact-enquiries',
        'itechglobal_contact_enquiries_page',
        'dashicons-email',
        26
    );

    add_submenu_page(
        'contact-enquiries',
        'All Enquiries',
        'All Enquiries',
        'manage_options',
        'contact-enquiries',
        'itechglobal_contact_enquiries_page'
    );
}

// Display enquiries page
function itechglobal_contact_enquiries_page() {
    global $wpdb;
    
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
    $per_page = 20;
    $offset = ($paged - 1) * $per_page;

    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('delete_enquiry_' . $_GET['id']);
        itechglobal_delete_contact_enquiry(intval($_GET['id']));
        echo '<div class="notice notice-success"><p>Enquiry deleted successfully.</p></div>';
    }

    // Handle mark as read
    if (isset($_GET['action']) && $_GET['action'] === 'read' && isset($_GET['id'])) {
        check_admin_referer('read_enquiry_' . $_GET['id']);
        itechglobal_mark_enquiry_read(intval($_GET['id']));
    }

    // Get enquiries
    $enquiries = itechglobal_get_contact_enquiries($per_page, $offset);
    $table_name = $wpdb->prefix . 'contact_enquiries';
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total / $per_page);

    ?>
    <div class="wrap">
        <h1>Contact Enquiries</h1>
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <span class="displaying-num"><?php echo $total; ?> items</span>
            </div>
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => admin_url('admin.php?page=contact-enquiries&paged=%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $paged,
                ));
                ?>
            </div>
            <br class="clear">
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column" width="20%">Name</th>
                    <th scope="col" class="manage-column" width="20%">Email</th>
                    <th scope="col" class="manage-column" width="15%">Phone</th>
                    <th scope="col" class="manage-column" width="15%">Subject</th>
                    <th scope="col" class="manage-column" width="15%">Date</th>
                    <th scope="col" class="manage-column" width="15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($enquiries) {
                    foreach ($enquiries as $enquiry) {
                        $read_class = $enquiry->read_status ? '' : ' style="font-weight: bold; background-color: #fffbcc;"';
                        ?>
                        <tr<?php echo $read_class; ?>>
                            <td><?php echo esc_html($enquiry->name); ?></td>
                            <td><?php echo esc_html($enquiry->email); ?></td>
                            <td><?php echo esc_html($enquiry->phone); ?></td>
                            <td><?php echo esc_html($enquiry->subject); ?></td>
                            <td><?php echo esc_html($enquiry->submitted_at); ?></td>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=contact-enquiry-detail&id=' . $enquiry->id)); ?>" class="button button-small">View</a>
                                <?php
                                $delete_url = wp_nonce_url(
                                    admin_url('admin.php?page=contact-enquiries&action=delete&id=' . $enquiry->id),
                                    'delete_enquiry_' . $enquiry->id
                                );
                                ?>
                                <a href="<?php echo esc_url($delete_url); ?>" class="button button-small button-link-delete" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No enquiries found.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add detail page
add_action('admin_menu', function() {
    add_submenu_page(
        'contact-enquiries',
        'Enquiry Detail',
        'Detail',
        'manage_options',
        'contact-enquiry-detail',
        'itechglobal_contact_enquiry_detail_page'
    );
});

function itechglobal_contact_enquiry_detail_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    if (!isset($_GET['id'])) {
        wp_die('Invalid enquiry ID');
    }

    $id = intval($_GET['id']);
    $enquiry = itechglobal_get_contact_enquiry($id);

    if (!$enquiry) {
        wp_die('Enquiry not found');
    }

    // Mark as read
    if (!$enquiry->read_status) {
        itechglobal_mark_enquiry_read($id);
    }

    ?>
    <div class="wrap">
        <h1>Contact Enquiry #<?php echo esc_html($enquiry->id); ?></h1>

        <div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3>Enquiry Details</h3>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label>Name</label></th>
                    <td><?php echo esc_html($enquiry->name); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>Email</label></th>
                    <td><a href="mailto:<?php echo esc_attr($enquiry->email); ?>"><?php echo esc_html($enquiry->email); ?></a></td>
                </tr>
                <tr>
                    <th scope="row"><label>Phone</label></th>
                    <td><?php echo esc_html($enquiry->phone ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>Company</label></th>
                    <td><?php echo esc_html($enquiry->company ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>Service Interested</label></th>
                    <td><?php echo esc_html($enquiry->service ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>Subject</label></th>
                    <td><?php echo esc_html($enquiry->subject ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>Submitted On</label></th>
                    <td><?php echo esc_html($enquiry->submitted_at); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label>IP Address</label></th>
                    <td><?php echo esc_html($enquiry->ip_address ?: 'N/A'); ?></td>
                </tr>
            </table>

            <h3>Message</h3>
            <div style="background: #f9f9f9; padding: 15px; border-radius: 3px; border-left: 4px solid #0073aa;">
                <?php echo wp_kses_post(nl2br($enquiry->message)); ?>
            </div>

            <hr style="margin: 30px 0;">

            <div>
                <?php
                $delete_url = wp_nonce_url(
                    admin_url('admin.php?page=contact-enquiries&action=delete&id=' . $enquiry->id),
                    'delete_enquiry_' . $enquiry->id
                );
                ?>
                <a href="<?php echo esc_url($delete_url); ?>" class="button button-link-delete" onclick="return confirm('Are you sure?');">Delete Enquiry</a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=contact-enquiries')); ?>" class="button">Back to List</a>
            </div>
        </div>
    </div>
    <?php
}
