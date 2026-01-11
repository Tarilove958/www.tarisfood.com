        </main>

        <footer class="bg-white border-t border-gray-100 px-6 py-4 text-center text-xs text-gray-500 shadow-sm flex-shrink-0">
            <div class="flex items-center justify-between max-w-full">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> Admin Panel. All rights reserved.</p>
                <div class="flex items-center gap-2 text-gray-400">
                    <i class="bi bi-shield-check"></i>
                    <span>Secure • Encrypted</span>
                </div>
            </div>
        </footer>

    </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>

    <script>
        // Sidebar Toggle with smooth animation
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const appContainer = document.getElementById('app-container');
        
        if(sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('hidden');
                this.classList.toggle('text-primary');
            });
        }

        // Close sidebar on link click (mobile)
        if(sidebar) {
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't prevent default navigation
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('hidden');
                    }
                });
            });
        }

        // Close sidebar when clicking outside (mobile)
        if(appContainer) {
            appContainer.addEventListener('click', function(e) {
                if(sidebar && !e.target.closest('#sidebar') && !e.target.closest('#sidebar-toggle')) {
                    if(window.innerWidth < 768 && !sidebar.classList.contains('hidden')) {
                        sidebar.classList.add('hidden');
                    }
                }
            });
        }

        // Auto-dismiss flash messages after 5 seconds with fade-out
        setTimeout(function() {
            const flashMessage = document.querySelector('.flash-message');
            if(flashMessage) {
                flashMessage.style.transition = 'opacity 0.4s ease';
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 400);
            }
        }, 5000);

        // Add ripple effect to buttons
        document.querySelectorAll('button, [class*="btn"], a[class*="bg-primary"]').forEach(el => {
            el.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.className = 'ripple';
                
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });
    </script>

    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>
                flashMessage.style.transition = 'opacity 0.3s ease';
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 300);
            }
        }, 5000);
    </script>
</body>
</html>