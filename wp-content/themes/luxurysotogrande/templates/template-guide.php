<?php
/**
 * Template Name: Guide Page
 */
get_header(); ?>




<main class="main-content-guide">
    <section class="guide-hero-section">
        <?php if($image = get_field('image')) : ?>
            <?php echo wp_get_attachment_image($image, 'full'); ?>
	    <?php elseif ( has_post_thumbnail() ) : ?>
<!--		    --><?php //echo wp_get_attachment_image(get_post_thumbnail_id(), 'full'); ?>
        <div class="guide-hero-section__image bg-lsg-gold"></div>
	    <?php endif;?>
        <h1 class="guide-hero-section__title" data-aos="fade-up" data-aos-easing="ease" data-aos-delay="400"><?php the_title(); ?></h1>
    </section>
	<?php if($text_guides = get_field('text_guide')) : ?>
        <section class="text-guides single-text-guides">
            <div class="container-980">
                <div class="text-guides__text">
					<?php echo $text_guides; ?>
                </div>
            </div>
        </section>
	<?php endif;?>
	<?php if ( have_rows( 'faq_guides') ): ?>
        <section class="faq bg-lsg-gray faq-guides">
            <div class="container-new">
                <h2 class="text-3xl font-normal lg:text-4xl"><?php _e('FAQ', 'purple');?></h2>
				<?php $index = 1;?>
                <div class="faq__grid">
					<?php while ( have_rows( 'faq_guides') ): the_row();
						$title = get_sub_field( 'title' );
						$text = get_sub_field( 'text' ); ?>
                        <div class="faq__item bg-white shadow-xl">
                            <button class="faq__item-btn ">
								<?php echo $title; ?>
                                <img width="16" height="16" alt="<?php _e('Arrow', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/arrow.svg">
                            </button>
                            <div class="faq__item-text">
								<?php echo $text; ?>
                            </div>
                        </div>
						<?php $index++; ?>
					<?php endwhile; ?>
                </div>
            </div>
        </section>
	<?php endif; ?>
   <?php $contact_guide_text = get_field('contact_guide_text', 'options'); ?>

    <?php $contact_guide_link = get_field('contact_guide_link', 'options'); ?>
	<?php if($contact_guide_text || $contact_guide_link) : ?>
        <section class="text-gold-bg bg-lsg-gold">
            <div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
                <div class="text-gold-bg__block">
					<?php if ($contact_guide_text) : ?>
                        <div class="text-gold-bg__block" data-aos="fade-up">
							<?php echo $contact_guide_text; ?>
                        </div>
					<?php endif;?>
					<?php if($contact_guide_link) : ?>
                        <div class="text-gold-bg__block-button" data-aos="fade-up" data-aos-delay="100">
                            <a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat" href="<?php echo $contact_guide_link['url']; ?>"
                               target="<?php echo $contact_guide_link['target'] ?: '_self'; ?>">
								<?php echo $contact_guide_link['title']; ?>
                            </a>
                        </div>
					<?php endif;?>
                </div>
            </div>
        </section>
	<?php endif;?>

    <!-- /Location -->
    <section class="guides-listing guides-listing-others">
        <div class="guides-listing__container">
			<?php  $parent_page = get_page_by_path('guides'); // Replace 'guides' with the slug of your guides page
			$parent_page_id = $parent_page->ID;
			// Get the current page ID
			$current_page_id = get_the_ID();
			$args = array(
				'post_type'      => 'page',
				'order'          => 'DESC',
				'orderby'        => 'rand', // Order by date
				'posts_per_page' => 3,
				'post_parent'    => $parent_page_id, // Display children pages of the guides page
				'post__not_in'   => array($current_page_id), // Exclude the current page

			);
			$the_query = new WP_Query( $args );
			if ( $the_query->have_posts() ) : ?>
                <h2 class="text-3xl font-normal lg:text-4xl guides-listing__main-heading"><?php _e('Our Other Guides', 'purple'); ?></h2>
                <div class="guides-listing__pages">
					<?php while ( $the_query->have_posts() ) :
						$the_query->the_post(); ?>
                        <div class="guides-listing__item">
                            <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
                                <div class="guides-listing__item-image">
									<?php the_post_thumbnail( 'full', array( 'class' => 'preview__thumb' ) ); ?>
                                </div>
                                <div class="guides-listing__item-title">
                                    <h4 class="text-xl font-medium uppercase truncate"><?php the_title(); ?></h4>
									<?php if($short_text = get_field('short_text')) : ?>
                                        <p><?php echo $short_text; ?></p>
									<?php endif;?>
                                </div>
                                <button class="bg-lsg-gold">
									<?php _e('Read More', 'purple'); ?>
                                </button>
                            </a>
                        </div>
					<?php endwhile; ?>
                </div>
			<?php endif;
			wp_reset_query(); ?>
        </div>
    </section>
</main>


<?php get_footer(); ?>

