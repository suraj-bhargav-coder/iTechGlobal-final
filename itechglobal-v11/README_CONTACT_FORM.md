# iTech Global Contact Form with Database Integration

This contact form system integrates with WordPress to store contact enquiries in a custom database table.

## Features

✅ **Contact Form Database** - All enquiries stored in WordPress database
✅ **AJAX Form Submission** - Non-blocking, real-time form submission
✅ **Email Notifications** - Automatic emails to admin and user confirmation
✅ **WordPress Admin Panel** - View all enquiries in a dedicated admin menu
✅ **Responsive Design** - Works perfectly on mobile and desktop
✅ **Security** - Nonce verification, input sanitization, and data validation
✅ **User Tracking** - Tracks IP address and user agent information

## Installation

1. **Upload the theme folder** to your WordPress installation at `/wp-content/themes/`
2. **Activate the theme** from WordPress admin dashboard
3. The contact database table will be **automatically created** on theme activation

## Database Structure

The system creates a custom table (`wp_contact_enquiries`) with the following fields:

- **id** - Unique identifier
- **name** - Visitor's full name
- **email** - Email address
- **phone** - Phone number (optional)
- **subject** - Subject line
- **message** - Enquiry message
- **company** - Company name (optional)
- **service** - Service interested in (optional)
- **ip_address** - Visitor's IP address
- **user_agent** - Browser information
- **submitted_at** - Timestamp of submission
- **read_status** - Mark enquiry as read/unread

## Files Included

### Core Files
- `functions.php` - Main theme functions
- `style.css` - Theme stylesheet

### New Contact Form Module Files

#### `/inc/` Directory
- **contact-db.php** - Database table creation and operations
  - Creates custom table on theme activation
  - Functions to save, retrieve, and delete enquiries
  - Database query helpers

- **contact-form-handler.php** - AJAX form submission handler
  - Handles form submission via AJAX
  - Email notifications to admin and user
  - Input validation and sanitization
  - Enqueues scripts and styles

- **contact-admin-page.php** - WordPress admin interface
  - Main enquiries list page with pagination
  - Individual enquiry detail page
  - Ability to mark as read/delete
  - Dashboard menu integration

#### `/template-parts/` Directory
- **contact-form.php** - Contact form HTML template
  - Form fields (name, email, phone, company, service, subject, message)
  - Built-in CSS styling
  - AJAX submission handling

#### `/assets/js/` Directory
- **contact-form.js** - JavaScript for form handling
  - AJAX submission
  - Form validation
  - Error/success messaging
  - Loading states

## Usage

### Display Contact Form on a Page

To display the contact form on any WordPress page or template, use:

```php
<?php get_template_part('template-parts/contact-form'); ?>
```

Or in your page template:

```php
<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <div class="contact-section">
            <h2><?php the_title(); ?></h2>
            <?php the_content(); ?>
            <?php get_template_part('template-parts/contact-form'); ?>
        </div>
        <?php
    }
}
?>
```

### Access Admin Panel

1. Log in to WordPress admin
2. Look for **"Contact Enquiries"** in the left sidebar
3. View all submitted enquiries
4. Click on any enquiry to view full details
5. Mark as read or delete enquiries as needed

## Form Fields

The contact form includes the following fields:

| Field | Required | Type | Notes |
|-------|----------|------|-------|
| Full Name | ✓ | Text | Visitor's name |
| Email Address | ✓ | Email | Must be valid email |
| Phone Number | ✗ | Tel | Optional contact number |
| Company Name | ✗ | Text | Optional company info |
| Service Interested In | ✗ | Select | EDI, Cloud, Engineering, etc. |
| Subject | ✓ | Text | Enquiry subject |
| Message | ✓ | Textarea | Detailed enquiry message |

## Email Notifications

### Admin Notification
When a visitor submits the form, the site admin receives an email with:
- Visitor's name, email, phone
- Company and service information
- Full enquiry message
- Link to view in WordPress admin

### User Confirmation
The visitor automatically receives a confirmation email:
- Acknowledgment of their submission
- Company name and message
- Promise to respond soon

## Customization

### Change Form Fields

Edit `/template-parts/contact-form.php` to modify form fields:

