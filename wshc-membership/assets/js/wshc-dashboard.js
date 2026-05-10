jQuery(document).ready(function($) {
    // Hamburger menu toggle
    $('#wshc-hamburger').on('click', function() {
        $(this).toggleClass('open');
        $('#wshc-sidebar').toggleClass('active');
    });

    // Sidebar navigation simulation
    $('#wshc-sidebar a[data-view]').on('click', function(e) {
        e.preventDefault();
        var label = $(this).text().trim();
        $('#wshc-view-title').text(label);
        
        // Mark active
        $('#wshc-sidebar li').removeClass('active');
        $(this).parent().addClass('active');

        // On mobile, close sidebar after selection
        if ($(window).width() < 992) {
            $('#wshc-sidebar').removeClass('active');
            $('#wshc-hamburger').removeClass('open');
        }
    });
});
