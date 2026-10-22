<?php
/**
 * Testimonials Section Template
 *
 * Layout: testimonials (Flexible Content in Page Builder)
 * Strictly ACF-driven: Title, subtitle, and repeater testimonials (quote, client_name,
 * client_role, rating) are fetched purely from ACF without hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$title            = get_sub_field('title');
$subtitle         = get_sub_field('subtitle');
$raw_testimonials = get_sub_field('testimonials');

// 2. Parse Testimonials
$testimonials = [];
if (!empty($raw_testimonials) && is_array($raw_testimonials)) {
    foreach ($raw_testimonials as $item) {
        $raw_q  = !empty($item['quote']) ? trim($item['quote']) : '';
        $quote  = preg_replace('/^(["\'“”«»\s]+)|(["\'“”«»\s]+)$/u', '', $raw_q);
        $name   = !empty($item['client_name']) ? trim($item['client_name']) : '';
        $role   = !empty($item['client_role']) ? trim($item['client_role']) : '';
        $rating = isset($item['rating']) && $item['rating'] !== '' ? (int) $item['rating'] : 5;

        if (!empty($quote) || !empty($name)) {
            $testimonials[] = [
                'quote'  => $quote,
                'name'   => $name,
                'role'   => $role,
                'rating' => $rating,
            ];
        }
    }
}

// Exit if entire section is empty
if (empty($title) && empty($subtitle) && empty($testimonials)) {
    return;
}

// Unique slider ID for multiple instances
$unique_id = 'testimonials-' . uniqid();
?>

<section class="testimonials-section">
    <div class="ls-container">
        <div class="testimonials-wrapper">

            <?php if (!empty($title)) : ?>
                <h2 class="testimonials-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($subtitle)) : ?>
                <p class="testimonials-subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>

            <?php if (!empty($testimonials)) : ?>
                <div id="<?php echo esc_attr($unique_id); ?>" class="testimonials-slider glide">
                    
                    <div class="glide__track" data-glide-el="track">
                        <ul class="glide__slides">
                            <?php foreach ($testimonials as $t) : ?>
                                <li class="glide__slide testimonial-slide">
                                    
                                    <?php if (!empty($t['rating']) && $t['rating'] > 0) : ?>
                                        <div class="testimonial-stars" aria-label="<?php echo esc_attr($t['rating']); ?> out of 5 stars">
                                            <?php for ($i = 0; $i < min(5, (int)$t['rating']); $i++) : ?>
                                                <span class="testimonial-star">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($t['quote'])) : ?>
                                        <blockquote class="testimonial-quote">
                                            “<?php echo nl2br(esc_html($t['quote'])); ?>”
                                        </blockquote>
                                    <?php endif; ?>

                                    <?php if (!empty($t['name'])) : ?>
                                        <div class="testimonial-author"><?php echo esc_html($t['name']); ?></div>
                                    <?php endif; ?>

                                    <?php if (!empty($t['role'])) : ?>
                                        <div class="testimonial-role"><?php echo esc_html($t['role']); ?></div>
                                    <?php endif; ?>

                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if (count($testimonials) > 1) : ?>
                        <div class="testimonials-arrows" data-glide-el="controls">
                            <button class="testimonials-arrow testimonials-arrow--prev" data-glide-dir="<" aria-label="Previous">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="testimonials-arrow testimonials-arrow--next" data-glide-dir=">" aria-label="Next">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>

                <?php if (count($testimonials) > 1) : ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var sliderEl = document.getElementById('<?php echo esc_js($unique_id); ?>');
                            if (sliderEl && typeof Glide !== 'undefined') {
                                new Glide(sliderEl, {
                                    type: 'carousel',
                                    autoplay: 7000,
                                    hoverpause: true,
                                    perView: 1,
                                    gap: 0,
                                    animationDuration: 600
                                }).mount();
                            }
                        });
                    </script>
                <?php endif; ?>

            <?php endif; ?>

        </div>
    </div>
</section>
