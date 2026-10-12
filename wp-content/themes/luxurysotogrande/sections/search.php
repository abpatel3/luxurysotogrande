<section class="px-5 py-6 uppercase bg-lsg-gray lg:px-0 search-bar-section">
	<div class="search-bar">
		<!-- BEGIN of search form -->
		<form method="get" id="searchform" class="grid max-w-6xl mx-auto grid-cols-2 gap-x-8 gap-y-4" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="s" class="css-clip"><?php _e( 'Search', 'default' ); ?></label>
			<input type="search" name="s" id="s" class="w-full p-3 uppercase bg-white placeholder:text-black focus:placeholder:text-gray-400" placeholder="<?php _e( 'Search Keyword/Ref', 'default' ); ?>" value="<?php echo get_search_query(); ?>"/>
			<input type="submit" value="<?php _e( 'Search by Keyword', 'default' ); ?>" class="cursor-pointer search-form__submit  p-2 text-lg text-white uppercase bg-lsg-gold btn btn-primary cursor-pointer">
		</form>
		<!-- END of search form -->
	</div>
</section>


