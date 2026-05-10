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

            // AJAX View Routing
            $(document).on('click', '#wshc-sidebar a[data-view]', function(e) {
                e.preventDefault();
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

            // Modal: Close
            $(document).on('click', '.wshc-modal-close', function() {
                $('#wshc-user-modal').fadeOut();
            });

            // Modal: Submit
            $(document).on('submit', '#wshc-user-edit-form', function(e) {
                e.preventDefault();
                self.updateUser($(this));
            });
        },

        handleInitialView: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const view = urlParams.get('view');
            if (view) {
                const label = $(`#wshc-sidebar a[data-view="${view}"]`).text().trim();
                this.loadView(view, label, false);
            } else if ($('#wshc-user-table').length) {
                this.loadUsers(1);
            }
        },

        loadView: function(view, label, updatePushState = true) {
            const self = this;
            $('.wshc-loading-overlay').show();

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
                        $('#wshc-dynamic-content').html(response.html);
                        $('#wshc-view-title').text(label);

                        // Mark active in sidebar
                        $('#wshc-sidebar li').removeClass('active');
                        $(`#wshc-sidebar a[data-view="${view}"]`).parent().addClass('active');

                        if (updatePushState) {
                            const newUrl = window.location.pathname + '?view=' + view;
                            history.pushState({ view: view }, '', newUrl);
                        }

                        // If user directory, load users
                        if (view === 'user-directory') {
                            self.loadUsers(1);
                        }
                    }
                    $('.wshc-loading-overlay').hide();
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
