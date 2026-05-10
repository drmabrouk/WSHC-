jQuery(document).ready(function($) {
    // Switch between auth views
    $('.wshc-switch').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('.wshc-auth-view').hide();
        $('#wshc-' + target + '-view').fadeIn(300);
        $('#wshc-api-response').empty();
    });

    // Handle AJAX forms
    $('#wshc-login-form, #wshc-register-form, #wshc-recover-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $resp = $('#wshc-api-response');
        var action = '';
        
        if ($form.attr('id') === 'wshc-login-form') action = 'wshc_login';
        if ($form.attr('id') === 'wshc-register-form') action = 'wshc_register';
        if ($form.attr('id') === 'wshc-recover-form') action = 'wshc_recover';

        $resp.html('<p class="loading">Processing...</p>');

        $.ajax({
            type: 'POST',
            url: wshc_vars.ajax_url,
            data: $form.serialize() + '&action=' + action + '&security=' + wshc_vars.nonce,
            success: function(response) {
                if (response.success) {
                    $resp.html('<p class="success">' + response.data.message + '</p>');
                    if (response.data.redirect) {
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    }
                    if (action === 'wshc_register') $form[0].reset();
                } else {
                    $resp.html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $resp.html('<p class="error">An unexpected error occurred.</p>');
            }
        });
    });
});
