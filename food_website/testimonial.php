<?php
require_once 'includes/config.php';
$page_title = 'Testimonials';
include 'includes/header.php';
?>

<section class="py-16 bg-light">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="font-bricolage font-bold text-4xl mb-4">What our customers say</h1>
            <p class="text-gray-600">Don't just take our word for it.</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC LIMIT 9");
            while($row = $stmt->fetch()):
            ?>
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex text-yellow-400 mb-4">
                    <?php for($i=0; $i<$row['rating']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                </div>
                <p class="text-gray-600 italic mb-6">"<?php echo $row['testimonial_text']; ?>"</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-primary font-bold">
                        <?php echo substr($row['customer_name'], 0, 1); ?>
                    </div>
                    <span class="font-bold text-dark"><?php echo $row['customer_name']; ?></span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <?php if(isLoggedIn()): ?>
        <div class="mt-16 max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm">
            <h3 class="font-bold text-xl mb-4 text-center">Leave a Review</h3>
            <form action="user/submit-review.php" method="POST">
                <textarea name="review" class="w-full border p-3 rounded-lg mb-4" placeholder="Share your experience..."></textarea>
                <button class="bg-primary text-white px-6 py-2 rounded-lg font-bold w-full">Submit Review</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>