```php
<div class="form-group">
    <label for="custom_field">Your Label</label>
    <input type="text" id="custom_field" name="custom_field" class="form-control">
</div>
```

Then update `/inc/contact-form-handler.php` to handle the new field:

```php
'custom_field' => sanitize_text_field($_POST['custom_field'] ?? ''),
```

### Modify Email Template

Edit the email content in `/inc/contact-form-handler.php`:

```php
$message = sprintf(
    "Your custom email template here\n\n" .
    "Name: %s\n" .
    "Email: %s\n",
    $data['name'],
    $data['email']
);
```

### Customize Styling

The form comes with basic styling. You can customize CSS in:
- `/template-parts/contact-form.php` (inline styles)
- `/style.css` (theme stylesheet)

Example custom CSS:

```css
.itechglobal-contact-form-wrapper {
    background: #ffffff;
    padding: 50px;
}

.form-control {
    border: 2px solid #0073aa;
}

.btn-primary {
    background: #e74c3c;
}
```

## Functions Reference

### Database Functions

#### `itechglobal_save_contact_enquiry($data)`
Saves a new contact enquiry to database
```php
$enquiry_id = itechglobal_save_contact_enquiry(array(
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'message' => 'Enquiry message'
));
```

#### `itechglobal_get_contact_enquiries($limit, $offset, $order)`
Retrieves paginated enquiries
```php
$enquiries = itechglobal_get_contact_enquiries(50, 0, 'DESC');
```

#### `itechglobal_get_contact_enquiry($id)`
Gets a single enquiry by ID
```php
$enquiry = itechglobal_get_contact_enquiry(1);
```

#### `itechglobal_mark_enquiry_read($id)`
Marks an enquiry as read
```php
itechglobal_mark_enquiry_read(1);
```

#### `itechglobal_delete_contact_enquiry($id)`
Deletes an enquiry
```php
itechglobal_delete_contact_enquiry(1);
```

#### `itechglobal_get_unread_enquiries_count()`
Gets count of unread enquiries
```php
$unread = itechglobal_get_unread_enquiries_count();
```

## Security

The system implements:
- ✓ **Nonce verification** - CSRF protection
- ✓ **Input sanitization** - All data sanitized before saving
- ✓ **Email validation** - Checks valid email format
- ✓ **Capability checks** - Admin pages check user permissions
- ✓ **Data escaping** - Output properly escaped for display

## Troubleshooting

### Table Not Created
- Ensure theme is activated
- Check WordPress database user has CREATE TABLE permissions
- Look in WordPress error log at `/wp-content/debug.log`

### Emails Not Sending
- Check WordPress mail configuration
- Verify admin email in WordPress settings
- Check spam/junk folder
- Enable WP_DEBUG to see errors

### Form Not Submitting
- Verify jQuery is loaded
- Check browser console for JavaScript errors
- Ensure nonce is present in form
- Check AJAX URL is correct

### Missing Admin Menu
- Verify you're logged in as admin
- Try deactivating/reactivating theme
- Check for PHP errors in debug.log

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review WordPress documentation
3. Check theme console for errors

## Version History

- **v1.0** - Initial release with full contact database integration

## License

This theme and all included code is provided as-is for use on the iTech Global website.


## Favicon
The theme serves its bundled avicon.ico at the site root (/favicon.ico) through WordPress and declares that URL in the page head. The route works without a permalink flush. Deploy the updated theme package for the change to reach the live site.


## Site icon
The browser favicon, WordPress site identity markup, and web app manifest use ssets/itech-site-icon.png (512 � 512). Deploy the theme update, then clear page and CDN caches; Google may take time to recrawl the icon.


## Robots and sitemap
Public robots output preserves crawl rules and advertises one sitemap at /sitemap.xml, which is served by the site's SEO sitemap provider. Deploy the theme update for WordPress-generated obots.txt; the included obots.txt mirrors this configuration.


## Sitemap data
/sitemap.xml is generated by the site's SEO plugin, not a static theme file. It currently lists the homepage and published post URLs plus category and tag archives; the listed URLs respond successfully. Keep the SEO plugin active and submit /sitemap.xml in Search Console after deployment.
