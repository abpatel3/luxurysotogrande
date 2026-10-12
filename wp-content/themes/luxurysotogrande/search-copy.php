<?php
/**
 * Index
 *
 * Standard loop for the search result page
 */
get_header(); ?>


<section class="guides-top bg-lsg-blue">
	<div class="max-w-screen-2xl">
		<div class="guides-top__block">
			<h1 class="text-3xl font-normal lg:text-4xl"><?php printf( __( 'Search Results for: %s', 'default' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>
		</div>
	</div>
</section>
<section class="text-guides search-form-main bg-lsg-gray">
	<div class="max-w-screen-2xl">
		<h2 class="mb-4 text-2xl font-semibold text-center"><?php _e('SEARCH', 'purple'); ?></h2>
		<div class="text-guides__text">
			<!-- BEGIN of search form -->
			<form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label for="s" class="css-clip"><?php _e( 'Search', 'default' ); ?></label>
				<input type="search" name="s" id="s" class="search-form__input" placeholder="<?php _e( 'Search', 'default' ); ?>" value="<?php echo get_search_query(); ?>"/>
				<button type="submit" name="submit" class="search-form__submit  p-2 text-lg text-white uppercase bg-lsg-gold btn btn-primary" id="searchsubmit" aria-label="<?php _e('Submit search','default'); ?>"><?php _e( 'Search', 'default' ); ?></button>
			</form>
			<!-- END of search form -->

		</div>
	</div>
</section>
<div class="px-5 mx-auto my-10 max-w-screen-2xl lg:px-0 posts-container">
	<div class="row posts-list">
		<div class="col-12 col-sm-12">
			<!-- BEGIN of search results -->
			<main class="main-content">
				<?php if ( have_posts() ) : ?>
					<div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'parts/loop', 'search' ); // Post item ?>
						<?php endwhile; ?>
					</div>
				<?php else: ?>
					<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'default' ); ?></p>
				<?php endif; ?>
				<!-- BEGIN of pagination -->
				<?php bootstrap_pagination(); ?>
				<!-- END of pagination -->
			</main>
		</div>
		<!-- END of search results -->
	</div>
</div>

<?php get_footer(); ?>
