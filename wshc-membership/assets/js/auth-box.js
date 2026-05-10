jQuery(document).ready(function($) {
    // Toggle forms
    $('.wshc-toggle-form').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('#wshc-login-form-wrapper, #wshc-register-form-wrapper, #wshc-recover-form-wrapper').hide();
        $('#wshc-' + target + '-form-wrapper').fadeIn();
        $('#wshc-ajax-message').empty().removeClass('success error');
    });

    // Handle Login
    $('#wshc-login-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $msg = $('#wshc-ajax-message');
        
        $msg.text('Processing...').removeClass('success error');

        $.ajax({
            type: 'POST',
            url: wshc_ajax.ajax_url,
            data: {
                action: 'wshc_login',
                username: $form.find('input[name="username"]').val(),
                password: $form.find('input[name="password"]').val(),
                security: wshc_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $msg.addClass('success').text(response.data.message);
                    window.location.href = response.data.redirect;
                } else {
                    $msg.addClass('error').text(response.data.message);
                }
            }
        });
    });

    // Handle Register
    $('#wshc-register-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $msg = $('#wshc-ajax-message');

        $msg.text('Processing...').removeClass('success error');

        $.ajax({
            type: 'POST',
            url: wshc_ajax.ajax_url,
            data: {
                action: 'wshc_register',
                username: $form.find('input[name="username"]').val(),
                email: $form.find('input[name="email"]').val(),
                password: $form.find('input[name="password"]').val(),
                security: wshc_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $msg.addClass('success').text(response.data.message);
                    $form[0].reset();
                } else {
                    $msg.addClass('error').text(response.data.message);
                }
            }
        });
    });

    // Handle Recovery
    $('#wshc-recover-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $msg = $('#wshc-ajax-message');

        $msg.text('Processing...').removeClass('success error');

        $.ajax({
            type: 'POST',
            url: wshc_ajax.ajax_url,
            data: {
                action: 'wshc_recover',
                user_login: $form.find('input[name="user_login"]').val(),
                security: wshc_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $msg.addClass('success').text(response.data.message);
                } else {
                    $msg.addClass('error').text(response.data.message);
                }
            }
        });
    });
});
