<?php $arg = array(
	'post_type'      => 'post', /*<-- Enter name of Custom Post Type here*/
	'order'          => 'DESC',
	'orderby'        => 'date',
	'posts_per_page' => 10
);
$the_query = new WP_Query( $arg );
if ( $the_query->have_posts() ) : ?>
    <div class="px-5 mx-auto my-10 max-w-screen-2xl lg:px-0 posts-container">
	    <?php if($title = get_sub_field('title_recent_blogs')) : ?>
            <h2 class="text-center text-3xl font-normal lg:text-4xl"><?php echo $title; ?></h2>
	    <?php endif;?>
        <div class="recent-blogs">
		    <?php while ( $the_query->have_posts() ) :
		    $the_query->the_post();
		    ?><!-- BEGIN of Post -->
            <div class="recent-blogs__item">
                <a href="<?php the_permalink(); ?>" target="_self">
	                <?php if (has_post_thumbnail()) : ?>
                        <div class="relative flex items-center justify-center">
			                <?php the_post_thumbnail('medium_large', ['class' => 'object-cover w-full h-60 lg:h-96 skip-lazy']); ?>
                        </div>
                    <?php else: ?>
                        <div class="relative flex items-center justify-center">
			                <img alt="placeholder" class="object-cover w-full h-60 lg:h-96 skip-lazy" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" width="1280" height="800">
                        </div>
	                <?php endif; ?>
                    <div class="flex flex-col justify-between flex-1 p-4 space-y-2 bg-lsg-gray">
                        <h2 class="text-xl font-medium uppercase truncate"><?php the_title(); ?></h2>
                        <p><?php the_field('excerpt'); ?></p>
                    </div>
                </a>
            </div>
		    <?php endwhile; ?><!-- END of Post -->
        </div><!-- END of .post-type -->
    </div>
<?php endif;
wp_reset_query(); ?>