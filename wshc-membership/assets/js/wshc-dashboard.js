jQuery(document).ready(function($) {
    const dashboard = {
        init: function() {
            this.bindEvents();
            this.handleInitialView();
        },

        bindEvents: function() {
            const self = this;

            // Hamburger menu toggle
            $('#wshc-hamburger').on('click', function() {
                $(this).toggleClass('open');
                $('#wshc-sidebar').toggleClass('active');
            });

            // AJAX View Routing - With Content Persistence Shield
            $(document).on('click', 'a[data-view]', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation(); // Prevent Conflict
                const view = $(this).data('view');
                const label = $(this).text().trim();

                self.loadView(view, label);
            });

            // Popstate for back/forward navigation
            $(window).on('popstate', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const view = urlParams.get('view') || 'overview';

                // Find label from sidebar
                const label = $(`#wshc-sidebar a[data-view="${view}"]`).text().trim();
                self.loadView(view, label, false);
            });

            // User Directory: Search & Filter
            $(document).on('input', '#wshc-user-search', this.debounce(function() {
                self.loadUsers(1);
            }, 300));

            $(document).on('change', '#wshc-role-filter', function() {
                self.loadUsers(1);
            });

            // User Directory: Pagination
            $(document).on('click', '.wshc-pagination a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                self.loadUsers(page);
            });

            // User Directory: Open Modal
            $(document).on('click', '.wshc-edit-user', function(e) {
                e.preventDefault();
                const userData = $(this).data('user');
                self.openModal(userData);
            });

            // User Directory: Quick Elevate
            $(document).on('click', '.wshc-elevate-user', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const userId = $btn.data('id');
                const $row = $btn.closest('tr');
                const user = JSON.parse($btn.siblings('.wshc-edit-user').attr('data-user'));
                self.openModal(user);
            });

            // User Directory: Quick Suspend
            $(document).on('click', '.wshc-suspend-user', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to suspend this user?')) {
                    const userId = $(this).data('id');
                    self.updateUserAjax({
                        user_id: userId,
                        status: 'Pending'
                    });
                }
            });

            // User Directory: Delete User
            $(document).on('click', '.wshc-delete-user', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    const userId = $(this).data('id');
                    self.deleteUser(userId);
                }
            });

            // Modal: Close
            $(document).on('click', '.wshc-modal-close', function() {
                $('#wshc-user-modal').fadeOut();
            });

            // Modal: Submit
            $(document).on('submit', '#wshc-user-edit-form', function(e) {
                e.preventDefault();
                self.updateUser($(this));
            });

            // Profile: Update Identity
            $(document).on('submit', '#wshc-identity-form', function(e) {
                e.preventDefault();
                self.updateIdentity($(this));
            });

            // Profile: Update Meta
            $(document).on('submit', '#wshc-profile-meta-form', function(e) {
                e.preventDefault();
                self.updateProfileMeta($(this));
            });

            // Profile: Copy Link
            $(document).on('click', '#wshc-copy-link', function(e) {
                e.preventDefault();
                const link = $('#wshc-profile-link').val();
                navigator.clipboard.writeText(link).then(() => {
                    $(this).text('Copied!');
                    setTimeout(() => $(this).text('Copy Link'), 2000);
                });
            });

            // Settings: Toggle Modal
            $(document).on('click', '#wshc-settings-toggle, #wshc-settings-toggle-shortcut', function(e) {
                e.preventDefault();
                $('#wshc-settings-modal').css('display', 'flex').hide().fadeIn(200);
            });

            $(document).on('click', '#wshc-settings-close', function(e) {
                e.preventDefault();
                $('#wshc-settings-modal').fadeOut(200);
            });

            // Settings: Bio Character Counter
            $(document).on('input', '#wshc-bio-field', function() {
                const count = $(this).val().length;
                $('#wshc-bio-count').text(count);
                if (count > 150) {
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            // Settings: Submit
            $(document).on('submit', '#wshc-global-settings-form', function(e) {
                e.preventDefault();
                self.saveGlobalSettings($(this));
            });
        },

        handleInitialView: function() {
            const urlParams = new URLSearchParams(window.location.search);
            let view = urlParams.get('view') || 'overview';
            const $container = $('#wshc-dynamic-content');
            const activeView = $container.attr('data-active-view');

            // Set initial active state in sidebar
            $('#wshc-sidebar li').removeClass('active');
            $(`#wshc-sidebar a[data-view="${view}"]`).parent().addClass('active');

            // If the current container already has the correct view (server-rendered), just initialize modules
            if (view === activeView) {
                if (view === 'user-directory') {
                    this.loadUsers(1);
                }
                return;
            }

            // Otherwise, force load the requested view to ensure consistency
            const $link = $(`#wshc-sidebar a[data-view="${view}"]`);
            const label = $link.length ? $link.text().trim() : 'Dashboard';
            this.loadView(view, label, false);
        },

        loadView: function(view, label, updatePushState = true) {
            const self = this;
            const $container = $('#wshc-dynamic-content');

            // State Locking Mechanism - Prevent redundant loads during active AJAX
            if ($container.attr('data-active-view') === 'loading') return;
            if (updatePushState && $container.attr('data-active-view') === view) return;

            // Show loading
            $('.wshc-loading-overlay').stop(true, true).fadeIn(100);
            $container.css('opacity', '0.5');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'wshc_load_view',
                    security: wshc_vars.nonce,
                    view: view
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.html).attr('data-active-view', view);
                        $('#wshc-view-title').text(label);

                        // Mark active in sidebar
                        $('#wshc-sidebar li').removeClass('active');
                        $(`#wshc-sidebar a[data-view="${view}"]`).parent().addClass('active');

                        if (updatePushState) {
                            const newUrl = window.location.pathname + '?view=' + view;
                            history.pushState({ view: view }, '', newUrl);
                        }

                        // Handle unique view initializations
                        if (view === 'user-directory') {
                            self.loadUsers(1);
                        }
                    } else {
                        $container.html(`<div class="wshc-card"><p class="error">${response.data.message}</p></div>`).attr('data-active-view', 'error');
                    }
                },
                error: function() {
                    $container.html('<div class="wshc-card"><p class="error">Failed to load view. Please check your connection.</p></div>').attr('data-active-view', 'error');
                },
                complete: function() {
                    $('.wshc-loading-overlay').fadeOut(100);
                    $container.css('opacity', '1');
                }
            });

            // On mobile, close sidebar after selection
            if ($(window).width() < 992) {
                $('#wshc-sidebar').removeClass('active');
                $('#wshc-hamburger').removeClass('open');
            }
        },

        loadUsers: function(page) {
            const search = $('#wshc-user-search').val();
            const role = $('#wshc-role-filter').val();
            const $tbody = $('#wshc-user-table tbody');

            $tbody.html('<tr><td colspan="5" style="text-align:center;">Loading users...</td></tr>');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'wshc_admin_get_users',
                    security: wshc_vars.nonce,
                    search: search,
                    role: role,
                    page: page
                },
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.data.users.length === 0) {
                            html = '<tr><td colspan="5" style="text-align:center;">No users found.</td></tr>';
                        } else {
                            response.data.users.forEach(user => {
                                const userJson = JSON.stringify(user).replace(/'/g, "&apos;");
                                html += `
                                    <tr>
                                        <td>
                                            <strong>${user.display_name}</strong><br>
                                            <small>${user.user_email}</small>
                                        </td>
                                        <td>${user.role_label}</td>
                                        <td><span class="wshc-badge status-${user.status.toLowerCase()}">${user.status}</span></td>
                                        <td>${user.registered}</td>
                                        <td>
                                            <button class="wshc-btn-action wshc-edit-user" data-user='${userJson}' title="Edit Profile">Edit</button>
                                            <button class="wshc-btn-action wshc-elevate-user" data-id="${user.ID}" title="Elevate Role">Elevate</button>
                                            <button class="wshc-btn-action wshc-suspend-user" data-id="${user.ID}" title="Suspend Account">Suspend</button>
                                            <button class="wshc-btn-action wshc-delete-user" data-id="${user.ID}" title="Delete User">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $tbody.html(html);
                        this.renderPagination(response.data.pages, response.data.current);
                    }
                }.bind(this)
            });
        },

        renderPagination: function(totalPages, currentPage) {
            let html = '';
            for (let i = 1; i <= totalPages; i++) {
                html += `<a href="#" data-page="${i}" class="${i === currentPage ? 'active' : ''}">${i}</a>`;
            }
            $('#wshc-pagination').html(html);
        },

        openModal: function(user) {
            $('#edit-user-id').val(user.ID);
            $('#edit-display-name').val(user.display_name);
            $('#edit-user-login').val(user.user_login);
            $('#edit-user-role').val(user.role);
            $('#edit-user-status').val(user.status);
            $('#edit-user-id-verified').val(user.id_verified);
            $('#edit-user-registered').val(user.registered);
            $('#edit-user-credentials').val(user.credentials);
            $('#wshc-user-modal').fadeIn();
        },

        updateUser: function($form) {
            const self = this;
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=wshc_admin_update_user&security=' + wshc_vars.nonce,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        $('#wshc-user-modal').fadeOut();
                        self.loadUsers($('.wshc-pagination a.active').data('page') || 1);
                    } else {
                        alert(response.data.message);
                    }
                    $btn.prop('disabled', false).text('Save Changes');
                }
            });
        },

        saveGlobalSettings: function($form) {
            const self = this;
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=wshc_save_global_settings&security=' + wshc_vars.nonce,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload(); // Refresh to update display names/headers
                    } else {
                        alert(response.data.message);
                    }
                    $btn.prop('disabled', false).text('Save All Changes');
                }
            });
        },

        updateIdentity: function($form) {
            const self = this;
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=wshc_update_account_identity&security=' + wshc_vars.nonce,
                success: function(response) {
                    alert(response.data.message);
                    $btn.prop('disabled', false).text('Update Identity');
                }
            });
        },

        updateProfileMeta: function($form) {
            const self = this;
            const $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: $form.serialize() + '&action=wshc_update_profile_meta&security=' + wshc_vars.nonce,
                success: function(response) {
                    alert(response.data.message);
                    $btn.prop('disabled', false).text('Save Profile Details');
                }
            });
        },

        deleteUser: function(userId) {
            const self = this;
            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'wshc_admin_delete_user',
                    security: wshc_vars.nonce,
                    user_id: userId
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        self.loadUsers($('.wshc-pagination a.active').data('page') || 1);
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        updateUserAjax: function(data) {
            const self = this;
            $.ajax({
                url: wshc_vars.ajax_url,
                type: 'POST',
                data: $.extend(data, {
                    action: 'wshc_admin_update_user',
                    security: wshc_vars.nonce
                }),
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        self.loadUsers($('.wshc-pagination a.active').data('page') || 1);
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        debounce: function(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
    };

    dashboard.init();
});
