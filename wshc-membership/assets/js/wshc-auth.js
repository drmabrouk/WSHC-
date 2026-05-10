jQuery(document).ready(function($) {
    const auth = {
        currentStep: 1,
        totalSteps: 3,
        userId: 0,

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
            $(document).on('click', '.wshc-toggle-password', function() {
                const $input = $(this).siblings('input');
                const type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
                $(this).toggleClass('dashicons-visibility dashicons-hidden');
            });

            // Next Step Handler (Step 1 -> 2)
            $('.wshc-next-step').on('click', function() {
                if (self.currentStep === 1) {
                    self.submitStep1();
                }
            });

            // Verification Handler (Step 2 -> 3)
            $('.wshc-verify-step').on('click', function() {
                self.submitStep2();
            });

            // Final Submit Handler
            $('#wshc-register-form').on('submit', function(e) {
                e.preventDefault();
                self.submitStep3();
            });

            // Login & Recover
            $('#wshc-login-form, #wshc-recover-form').on('submit', function(e) {
                e.preventDefault();
                self.handleSimpleForm($(this));
            });
        },

        switchView: function(target) {
            const $container = $('.wshc-auth-container');
            $('.wshc-auth-view').fadeOut(200, function() {
                $('#wshc-' + target + '-view').fadeIn(300);
                $('#wshc-api-response').empty();

                if (target === 'register') {
                    $('#wshc-reg-progress').show();
                    auth.goToStep(1);
                } else {
                    $('#wshc-reg-progress').hide();
                }
            });
        },

        goToStep: function(step) {
            this.currentStep = step;
            $('.wshc-reg-step').removeClass('active').hide();
            $(`.wshc-reg-step[data-step="${step}"]`).fadeIn(400).addClass('active');

            const progress = (step / this.totalSteps) * 100;
            $('.wshc-progress-bar').css('width', progress + '%');
        },

        submitStep1: function() {
            const self = this;
            const $form = $('#wshc-register-form');
            const data = $form.serializeArray();
            data.push({ name: 'action', value: 'wshc_register' });
            data.push({ name: 'security', value: wshc_vars.nonce });

            $('.wshc-loading-overlay').show();

            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: data,
                success: function(response) {
                    if (response.success) {
                        self.userId = response.data.user_id;
                        self.goToStep(2);
                    } else {
                        $('#wshc-api-response').html('<p class="error">' + response.data.message + '</p>');
                    }
                    $('.wshc-loading-overlay').hide();
                }
            });
        },

        submitStep2: function() {
            const self = this;
            const otp = $('input[name="otp"]').val();

            $('.wshc-loading-overlay').show();

            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: {
                    action: 'wshc_verify_otp',
                    security: wshc_vars.nonce,
                    user_id: self.userId,
                    otp: otp
                },
                success: function(response) {
                    if (response.success) {
                        self.goToStep(3);
                    } else {
                        $('#wshc-api-response').html('<p class="error">' + response.data.message + '</p>');
                    }
                    $('.wshc-loading-overlay').hide();
                }
            });
        },

        submitStep3: function() {
            const self = this;
            const $form = $('#wshc-register-form');
            const data = $form.serializeArray();
            data.push({ name: 'action', value: 'wshc_update_profile_meta' }); // Reusing logic
            data.push({ name: 'security', value: wshc_vars.nonce });
            data.push({ name: 'user_id', value: self.userId }); // For admin context if needed

            $('.wshc-loading-overlay').show();

            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#wshc-api-response').html('<p class="success">Registration complete! Redirecting...</p>');
                        setTimeout(() => {
                            window.location.href = wshc_vars.dashboard_url;
                        }, 1500);
                    } else {
                        $('#wshc-api-response').html('<p class="error">' + response.data.message + '</p>');
                    }
                    $('.wshc-loading-overlay').hide();
                }
            });
        },

        handleSimpleForm: function($form) {
            const $resp = $('#wshc-api-response');
            let action = $form.attr('id').includes('login') ? 'wshc_login' : 'wshc_recover';

            $('.wshc-loading-overlay').show();

            $.ajax({
                type: 'POST',
                url: wshc_vars.ajax_url,
                data: $form.serialize() + '&action=' + action + '&security=' + wshc_vars.nonce + '&remember=true',
                success: function(response) {
                    if (response.success) {
                        $resp.html('<p class="success">' + response.data.message + '</p>');
                        if (response.data.redirect) {
                            setTimeout(() => { window.location.href = response.data.redirect; }, 1000);
                        }
                    } else {
                        $resp.html('<p class="error">' + response.data.message + '</p>');
                    }
                    $('.wshc-loading-overlay').hide();
                }
            });
        }
    };

    auth.init();
});
