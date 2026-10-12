<?php

/**
 * Template file for single posts
 */
get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <?php if ($header_image = get_field('header_image')) : ?>
        <div class="h-[16rem] lg:h-[40rem] hero-block">
            <?= wp_get_attachment_image($header_image, 'full', null, ['class' => 'w-full h-full object-cover']); ?>
            <h1 class="single-blog-title">
		        <?php
		        $title = get_the_title();
		        $words = explode(' ', $title);

		        if (count($words) > 3) {
			        $last_three = array_splice($words, -3);
			        echo implode(' ', $words) . ' <span>' . implode(' ', $last_three) . '</span>';
		        } else {
			        echo $title;
		        }
		        ?>
            </h1>

            <div class="hero-overlay"></div>
        </div>
    <?php elseif (has_post_thumbnail()) : ?>
        <div class="h-[16rem] lg:h-[40rem] hero-block">
            <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
            <h1 class="single-blog-title">
		        <?php
		        $title = get_the_title();
		        $words = explode(' ', $title);

		        if (count($words) > 3) {
			        $last_three = array_splice($words, -3);
			        echo implode(' ', $words) . ' <span>' . implode(' ', $last_three) . '</span>';
		        } else {
			        echo $title;
		        }
		        ?>
            </h1>

            <div class="hero-overlay"></div>
        </div>
    <?php endif; ?>
    <div class="max-w-screen-lg py-10 mx-auto prose container-1200">
        <div class="blog-posts-grid">
            <div class="blog-posts-grid__pagination">
                <h2><?php _e('In This Article:', 'luxury'); ?></h2>
	            <?php
	            $content = get_the_content();
	            $titles = [];

	            // Extract and store all headings with numbers (e.g., h1, h2, h3, etc.) from the content.
	            if (preg_match_all('/<h[1-6][^>]*>.*?<\/h[1-6]>/i', $content, $matches)) {
		            $titles = $matches[0];
	            }

	            if (!empty($titles)) {
		            echo '<div class="blog-posts-grid__pagination-titles">';

		            // Initialize a counter for numbering the titles.
		            $titleNumber = 1;

		            // Display the extracted titles with numbers (1. 2. 3.) within h3 tags.
		            foreach ($titles as $title) {
			            // Add the number within a span element before each title.
			            $titleWithNumber = '<h3><span class="title-number">' . $titleNumber . '. </span>' . strip_tags($title) . '</h3>';
			            echo $titleWithNumber;
			            $titleNumber++;
		            }

		            echo '</div>';
	            }

	            ?>

            </div>
            <div class="blog-posts-grid__content">
	            <?php the_content(); ?>
            </div>
            <div class="blog-posts-grid__author">
	            <?php if($avatar_blog = get_field('avatar_blog', 'options')) : ?>
                    <div class="blog-posts-grid__author-image">
			            <?php echo wp_get_attachment_image($avatar_blog, 'large')?>
                    </div>
	            <?php endif;?>
	            <?php if($title_author = get_field('title_author', 'options')) : ?>
                    <div class="blog-posts-grid__author-content">
			            <h2><?php echo $title_author; ?></h2>
	                    <?php if($subtitle_author = get_field('subtitle_author', 'options')) : ?>
                            <h3><?php echo $subtitle_author; ?></h3>
	                    <?php endif;?>
                    </div>
	            <?php endif;?>
                <div class="blog-posts-grid__links">
	                <?php if ( $email = get_field( 'email', 'options' ) ): ?>
                        <p class="contact-link contact-link--email"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
	                <?php endif; ?>
	                <?php if ( $phone = get_field( 'telephone', 'options' ) ): ?>
                        <p class="contact-link contact-link--phone"><a href="tel:<?php echo sanitize_number( $phone ); ?>"><?php echo $phone; ?></a></p>
	                <?php endif; ?>
                    <a target="_self" class="button" href="<?php echo get_permalink( get_page_by_path( 'contact-us' ) ) ?>">
                        <?php _e('Contact Us', 'Luxury'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="other-posts">
	        <?php $arg = array(
		        'post_type'      => 'post',
		        'posts_per_page' => 6,
		        'orderby'        => 'rand',
	        );
	        $the_query = new WP_Query( $arg );

	        if ( $the_query->have_posts() ) : ?>
                <h2><?php _e('Lifestyle', 'luxury'); ?></h2>
                <div class="other-posts__grid">
			        <?php while ( $the_query->have_posts() ) :
			        $the_query->the_post(); ?>
                    <div class="other-posts__item">
                        <div class="other-posts__item-content">
                            <a target="_self" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
	                            <?php the_title(); ?>
                            </a>
                        </div>
                    </div>
			        <?php endwhile; ?><!-- END of Post -->
                </div><!-- END of .post-type -->
	        <?php endif;
	        wp_reset_query(); ?>
        </div>

    </div>
<?php endwhile; ?>


<?php get_template_part('parts/schema-post'); ?>


<?php
get_footer();
