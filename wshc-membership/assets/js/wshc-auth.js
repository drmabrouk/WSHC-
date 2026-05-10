jQuery(document).ready(function($) {
    const auth = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            const self = this;

            // Switch between auth views
            $('.wshc-switch').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                self.switchView(target);
            });

            // Toggle Password Visibility
            $('.wshc-toggle-password').on('click', function() {
                const $input = $(this).siblings('input');
                const type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
                $(this).toggleClass('dashicons-visibility dashicons-hidden');
            });

            // Handle AJAX forms
            $('#wshc-login-form, #wshc-register-form, #wshc-recover-form, #wshc-verify-form').on('submit', function(e) {
                e.preventDefault();
                self.handleFormSubmit($(this));
            });
        },

        switchView: function(target) {
            $('.wshc-auth-view').hide();
            $('#wshc-' + target + '-view').fadeIn(300);
            $('#wshc-api-response').empty();
        },

        handleFormSubmit: function($form) {
            const self = this;
            const $resp = $('#wshc-api-response');
            let action = '';
            const formId = $form.attr('id');

            if (formId === 'wshc-login-form') action = 'wshc_login';
            if (formId === 'wshc-register-form') action = 'wshc_register';
            if (formId === 'wshc-recover-form') action = 'wshc_recover';
            if (formId === 'wshc-verify-form') action = 'wshc_verify_otp';

            $resp.html('<p class="loading">Processing...</p>');
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true);

            const formData = $form.serializeArray();
            formData.push({ name: 'action', value: action });
            formData.push({ name: 'security', value: wshc_vars.nonce });

            // Handle "Remember Me"
            if (formId === 'wshc-login-form') {
                const remember = $form.find('input[name="remember"]').is(':checked');
                formData.push({ name: 'remember', value: remember });
            }

            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $resp.html('<p class="success">' + response.data.message + '</p>');

                        if (action === 'wshc_register') {
                            $('#wshc-verify-user-id').val(response.data.user_id);
                            setTimeout(() => self.switchView('verify'), 1500);
                        } else if (action === 'wshc_verify_otp') {
                            setTimeout(() => self.switchView('login'), 2000);
                        } else if (response.data.redirect) {
                            setTimeout(() => window.location.href = response.data.redirect, 1000);
                        }

                        if (action !== 'wshc_login') $form[0].reset();
                    } else {
                        $resp.html('<p class="error">' + response.data.message + '</p>');
                    }
                    $btn.prop('disabled', false);
                },
                error: function() {
                    $resp.html('<p class="error">An unexpected error occurred.</p>');
                    $btn.prop('disabled', false);
                }
            });
        }
    };

    auth.init();
});
