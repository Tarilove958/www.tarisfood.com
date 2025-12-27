<?php
require_once 'includes/config.php';
$page_title = 'About Us';
include 'includes/header.php';
?>

<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h1 class="font-bricolage font-bold text-4xl md:text-5xl mb-6 text-dark">We Cook With <span class="text-secondary">Love</span></h1>
        <p class="text-xl text-gray-600 leading-relaxed mb-12">
            Founded in Bayelsa, TarisFood was born from a simple passion: to make high-quality, delicious meals accessible to everyone, delivered fast.
        </p>
    </div>
    
    <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
        <div class="bg-gray-100 rounded-3xl h-64 md:h-96 overflow-hidden shadow-lg">
            <img src="assets/images/about image.jpeg" alt="About TarisFood" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
        </div>
        <div>
            <h2 class="font-bold text-2xl mb-4">Our Mission</h2>
            <p class="text-gray-600 mb-6">To satisfy cravings with healthy, hygienic, and tasty food while providing seamless service.</p>
            
            <div class="space-y-4">
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-primary flex-shrink-0">
                        <i class="bi bi-check-lg text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Fresh Ingredients</h4>
                        <p class="text-sm text-gray-500">We source locally from trusted farmers.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-secondary flex-shrink-0">
                        <i class="bi bi-heart-fill text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Expert Chefs</h4>
                        <p class="text-sm text-gray-500">Culinary masters behind every dish.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>