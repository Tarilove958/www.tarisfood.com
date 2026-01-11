<?php
// includes/footer.php
// Get active theme for footer colors
$footerTheme = getActiveTheme();
$footerPrimary = $footerTheme['primary_color'] ?? '#3b82f6';
$footerSecondary = $footerTheme['secondary_color'] ?? '#f97316';
?>
<style>
    footer {
        background-color: #1f2937 !important;
    }
    
    footer h4 {
        color: <?php echo $footerPrimary; ?> !important;
    }
    
    footer a:hover {
        color: <?php echo $footerPrimary; ?> !important;
    }
    
    footer button[type="submit"],
    footer button {
        background-color: <?php echo $footerSecondary; ?> !important;
    }
    
    footer button[type="submit"]:hover,
    footer button:hover {
        background-color: <?php echo adjustBrightness($footerSecondary, -20); ?> !important;
    }
    
    footer input:focus {
        border-color: <?php echo $footerPrimary; ?> !important;
    }
</style>

    <footer class="text-white pt-16 pb-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <img src="<?php echo isUserPath() ? '../' : ''; ?>assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="h-12 mb-4 bg-white rounded p-1">
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Delicious food delivered to your doorstep. Fresh ingredients, fast delivery, and the best taste in town.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>menu.php" class="hover:transition-colors">Our Menu</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>about.php" class="hover:transition-colors">About Us</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>testimonial.php" class="hover:transition-colors">Reviews</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>contact.php" class="hover:transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>terms-of-use.php" class="hover:transition-colors">Terms of Use</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>privacy-policy.php" class="hover:transition-colors">Privacy Policy</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>return-policy.php" class="hover:transition-colors">Return Policy</a></li>
                        <li><a href="<?php echo isUserPath() ? '../' : ''; ?>help.php" class="hover:transition-colors">Help Center</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4">Stay Updated</h4>
                    <form class="flex gap-2" onsubmit="event.preventDefault(); alert('Subscribed!');">
                        <input type="email" placeholder="Email address" class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:outline-none">
                        <button type="submit" class="p-2 rounded-lg text-white transition-colors">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:transition-colors"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:transition-colors"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:transition-colors"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo isUserPath() ? '../' : ''; ?>assets/js/main.js"></script>
    <script src="<?php echo isUserPath() ? '../' : ''; ?>assets/js/cart.js"></script>
</body>
</html>