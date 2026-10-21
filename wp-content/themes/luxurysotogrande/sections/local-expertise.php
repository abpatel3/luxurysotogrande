<?php
/**
 * Local Expertise, Global Reach - Dark Luxury Value Proposition Section
 *
 * Layout: local_expertise (Flexible Content in Page Builder)
 * Matching luxury editorial design: Dark signature background (#2B2622),
 * left statement block with serif title and description, followed by 3 vertical-divided
 * stat/feature columns with gold icons.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. ACF Sub-fields
$raw_title       = get_sub_field('title');
$raw_description = get_sub_field('description');

// Check both 'features_' and 'features' for flexibility
$raw_features = get_sub_field('features_');
if (empty($raw_features)) {
    $raw_features = get_sub_field('features');
}

// Defaults
$title = !empty($raw_title) ? $raw_title : "LOCAL EXPERTISE.\nGLOBAL REACH.";
$description = !empty($raw_description) ? $raw_description : __("Whether your property is marketed publicly or privately, our bespoke approach combines thoughtful marketing, strategic advice and global reach to achieve outstanding results.", 'luxurysotogrande');

// Default 3 Features if left empty
$default_features = [
    [
        'icon'  => 'compass',
        'title' => "40+ YEARS\nIN SOTOGRANDE",
        'text'  => __("Let’s make my home your home too", 'luxurysotogrande'),
    ],
    [
        'icon'  => 'star',
        'title' => "HANDPICKED\nPORTFOLIO",
        'text'  => __("Including exclusive off-market homes", 'luxurysotogrande'),
    ],
    [
        'icon'  => 'target',
        'title' => "DIRECT\nWITH BELLA",
        'text'  => __("A personal approach from first meeting to completion", 'luxurysotogrande'),
    ],
];

$features = [];
if (!empty($raw_features) && is_array($raw_features)) {
    foreach ($raw_features as $index => $item) {
        $f_icon  = !empty($item['icon']) ? $item['icon'] : '';
        $f_title = !empty($item['title']) ? $item['title'] : '';
        $f_text  = !empty($item['text']) ? $item['text'] : '';

        // If icon not set, assign based on index (1=compass, 2=star, 3=target)
        if (empty($f_icon)) {
            $f_icon = ($index === 0) ? 'compass' : (($index === 1) ? 'star' : 'target');
        }

        if (!empty($f_title) || !empty($f_text)) {
            $features[] = [
                'icon'  => $f_icon,
                'title' => $f_title,
                'text'  => $f_text,
            ];
        }
    }
}

if (empty($features)) {
    $features = $default_features;
}
?>

<section class="local-expertise-section">
    <div class="ls-container">
        <div class="local-expertise-grid">

            <!-- Left Statement Block -->
            <div class="local-expertise-statement">
                <h2 class="expertise-title"><?php echo nl2br(esc_html($title)); ?></h2>
                <?php if (!empty($description)) : ?>
                    <p class="expertise-description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>

            <!-- Right 3 Feature Columns -->
            <div class="local-expertise-features">
                <?php foreach ($features as $f) : ?>
                    <div class="expertise-feature-col">
                        <div class="expertise-feature-icon">
                            <?php if ($f['icon'] === 'star') : ?>
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.192 21.5039C9.98669 20.2905 9.44536 18.9839 8.56802 17.5839C7.69069 16.1652 6.44002 14.8492 4.81602 13.6359C3.21069 12.4225 1.60536 11.6479 2.15769e-05 11.3119V10.1359C1.58669 9.76255 3.10802 9.07188 4.56402 8.06388C6.03869 7.03721 7.27069 5.80521 8.26002 4.36788C9.26802 2.89321 9.91202 1.43721 10.192 -0.000119209H11.368C11.536 0.933213 11.872 1.89455 12.376 2.88388C12.88 3.85455 13.524 4.78788 14.308 5.68388C15.1107 6.56121 16.0067 7.35455 16.996 8.06388C18.4707 9.10921 19.9734 9.79988 21.504 10.1359V11.3119C20.4774 11.5172 19.4134 11.9372 18.312 12.5719C17.2294 13.2065 16.2214 13.9625 15.288 14.8399C14.3547 15.6985 13.5894 16.6039 12.992 17.5559C12.1147 18.9559 11.5734 20.2719 11.368 21.5039H10.192Z" fill="currentColor"/>
                                </svg>
                                
                            <?php elseif ($f['icon'] === 'target') : ?>
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.276 20.5521C4.59202 20.5521 2.15769e-05 15.9601 2.15769e-05 10.2761C2.15769e-05 4.59206 4.59202 5.72205e-05 10.276 5.72205e-05C15.96 5.72205e-05 20.552 4.59206 20.552 10.2761C20.552 15.9601 15.904 20.5521 10.276 20.5521ZM9.40802 2.07206C5.54402 2.46406 2.49202 5.51606 2.07202 9.38006H9.40802V2.07206ZM11.2 2.07206V9.35206H18.48C18.06 5.51606 15.036 2.49206 11.2 2.07206ZM11.172 11.1441V18.4801C15.036 18.0601 18.088 15.0081 18.48 11.1441H11.172ZM2.07202 11.1721C2.49202 15.0081 5.54402 18.0881 9.38002 18.4801V11.1721H2.07202Z" fill="currentColor"/>
                                </svg>


                            <?php else : ?>
                                
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.66297e-05 10.528V9.828H3.69602C3.77068 8.72667 4.07868 7.728 4.62002 6.832C5.18002 5.91734 5.90802 5.18934 6.80402 4.648C7.71868 4.088 8.72668 3.77067 9.82802 3.696V2.38419e-06H10.556V3.696C11.6574 3.77067 12.656 4.088 13.552 4.648C14.4667 5.18934 15.1947 5.91734 15.736 6.832C16.296 7.728 16.6134 8.72667 16.688 9.828H20.384V10.528H16.688C16.632 11.6293 16.324 12.6373 15.764 13.552C15.204 14.448 14.4667 15.176 13.552 15.736C12.656 16.296 11.6574 16.6133 10.556 16.688V20.356H9.82802V16.688C8.72668 16.6133 7.71868 16.296 6.80402 15.736C5.90802 15.176 5.18002 14.448 4.62002 13.552C4.06002 12.6373 3.75202 11.6293 3.69602 10.528H1.66297e-05ZM9.82802 9.548V4.508C8.87602 4.58267 8.00802 4.86267 7.22402 5.348C6.45868 5.81467 5.83335 6.44 5.34802 7.224C4.86268 8.008 4.58268 8.876 4.50802 9.828H9.54802V10.528H4.50802C4.56402 11.48 4.83468 12.348 5.32002 13.132C5.80535 13.916 6.44002 14.5507 7.22402 15.036C8.00802 15.5213 8.87602 15.8013 9.82802 15.876V10.808H10.556V15.876C11.508 15.8013 12.376 15.5213 13.16 15.036C13.944 14.5507 14.5787 13.916 15.064 13.132C15.5494 12.348 15.82 11.48 15.876 10.528H10.836V9.828H15.876C15.8013 8.876 15.5213 8.008 15.036 7.224C14.5507 6.44 13.916 5.81467 13.132 5.348C12.3667 4.86267 11.508 4.58267 10.556 4.508V9.548H9.82802Z" fill="currentColor"/>
                                </svg>
                            <?php endif; ?>
                        </div>

                        <h3 class="expertise-feature-title"><?php echo nl2br(esc_html($f['title'])); ?></h3>

                        <?php if (!empty($f['text'])) : ?>
                            <p class="expertise-feature-text"><?php echo esc_html($f['text']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>
