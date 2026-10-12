
<?php if ( $title = get_sub_field('title') ): ?>
	<h2 class="search-section-title text-center text-3xl font-normal lg:text-4xl">
		<?php echo $title; ?>
	</h2>
<?php endif; ?>


<?php get_template_part('parts/search-bar'); ?>
