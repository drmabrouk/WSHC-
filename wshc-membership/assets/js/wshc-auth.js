jQuery(document).ready(function($) {
    const auth = {
        currentStep: 1,
        totalSteps: 3,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            const self = this;

            // Switch between main views
            $('.wshc-switch').on('click', function(e) {
                e.preventDefault();
                const target = $(this).data('target');
                self.switchView(target);
            });

            // Toggle Password Visibility
            $(document).on('click', '.wshc-toggle-password', function() {
                const $input = $(this).siblings('input');
                const type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
                $(this).toggleClass('dashicons-visibility dashicons-hidden');
            });

            // Registration Step Navigation
            $('.wshc-next-step').on('click', function() {
                if (self.validateStep(self.currentStep)) {
                    self.goToStep(self.currentStep + 1);
                }
            });

            $('.wshc-prev-step').on('click', function() {
                self.goToStep(self.currentStep - 1);
            });

            // Real-time Validation
            $('#reg-email').on('blur', function() {
                self.checkAvailability('email', $(this).val());
            });

            $('#reg-user').on('blur', function() {
                self.checkAvailability('username', $(this).val());
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

            if (target === 'register') {
                $('#wshc-reg-progress').show();
                this.goToStep(1);
            } else {
                $('#wshc-reg-progress').hide();
            }
        },

        goToStep: function(step) {
            this.currentStep = step;
            $('.wshc-reg-step').removeClass('active').hide();
            $(`.wshc-reg-step[data-step="${step}"]`).fadeIn(300).addClass('active');

            const progress = (step / this.totalSteps) * 100;
            $('.wshc-progress-bar').css('width', progress + '%');
        },

        validateStep: function(step) {
            let valid = true;
            const $stepContainer = $(`.wshc-reg-step[data-step="${step}"]`);

            $stepContainer.find('input[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    valid = false;
                } else {
                    $(this).removeClass('error');
                }
            });

            if (step === 1) {
                const pass = $('#reg-pass').val();
                const confirm = $('#reg-confirm').val();
                if (pass !== confirm) {
                    $('#reg-confirm').addClass('error');
                    alert('Passwords do not match.');
                    valid = false;
                }
            }

            return valid;
        },

        checkAvailability: function(type, value) {
            if (!value) return;
            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: {
                    action: 'wshc_check_availability',
                    security: wshc_vars.nonce,
                    type: type,
                    value: value
                },
                success: function(response) {
                    if (!response.success) {
                        $(`#reg-${type === 'username' ? 'user' : 'email'}`).addClass('error');
                        console.log(response.data.message);
                    } else {
                        $(`#reg-${type === 'username' ? 'user' : 'email'}`).removeClass('error');
                    }
                }
            });
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

            // Always "Remember Me" for 30 days (handled server-side by presence of param)
            if (formId === 'wshc-login-form') {
                formData.push({ name: 'remember', value: 'true' });
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
                            // After OTP success, the user is activated. The server can also log them in or we trigger login.
                            // For "Instant Onboarding", we'll redirect to dashboard/account immediately.
                            setTimeout(() => {
                                window.location.href = wshc_vars.dashboard_url;
                            }, 1500);
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
