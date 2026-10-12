<article id="post-<?php the_ID(); ?>" <?php post_class( 'preview preview--' . get_post_type() ); ?>>
	<div class="row">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="col-md-4 col-sm-12 col-12 text-center medium-text-left">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail( 'large', array( 'class' => 'preview__thumb' ) ); ?>
				</a>
			</div>
		<?php else:?>
			<div class="col-md-4 col-sm-12 col-12 text-center medium-text-left">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<img alt="placeholder" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/placeholder.jpg" width="1280" height="800">
				</a>
			</div>
		<?php endif; ?>
		<div class="col col-text-main bg-lsg-gray">
			<h3 class="preview__title text-xl font-medium uppercase truncate">
				<a href="<?php the_permalink(); ?>"
				   title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'default' ), the_title_attribute( 'echo=0' ) ) ); ?>"
				   rel="bookmark"><?php echo get_the_title() ?: __( 'No title', 'default' ); ?>
				</a>
			</h3>
			<?php if ( is_sticky() ) : ?>
				<span class="secondary label preview__sticky"><?php _e( 'Sticky', 'default' ); ?></span>
			<?php endif; ?>
			<div class="preview__excerpt">
				<?php the_excerpt(); // Use wp_trim_words() instead if you need specific number of words ?>
			</div>
		</div>
	</div>
</article>