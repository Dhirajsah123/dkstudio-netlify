<?php
// skillora/index.php
$page_title = 'Welcome to Skillora';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>Learn. Upgrade. Earn.</h1>
        <p>Your journey to professional growth starts here. Access high-quality courses and a vibrant community.</p>
        <a href="<?php echo $base_url; ?>/membership.php" class="btn btn-primary">View Membership Plans</a>
    </div>
</section>

<section id="features" class="features-section">
    <h2 class="section-title">Why Choose Skillora?</h2>
    <div class="features-grid">
        <div class="feature-item">

            <h3>Expert-Led Courses</h3>
            <p>Learn from seasoned industry professionals who are passionate about teaching.</p>
        </div>
        <div class="feature-item">

            <h3>Flexible Learning</h3>
            <p>Learn at your own pace, anytime, anywhere. Our platform is available on all devices.</p>
        </div>
        <div class="feature-item">

            <h3>Referral & Earn System</h3>
            <p>Share Skillora with your friends and earn rewards through our referral program.</p>
        </div>
    </div>
</section>

<section id="pricing" class="membership-pricing-section">
    <h2 class="section-title">Our Membership Plans</h2>
    <div class="pricing-grid">
        <!-- Bronze Plan -->
        <div class="pricing-card">
            <h3>Bronze</h3>
            <div class="price">300 NPR<span>/year</span></div>
            <ul>
                <li>Access to limited premium courses</li>
                <li>Standard Support</li>
                <li>-</li>
                <li>-</li>
            </ul>
            <a href="<?php echo $base_url; ?>/membership.php?tier=bronze" class="btn">Choose Plan</a>
        </div>
        <!-- Silver Plan -->
        <div class="pricing-card recommended">
            <div class="recommended-badge">Recommended</div>
            <h3>Silver</h3>
            <div class="price">500 NPR<span>/year</span></div>
            <ul>
                <li>Access to more premium courses</li>
                <li>Course Completion Certificates</li>
                <li>Standard Support</li>
                <li>-</li>
            </ul>
            <a href="<?php echo $base_url; ?>/membership.php?tier=silver" class="btn btn-primary">Choose Plan</a>
        </div>
        <!-- Gold Plan -->
        <div class="pricing-card">
            <h3>Gold</h3>
            <div class="price">700 NPR<span>/year</span></div>
            <ul>
                <li>Access to all courses</li>
                <li>Course Completion Certificates</li>
                <li>Downloadable Notes & Resources</li>
                <li>Priority Support</li>
            </ul>
            <a href="<?php echo $base_url; ?>/membership.php?tier=gold" class="btn">Choose Plan</a>
        </div>
    </div>
</section>

<section id="testimonials" class="testimonials-section">
    <h2 class="section-title">What Our Students Say</h2>
    <div class="testimonial">
        <blockquote>"Skillora has been a game-changer for my career. The courses are practical and the instructors are top-notch!"</blockquote>
        <cite>- Alex Johnson, Web Developer</cite>
    </div>
</section>

<section id="faq" class="faq-section">
    <h2 class="section-title">Frequently Asked Questions</h2>
    <div class="faq-item">
        <h3 class="faq-question">How does the payment process work?</h3>
        <div class="faq-answer">
            <p>You select a membership plan, choose a payment method (like eSewa or Khalti), and upload a screenshot of your payment receipt. Once our team approves it, your account is activated instantly.</p>
        </div>
    </div>
    <div class="faq-item">
        <h3 class="faq-question">Can I get a refund?</h3>
        <div class="faq-answer">
            <p>Due to the nature of digital products, we do not offer refunds. We encourage you to review the course details before purchasing a membership.</p>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
