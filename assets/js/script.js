// Sidebar Toggle
$(document).ready(function() {
    // Toggle Sidebar
    $('#sidebarToggle').click(function() {
        $('body').toggleClass('sidebar-collapsed');
        
        // Simpan preferensi sidebar di session
        fetch('/auth/toggle_sidebar.php')
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.error('Error:', error));
    });
    
    // Toggle Submenu
    $('.sidebar-menu .dropdown-toggle').click(function(e) {
        e.preventDefault();
        
        const submenu = $(this).next('.submenu');
        const arrow = $(this).find('.menu-arrow');
        
        if (submenu.css('display') === 'block') {
            submenu.slideUp();
            arrow.removeClass('rotate');
        } else {
            submenu.slideDown();
            arrow.addClass('rotate');
        }
    });
    
    // Aktifkan submenu jika halaman aktif ada di submenu
    $('.sidebar-menu .submenu a.active').each(function() {
        const parentMenu = $(this).closest('.has-submenu');
        const toggleBtn = parentMenu.find('.dropdown-toggle');
        const submenu = parentMenu.find('.submenu');
        const arrow = parentMenu.find('.menu-arrow');
        
        submenu.show();
        arrow.addClass('rotate');
    });
});