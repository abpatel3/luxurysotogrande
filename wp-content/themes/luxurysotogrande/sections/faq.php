<?php
/**
 * FAQ (Everything People Ask Us) Section Template
 *
 * Layout: faq (Flexible Content in Page Builder)
 * Strictly ACF-driven: Title and FAQ repeater items (Question/Title, Answer/Text)
 * are fetched purely from ACF without hardcoded fallback items.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$title   = get_sub_field('title');
$raw_faq = get_sub_field('faq');

// 2. Parse FAQ items
$faq_items = [];
if (!empty($raw_faq) && is_array($raw_faq)) {
    foreach ($raw_faq as $item) {
        $q = !empty($item['title']) ? trim($item['title']) : (!empty($item['question']) ? trim($item['question']) : '');
        $a = !empty($item['text']) ? trim($item['text']) : (!empty($item['answer']) ? trim($item['answer']) : '');

        if (!empty($q) || !empty($a)) {
            $faq_items[] = [
                'question' => $q,
                'answer'   => $a,
            ];
        }
    }
}

// Exit if entire section is empty
if (empty($title) && empty($faq_items)) {
    return;
}

$section_id = 'faq-luxury-' . uniqid();
?>

<section class="faq-luxury-section" id="<?php echo esc_attr($section_id); ?>">
    <div class="ls-container">
        <div class="faq-luxury-wrapper">

            <?php if (!empty($title)) : ?>
                <h2 class="faq-luxury-title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($faq_items)) : ?>
                <div class="faq-luxury-accordion">
                    <?php foreach ($faq_items as $index => $item) : 
                        $is_first = ($index === 0);
                    ?>
                        <div class="faq-luxury-item <?php echo $is_first ? 'faq-luxury-item--active' : ''; ?>">
                            <div class="faq-luxury-header" role="button" tabindex="0" aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>">
                                <span class="faq-luxury-question"><?php echo esc_html($item['question']); ?></span>
                                <span class="faq-luxury-icon" aria-hidden="true"><?php echo $is_first ? '−' : '+'; ?></span>
                            </div>
                            <div class="faq-luxury-content" style="<?php echo $is_first ? 'display: block;' : 'display: none;'; ?>">
                                <p class="faq-luxury-text"><?php echo nl2br(esc_html($item['answer'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var section = document.getElementById('<?php echo esc_js($section_id); ?>');
        if (!section) return;

        var headers = section.querySelectorAll('.faq-luxury-header');
        headers.forEach(function (header) {
            header.addEventListener('click', function () {
                var item = this.closest('.faq-luxury-item');
                var content = item.querySelector('.faq-luxury-content');
                var icon = item.querySelector('.faq-luxury-icon');
                var isOpen = item.classList.contains('faq-luxury-item--active');

                if (isOpen) {
                    item.classList.remove('faq-luxury-item--active');
                    this.setAttribute('aria-expanded', 'false');
                    if (icon) icon.textContent = '+';
                    if (typeof jQuery !== 'undefined') {
                        jQuery(content).slideUp(250);
                    } else {
                        content.style.display = 'none';
                    }
                } else {
                    item.classList.add('faq-luxury-item--active');
                    this.setAttribute('aria-expanded', 'true');
                    if (icon) icon.textContent = '−';
                    if (typeof jQuery !== 'undefined') {
                        jQuery(content).slideDown(250);
                    } else {
                        content.style.display = 'block';
                    }
                }
            });

            // Keyboard accessibility (Enter / Space)
            header.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
</script>
