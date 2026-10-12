<section class="search-bar px-5 py-6 uppercase bg-lsg-gray lg:px-0">
    <h2 class="text-center text-3xl font-normal lg:text-4xl"><?php _e('Property Search', 'luxury'); ?></h2>
    <div class="">
        <!-- BEGIN of search form -->
        <form method="get" id="searchform" class="search-form grid max-w-6xl mx-auto lg:grid-cols-4 gap-x-8 gap-y-4" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label for="s" class="css-clip d-none"><?php _e( 'Search', 'luxury' ); ?></label>
            <input type="search" name="s" id="s" class="search-form__input w-full p-3 uppercase bg-white placeholder:text-black focus:placeholder:text-gray-400" placeholder="<?php _e( 'Search Property: ID, Title, Location ...', 'luxury' ); ?>" value="<?php echo get_search_query(); ?>"/>
            <label class="d-none" for="property"><?php _e( 'New Search', 'luxury' ); ?></label>
            <select name="property_status" id="listing_type" class="search-form__select w-full p-3 uppercase bg-white">
                <option value=""><?php _e( 'STATUS', 'sitemender' ); ?></option>
				<?php
				// Get terms of the listing-type taxonomy
				$listing_types = get_terms( array(
					'taxonomy' => 'property_status',
					'hide_empty' => false, // Show even if no posts are assigned
				) );

				// Loop through each term and create an option element
				foreach ( $listing_types as $listing_type ) {
					echo '<option value="' . esc_attr( $listing_type->slug ) . '">' . esc_html( $listing_type->name ) . '</option>';
				}
				?>
            </select>
            <label class="d-none" for="property_type"><?php _e( 'New Search', 'luxury' ); ?></label>
            <select name="property_type" id="property_type" class="search-form__select w-full p-3 uppercase bg-white">
                <option value=""><?php _e( 'Type', 'sitemender' ); ?></option>
		        <?php
		        // Get terms of the property-type taxonomy
		        $property_types = get_terms( array(
			        'taxonomy' => 'property_type',
			        'hide_empty' => false, // Show even if no posts are assigned
		        ) );

		        // Loop through each term and create an option element
		        foreach ( $property_types as $property_type ) {
			        echo '<option value="' . esc_attr( $property_type->slug ) . '">' . esc_html( $property_type->name ) . '</option>';
		        }
		        ?>
            </select>
            <!-- Hidden input field for specifying post type -->
            <input type="hidden" name="post_type" value="property">
            <!-- Select dropdown for listing type taxonomy -->
            <!-- Submit button -->
            <button type="submit" name="submit" class="w-full p-2 text-lg text-white uppercase bg-lsg-gold search-form__submit button-gold" id="searchsubmit" aria-label="<?php _e('Submit search','default'); ?>"><?php _e( 'Search', 'default' ); ?></button>
        </form>
        <!-- END of search form -->
    </div>
</section>