<section class="search-bar">
    <div class="container">
        <div class="search-bar__grid">
            <!-- BEGIN of search form -->
            <form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="search-bar-property">
					<?php
					$search_property_status = get_field('search_property_status', 'options');
					if( $search_property_status ): ?>
                        <label class="" for="property_status"><?php _e( 'Property Status', 'default' ); ?></label>
                        <select name="property_status" id="property_status" class="search-form__select">
	                        <option value="" <?php echo empty($_GET['property_status']) ? 'selected' : ''; ?>>
								<?php _e('All Properties', 'default'); ?>
							</option>
							<?php
	                        if ( is_page_template('templates/template-rental_property.php') ) : ?>
                                <option value="<?php _e('holiday-rental', 'purple'); ?>">
                                    <?php _e('Holiday Rental', 'purple'); ?>
                                </option>
	                        <?php endif; ?>

                            <?php foreach( $search_property_status as $search_property_status_term ): ?>
                                <option value="<?php echo esc_attr( $search_property_status_term->slug ); ?>" <?php echo isset($_GET['property_status']) && $_GET['property_status'] === $search_property_status_term->slug ? 'selected' : ''; ?>>
									<?php echo esc_html( $search_property_status_term->name ); ?>
                                </option>
							<?php endforeach; ?>
                        </select>
					<?php endif; ?>
                </div>
                <div class="search-bar-area">
					<?php
					$search_property_area = get_field('search_property_area', 'options');
					if( $search_property_area ): ?>
                        <label class="" for="property_area"><?php _e( 'ALL AREAS', 'default' ); ?></label>
                        <select name="property_area" id="property_area" class="search-form__select">
                            <option value=""><?php _e( 'ALL AREAS', 'purple' ); ?></option>
							<?php foreach( $search_property_area as $search_property_area_term ): ?>
                                <option value="<?php echo esc_attr( $search_property_area_term->slug ); ?>" <?php echo isset($_GET['property_area']) && $_GET['property_area'] === $search_property_area_term->slug ? 'selected' : ''; ?>>
									<?php echo esc_html( $search_property_area_term->name ); ?>
                                </option>
							<?php endforeach; ?>
                        </select>
					<?php endif; ?>
                </div>
                <div class="search-bar-sub-area">
                    <label class="" for="sub_property_area"><?php _e( 'ALL SUBAREAS', 'default' ); ?></label>
                    <select name="sub-property_area" id="sub_property_area" class="search-form__select">
                        <option value=""><?php _e( 'ALL SUBAREAS', 'purple' ); ?></option>
                    </select>
                </div>
                <div class="price-slider-my">
                    <div id="price-slider"></div>
                    <span id="price-value"></span>
                    <!-- Hidden inputs for price range -->
                    <input type="hidden" name="price_min" id="price_min" value="0">
                    <input type="hidden" name="price_max" id="price_max" value="20000000">
                </div>
                <div class="search-bar-types">
					<?php
					$search_property_types = get_field('search_property_types', 'options');
					if( $search_property_types ): ?>
                        <label class="" for="property_type"><?php _e( 'ALL TYPES', 'default' ); ?></label>
                        <select name="property_type" id="property_type" class="search-form__select">
                            <option value=""><?php _e( 'ALL TYPES', 'purple' ); ?></option>
							<?php foreach( $search_property_types as $search_property_types_term ): ?>
                                <option value="<?php echo esc_attr( $search_property_types_term->slug ); ?>" <?php echo isset($_GET['property_type']) && $_GET['property_type'] === $search_property_types_term->slug ? 'selected' : ''; ?>>
									<?php echo esc_html( $search_property_types_term->name ); ?>
                                </option>
							<?php endforeach; ?>
                        </select>
					<?php endif; ?>
                </div>
                <div class="search-bar-bedrooms">
                    <label class="" for="bedrooms"><?php _e( 'NO. BEDROOMS', 'default' ); ?></label>
                    <select name="bedrooms" id="bedrooms" class="search-form__select">
                        <option value=""><?php _e( 'NO. BEDROOMS', 'purple' ); ?></option>
                        <option value="1" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '1' ? 'selected' : ''; ?>><?php _e( '1 BEDROOM', 'purple' ); ?></option>
                        <option value="2" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '2' ? 'selected' : ''; ?>><?php _e( '2 BEDROOMS', 'purple' ); ?></option>
                        <option value="3" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '3' ? 'selected' : ''; ?>><?php _e( '3 BEDROOMS', 'purple' ); ?></option>
                        <option value="4" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '4' ? 'selected' : ''; ?>><?php _e( '4 BEDROOMS', 'purple' ); ?></option>
                        <option value="5" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '5' ? 'selected' : ''; ?>><?php _e( '5 BEDROOMS', 'purple' ); ?></option>
                        <option value="6" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '6' ? 'selected' : ''; ?>><?php _e( '6 BEDROOMS', 'purple' ); ?></option>
                        <option value="7" <?php echo isset($_GET['bedrooms']) && $_GET['bedrooms'] === '7' ? 'selected' : ''; ?>><?php _e( '7 BEDROOMS', 'purple' ); ?></option>
                    </select>
                </div>
                <div class="search-bar-refence">
                    <label for="s" class="css-clip"><?php _e( 'REFERENCE', 'default' ); ?></label>
                    <input type="search" name="s" id="s" class="search-form__input" placeholder="<?php _e( 'REFERENCE', 'default' ); ?>" value="<?php echo get_search_query(); ?>"/>
                    <!-- Hidden input field for specifying post type -->
                    <input type="hidden" name="post_type" value="property">
                </div>
                <button type="submit" name="submit" class="search-form__submit w-full p-2 text-lg text-white uppercase bg-lsg-gold" id="searchsubmit" aria-label="<?php _e('Submit search','default'); ?>"><?php _e( 'Search', 'default' ); ?></button>
            </form>
            <!-- END of search form -->
        </div>
    </div>
</section>

<script>
    jQuery(document).ready(function($) {
        // Creating an object to store child elements
        $('select').select2({
            width: '100%',
        });
        var subAreas = {};

        // Populating subAreas object with data
		<?php foreach( $search_property_area as $search_property_area_term ): ?>
		<?php
		$child_terms = get_terms(array(
			'taxonomy' => 'property_area', // Replace with your taxonomy
			'parent'   => $search_property_area_term->term_id,
			'hide_empty' => false,
		));
		if (!empty($child_terms)): ?>
        subAreas["<?php echo esc_js($search_property_area_term->slug); ?>"] = <?php echo json_encode($child_terms); ?>;
		<?php endif; ?>
		<?php endforeach; ?>

        // Update sub_property_area options when property_area changes
        $("#property_area").change(function() {
            var selectedArea = $(this).val();
            var subAreaSelect = $("#sub_property_area");
            subAreaSelect.empty().append('<option value="">ALL SUBAREAS</option>'); // Reset sub-areas

            if (subAreas[selectedArea]) {
                $.each(subAreas[selectedArea], function(index, term) {
                    subAreaSelect.append('<option value="' + term.slug + '">' + term.name + '</option>');
                });
                subAreaSelect.prop('disabled', false); // Enable sub-area select
            } else {
                subAreaSelect.prop('disabled', true); // Disable sub-area select if no sub-areas
            }
        });

        // Initialize the sub_area based on the current value of property_area
        var initialArea = $("#property_area").val();
        if (subAreas[initialArea]) {
            $.each(subAreas[initialArea], function(index, term) {
                $("#sub_property_area").append('<option value="' + term.slug + '">' + term.name + '</option>');
            });
            $("#sub_property_area").prop('disabled', false);
        }
    });
</script>