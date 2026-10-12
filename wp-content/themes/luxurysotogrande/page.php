<?php

/**
 * The page template file
 */

$property_id = '';
$property_status = '';
$property_type = '';
$property_area = '';
$price_range = [];

if (is_page(224)) {
    $property_status = 'long-term-rental';
} elseif (is_page(224)) {
    $property_status = 'long-term-rental-es';
} elseif (is_page(219)) {
    $property_status = 'for-sale';
} elseif (is_page(1068)) {
    $property_status = 'for-sale-es';
} elseif (is_page(221)) {
    $property_status = 'holiday-rental';
} elseif (is_page(1073)) {
    $property_status = 'holiday-rental-es';
}

if (!empty($_GET['pid'])) {
    $property_id = $_GET['pid'];
}

if (!empty($_GET['min_price'])) {
    $price_range[] = $_GET['min_price'];
} else {
    $price_range[] = 1000;
}

if (!empty($_GET['max_price'])) {
    $price_range[] = $_GET['max_price'];
} else {
    $price_range[] = 10000000;
}

if (!empty($_GET['type'])) {
    $property_type = get_term($_GET['type'], 'property_type')->slug;
}

if (!empty($_GET['area'])) {
    $property_area = get_term($_GET['area'], 'property_area')->slug;
}

if (!empty($_GET['subarea'])) {
    $property_area = get_term($_GET['subarea'], 'property_area')->slug;
}



get_header();
get_section_templates();
?>

<?php //if (!is_page(22842)) : ?>
<!--    <section class="max-w-6xl px-5 py-20 mx-auto lg:px-0">-->
<!--        --><?php //get_template_part('template-parts/filtered-properties'); ?>
<!--    </section>-->
<?php //endif; ?>







<?php if($categories_content = get_field('categories_content')) : ?>
    <section class="property-page-content">
        <div class="container-new-800">
            <div class="property-page-content__block">
				<?php echo $categories_content; ?>
            </div>
        </div>
    </section>
<?php endif;?>

<?php $faq_global = get_field('faq', 'options');?>
<?php $faq = get_field('faq');?>

<?php if($faq_global || $faq ) : ?>
    <section class="faq bg-lsg-gray">
        <div class="container-new">
            <h2 class="text-3xl font-normal lg:text-4xl"><?php _e('FAQ', 'purple');?></h2>
			<?php $index = 1;?>
            <div class="faq__grid">
				<?php if ( have_rows( 'faq', 'options' ) ): ?>
					<?php while ( have_rows( 'faq', 'options' ) ): the_row();
						$title = get_sub_field( 'title' );
						$text = get_sub_field( 'text' ); ?>
                        <div class="faq__item bg-white shadow-xl">
                            <button class="faq__item-btn ">
								<?php echo $title; ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <p class="faq__item-text">
								<?php echo $text; ?>
                            </p>
                        </div>
						<?php $index++; ?>
					<?php endwhile; ?>
				<?php endif; ?>
				<?php $index_second = 150;?>
				<?php if ( have_rows( 'faq') ): ?>
					<?php while ( have_rows( 'faq') ): the_row();
						$title = get_sub_field( 'title' );
						$text = get_sub_field( 'text' ); ?>
                        <div class="faq__item bg-white shadow-xl">
                            <button class="faq__item-btn ">
								<?php echo $title; ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <p class="faq__item-text">
								<?php echo $text; ?>
                            </p>
                        </div>
						<?php $index_second++; ?>
					<?php endwhile; ?>
				<?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
get_footer();
