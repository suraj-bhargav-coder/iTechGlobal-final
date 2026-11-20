<?php
/**
 * Contact Form Template
 * Display contact form with all fields
 */

?>
<div class="itechglobal-contact-form-wrapper">
    <form id="itechglobal-contact-form" class="itechglobal-contact-form" method="POST">
        
        <div class="form-group">
            <label for="name">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control" placeholder="your.email@example.com" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control" placeholder="+91 (555) 000-0000">
        </div>

        <div class="form-group">
            <label for="company">Company Name</label>
            <input type="text" id="company" name="company" class="form-control" placeholder="Your Company">
        </div>

        <div class="form-group">
            <label for="service">Service Interested In</label>
            <select id="service" name="service" class="form-control">
                <option value="">Select a Service</option>
                <option value="EDI Integration">EDI Integration</option>
                <option value="Cloud Modernization">Cloud Modernization</option>
                <option value="Product Engineering">Product Engineering</option>
                <option value="IT Consulting">IT Consulting</option>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject">Subject <span class="required">*</span></label>
            <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this about?" required>
        </div>

        <div class="form-group">
            <label for="message">Message <span class="required">*</span></label>
            <textarea id="message" name="message" class="form-control" rows="6" placeholder="Please tell us more about your enquiry..." required></textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Send Message</button>
            <span class="form-loading" aria-live="polite">
                <span class="spinner"></span> Sending...
            </span>
        </div>

        <div class="form-message" aria-live="polite"></div>

        <?php wp_nonce_field('itechglobal_contact_form_nonce', 'nonce', false); ?>
    </form>
</div>
