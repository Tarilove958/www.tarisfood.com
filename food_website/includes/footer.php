<?php
// includes/footer.php
?>
    <footer class="bg-dark text-white pt-16 pb-8 mt-auto">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <img src="assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="h-12 mb-4 bg-white rounded p-1">
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Delicious food delivered to your doorstep. Fresh ingredients, fast delivery, and the best taste in town.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4 text-primary">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="menu.php" class="hover:text-white transition-colors">Our Menu</a></li>
                        <li><a href="about.php" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="testimonial.php" class="hover:text-white transition-colors">Reviews</a></li>
                        <li><a href="contact.php" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4 text-primary">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="terms-of-use.php" class="hover:text-white transition-colors">Terms of Use</a></li>
                        <li><a href="privacy-policy.php" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="return-policy.php" class="hover:text-white transition-colors">Return Policy</a></li>
                        <li><a href="help.php" class="hover:text-white transition-colors">Help Center</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bricolage font-bold text-lg mb-4 text-primary">Stay Updated</h4>
                    <form class="flex gap-2" onsubmit="event.preventDefault(); alert('Subscribed!');">
                        <input type="email" placeholder="Email address" class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:outline-none focus:border-primary">
                        <button type="submit" class="bg-secondary p-2 rounded-lg text-white hover:bg-red-700 transition-colors">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>