// skillora/assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {

    // --- FAQ Accordion Logic (for homepage) ---
    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const answer = item.querySelector('.faq-answer');
                const isVisible = answer.style.display === 'block';
                answer.style.display = isVisible ? 'none' : 'block';
            });
        });
    }

    // --- Membership Form QR Code Logic (for membership.php) ---
    const membershipForm = document.getElementById('membership-form');
    if (membershipForm) {
        const paymentMethodRadios = membershipForm.querySelectorAll('input[name="payment_method"]');
        const qrCodeDisplay = document.getElementById('qr-code-display');
        const qrCodeImage = document.getElementById('qr-code-image');

        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    const qrCodeUrl = this.dataset.qr;
                    if (qrCodeUrl) {
                        qrCodeImage.src = qrCodeUrl;
                        qrCodeImage.alt = `QR Code for ${this.value}`;
                        qrCodeDisplay.style.display = 'block';
                    } else {
                        qrCodeDisplay.style.display = 'none';
                    }
                }
            });
        });
    }

    // --- Course Video Player Logic (for view_course.php) ---
    const lessonLinks = document.querySelectorAll('.lesson-link');
    const videoPlayer = document.getElementById('lesson-video-player');
    const lessonTitle = document.getElementById('lesson-title');

    if (lessonLinks.length > 0 && videoPlayer && lessonTitle) {
        lessonLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent page from reloading
                const videoUrl = this.dataset.videoUrl;
                const newTitle = this.dataset.lessonTitle;
                videoPlayer.src = videoUrl;
                lessonTitle.textContent = newTitle;
                document.querySelectorAll('.lesson-list li').forEach(li => li.classList.remove('active'));
                this.closest('li').classList.add('active');
            });
        });
    }

    // --- Referral Code Copy Button (for user/dashboard.php) ---
    const copyBtn = document.getElementById('copy-ref-code');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const input = document.getElementById('ref-code-input');
            input.select();

            navigator.clipboard.writeText(input.value).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => { this.textContent = originalText; }, 2000);
            }).catch(err => {
                console.error('Clipboard API failed. Falling back to execCommand.', err);
                try {
                    document.execCommand('copy');
                    const originalText = this.textContent;
                    this.textContent = 'Copied!';
                    setTimeout(() => { this.textContent = originalText; }, 2000);
                } catch (e) {
                    console.error('execCommand failed.', e);
                }
            });
        });
    }
});
