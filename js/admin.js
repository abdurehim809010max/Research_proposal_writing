/* ============================================
   Admin Panel JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Sidebar Toggle ----
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });
    }

    // ---- Mobile Sidebar Toggle ----
    var mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    if (mobileSidebarToggle && sidebar) {
        mobileSidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('mobile-active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-active');
            }
        });
    }

    // ---- Flash Message Auto-dismiss ----
    var flashMessage = document.getElementById('flashMessage');
    if (flashMessage) {
        setTimeout(function () {
            flashMessage.style.transition = 'opacity 0.5s ease';
            flashMessage.style.opacity = '0';
            setTimeout(function () { flashMessage.remove(); }, 500);
        }, 5000);
    }

    // ---- Confirm Delete ----
    var deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // ---- AJAX Order Status Update ----
    var statusForms = document.querySelectorAll('.status-form');
    statusForms.forEach(function (form) {
        var select = form.querySelector('.status-select');
        if (select) {
            select.addEventListener('change', function () {
                var formData = new FormData();
                formData.append('order_id', form.querySelector('[name="order_id"]').value);
                formData.append('status', this.value);

                var siteUrl = document.querySelector('link[rel="stylesheet"]').href;
                var baseUrl = siteUrl.substring(0, siteUrl.indexOf('/css/'));

                // Asynchronous status update
                fetch(baseUrl + '/api/update_order_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        // Update the status badge in the same row
                        var row = form.closest('tr');
                        if (row) {
                            var badge = row.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge status-' + select.value;
                                badge.textContent = select.value.charAt(0).toUpperCase() + select.value.slice(1);
                            }
                        }
                        showAdminToast(data.message);
                    }
                })
                .catch(function () {
                    // Fallback: submit the form normally
                    form.submit();
                });
            });
        }
    });

    function showAdminToast(message) {
        var toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 24px;border-radius:8px;color:#fff;font-size:14px;font-weight:500;z-index:9999;background:#27ae60;box-shadow:0 5px 15px rgba(0,0,0,0.2);';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }
});
