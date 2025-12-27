        </main>

        <footer class="bg-white border-t border-gray-100 px-6 py-4 text-center text-sm text-gray-500">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> Admin Panel. All rights reserved.</p>
        </footer>

    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });

        // Close sidebar on link click (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    document.getElementById('sidebar').classList.add('hidden');
                }
            });
        });

        // Auto-dismiss flash messages after 5 seconds
        setTimeout(function() {
            const flashMessage = document.querySelector('[class*="text-green-800"], [class*="text-red-800"]');
            if (flashMessage && flashMessage.className.includes('rounded-xl')) {
                flashMessage.style.transition = 'opacity 0.3s ease';
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }, 5000);
    </script>
</body>
</html>