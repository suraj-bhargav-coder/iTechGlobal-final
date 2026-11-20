/**
 * Contact Form Handler
 * Handles form submission via AJAX and displays feedback
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle contact form submission
        $('#itechglobal-contact-form, form.contact-form').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const $loading = $form.find('.form-loading');
            const $message = $form.find('.form-message');

            // Validate form
            if (!$form[0].checkValidity()) {
                $message.removeClass('success error').addClass('error')
                    .text('Please fill in all required fields correctly.')
                    .show();
                return false;
            }

            // Disable submit button and show loading
            $submitBtn.prop('disabled', true);
            $loading.show();
            $message.hide();

            // Prepare form data
            const formData = {
                action: 'submit_contact_form',
                nonce: $form.find('input[name="nonce"]').val() || itechGlobalContactForm.nonce,
                name: $form.find('[name="name"]').val() || '',
                email: $form.find('[name="email"]').val() || '',
                phone: $form.find('[name="phone"]').val() || '',
                subject: $form.find('[name="subject"]').val() || '',
                message: $form.find('[name="message"]').val() || '',
                company: $form.find('[name="company"]').val() || '',
                service: $form.find('[name="service"]').val() || '',
            };

            // Send AJAX request
            $.ajax({
                url: itechGlobalContactForm.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $message.removeClass('error').addClass('success')
                            .html(response.data.message)
                            .show();

                        // Reset form
                        $form[0].reset();

                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: $message.offset().top - 100
                        }, 300);

                        // Hide message after 5 seconds
                        setTimeout(function() {
                            $message.fadeOut();
                        }, 5000);
                    } else {
                        // Show error message
                        $message.removeClass('success').addClass('error')
                            .text(response.data.message || 'An error occurred. Please try again.')
                            .show();
                    }
                },
                error: function() {
                    $message.removeClass('success').addClass('error')
                        .text('An error occurred. Please try again later.')
                        .show();
                },
                complete: function() {
                    // Re-enable submit button and hide loading
                    $submitBtn.prop('disabled', false);
                    $loading.hide();
                }
            });

            return false;
        });

        // Clear message on input focus
        $('#itechglobal-contact-form, form.contact-form').find('input, textarea, select').on('focus', function() {
            $(this).closest('form').find('.form-message').fadeOut();
        });
    });

})(jQuery);
