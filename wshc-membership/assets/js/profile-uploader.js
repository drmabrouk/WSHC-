jQuery(document).ready(function($) {
    const uploader = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            const self = this;

            // Trigger file input
            $(document).on('click', '#wshc-profile-upload-trigger', function() {
                $('#wshc-profile-file').click();
            });

            // Handle file selection
            $(document).on('change', '#wshc-profile-file', function(e) {
                if (e.target.files.length > 0) {
                    self.uploadImage(e.target.files[0]);
                }
            });
        },

        uploadImage: function(file) {
            const formData = new FormData();
            formData.append('action', 'wshc_upload_profile_image');
            formData.append('security', wshc_vars.nonce);
            formData.append('profile_image', file);

            $('.wshc-loading-overlay').show();

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Refresh images in sidebar and top-bar
                        $('.wshc-sidebar-avatar, .wshc-top-avatar img').attr('src', response.data.url);
                        // If standard avatar was showing, replace it
                        if ($('.wshc-avatar-wrapper .avatar').length) {
                            $('.wshc-avatar-wrapper .avatar').replaceWith(`<img src="${response.data.url}" class="wshc-sidebar-avatar" />`);
                        }
                        if ($('.wshc-top-avatar .avatar').length) {
                            $('.wshc-top-avatar .avatar').replaceWith(`<img src="${response.data.url}" />`);
                        }
                    } else {
                        alert(response.data.message);
                    }
                    $('.wshc-loading-overlay').hide();
                },
                error: function() {
                    alert('An error occurred during upload.');
                    $('.wshc-loading-overlay').hide();
                }
            });
        }
    };

    uploader.init();
});
