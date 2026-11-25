<?php
/**
 * Contact Form AJAX Handler
 * Handles form submission via AJAX
 */

// AJAX handler for contact form submission
add_action('wp_ajax_nopriv_submit_contact_form', 'itechglobal_handle_contact_form');
add_action('wp_ajax_submit_contact_form', 'itechglobal_handle_contact_form');

function itechglobal_handle_contact_form() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'itechglobal_contact_form_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        wp_die();
    }

    // Validate required fields
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $subject_text = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $message_text = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $company = isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
    $service = isset($_POST['service']) ? sanitize_text_field(wp_unslash($_POST['service'])) : '';

    if (empty($name) || empty($email) || empty($message_text)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields'));
        wp_die();
    }

    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address'));
        wp_die();
    }

    // Prepare data
    $data = array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject_text,
        'message' => $message_text,
        'company' => $company,
        'service' => $service,
    );

    // Save to database
    $enquiry_id = itechglobal_save_contact_enquiry($data);

    if ($enquiry_id) {
        // Send email notification to admin
        $admin_email = get_theme_mod('itechglobal_contact_email', 'info@itechglobal.in');
        if (!is_email($admin_email)) {
            $admin_email = 'info@itechglobal.in';
        }
        $site_name = get_bloginfo('name');
        
        $subject = sprintf(
            '[%s] New Contact Enquiry from %s',
            $site_name,
            $data['name']
        );

        $message = sprintf(
            "New Contact Enquiry Received:\n\n" .
            "Name: %s\n" .
            "Email: %s\n" .
            "Phone: %s\n" .
            "Company: %s\n" .
            "Service: %s\n" .
            "Subject: %s\n\n" .
            "Message:\n%s\n\n" .
            "---\n" .
            "View in Dashboard: %s",
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['company'],
            $data['service'],
            $data['subject'],
            $data['message'],
            admin_url('admin.php?page=contact-enquiries')
        );

        wp_mail($admin_email, $subject, $message);

        // Send confirmation email to user
        $user_subject = sprintf('We received your message - %s', $site_name);
        $user_message = sprintf(
            "Thank you for contacting us!\n\n" .
            "We have received your enquiry and will get back to you soon.\n\n" .
            "Best regards,\n%s",
            $site_name
        );
        wp_mail($data['email'], $user_subject, $user_message);

        wp_send_json_success(array(
            'message' => 'Thank you! Your message has been sent successfully. We will contact you soon.',
            'enquiry_id' => $enquiry_id
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to save your message. Please try again.'));
    }

    wp_die();
}

// Enqueue contact form scripts and styles
add_action('wp_enqueue_scripts', 'itechglobal_enqueue_contact_form_assets');

function itechglobal_enqueue_contact_form_assets() {
    if (!is_front_page() && !is_page_template('page-home.php')) {
        return;
    }

    $script_path = get_template_directory() . '/assets/js/contact-form.js';
    wp_enqueue_script(
        'itechglobal-contact-form',
        get_template_directory_uri() . '/assets/js/contact-form.js',
        array('jquery'),
        file_exists($script_path) ? (string) filemtime($script_path) : null,
        true
    );
    
    wp_localize_script('itechglobal-contact-form', 'itechGlobalContactForm', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('itechglobal_contact_form_nonce'),
    ));
}
