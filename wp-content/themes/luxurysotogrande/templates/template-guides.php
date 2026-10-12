<?php
/**
 * Template Name: Guides Page
 */
get_header(); ?>




<main class="main-content-guides">
	<section class="guides-top bg-lsg-blue">
		<div class="max-w-screen-2xl">
			<div class="guides-top__block">
				<h1 class="text-3xl font-normal lg:text-4xl"><?php the_title(); ?></h1>
			</div>
		</div>
	</section>
	<?php if($text_guides = get_field('text_guides')) : ?>
		<section class="text-guides">
			<div class="max-w-screen-2xl">
				<div class="text-guides__text">
					<?php echo $text_guides; ?>
				</div>
			</div>
		</section>
	<?php endif;?>
	<section class="guides-listing">
        <div class="guides-listing__container">
	        <?php $parent_page_id = get_the_ID(); // Get the ID of the current page

	        $arg = array(
		        'post_type'      => 'page',
		        'order'          => 'DESC',
		        'orderby'        => 'date', // Order by date
		        'posts_per_page' => -1,
		        'post_parent'    => $parent_page_id, // Display children pages of the current page
	        );
	        $the_query = new WP_Query( $arg );
	        if ( $the_query->have_posts() ) : ?>
                <h2 class="text-3xl font-normal lg:text-4xl guides-listing__main-heading"><?php _e('OUR GUIDES', 'purple'); ?></h2>
                <div class="guides-listing__pages">
			        <?php while ( $the_query->have_posts() ) :
			        $the_query->the_post(); ?>
                        <div class="guides-listing__item" data-aos="fade-up">
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
		                            <?php _e('READ MORE', 'purple'); ?>
                                </button>
                            </a>
                        </div>
			        <?php endwhile; ?>
                </div>
	        <?php endif;
	        wp_reset_query(); ?>
        </div>
    </section>
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
</main>


<?php get_footer(); ?>

