<section class="search-bar">
    <div class="container">
        <div class="search-bar__grid">
            <!-- BEGIN of search form -->
            <form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="search-bar-property">
					<?php
					$search_property_status = get_field('search_property_status', 'options');

					// Get full URL path
					$current_path = trim($_SERVER['REQUEST_URI'], '/');

					// Define custom mappings: URL keywords => term slugs
					$url_to_status_map = [
						'holiday-rental' => 'holiday-rental',
						'sotogrande-villas-for-sale' => 'for-sale',
						'immobiliaria-sotogrande' => 'for-sale-es',
						'alquiler-de-vacaciones' => 'holiday-rental-es',
					];

					// Find matched property status slug based on URL
					$matched_slug = '';
					foreach ($url_to_status_map as $url_part => $slug) {
						if (stripos($current_path, $url_part) !== false) {
							$matched_slug = $slug;
							break;
						}
					}

					if( $search_property_status ): ?>
						<label for="property_status"><?php _e( 'Property Status', 'default' ); ?></label>
						<select name="property_status" id="property_status" class="search-form__select">
							<option value="" <?php echo (empty($_GET['property_status']) && !$matched_slug) ? 'selected' : ''; ?>>
								<?php _e('All Properties', 'purple'); ?>
							</option>

							<?php foreach( $search_property_status as $search_property_status_term ): 
								$term_slug = $search_property_status_term->slug;

								$is_selected = (
									(isset($_GET['property_status']) && $_GET['property_status'] === $term_slug) ||
									(!isset($_GET['property_status']) && $matched_slug === $term_slug)
								);
							?>
								<option value="<?php echo esc_attr($term_slug); ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
									<?php echo esc_html($search_property_status_term->name); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php endif; ?>
				</div>
				
                <div class="search-bar-area">
					<?php
						$search_property_area = get_field('search_property_area', 'options');

						$current_path = trim($_SERVER['REQUEST_URI'], '/');

						// Detect language
						if (function_exists('apply_filters')) {
							$current_lang = apply_filters('wpml_current_language', null);
						} else {
							$current_lang = (strpos($current_path, 'es/') !== false) ? 'es' : 'en';
						}

					// Define separate mappings for each language
					$url_to_area_map_en = [
						'all-sotogrande' => 'all-sotogrande',
						'sotogrande-alto' => 'sotogrande-alto',
						'sotogrande-costa' => 'sotogrande-costa',
						'sotogrande-la-reserva' => 'sotogrande-la-reserva',
						'sotogrande-marina' => 'sotogrande-marina',
						'sotogrande-playa' => 'sotogrande-playa',
						'sotogrande-puerto' => 'sotogrande-puerto',
						'san-roque-club' => 'san-roque-club',
						'la-alcadeisa' => 'la-alcaidesa',
						'torreguadiaro' => 'torreguadiaro',
					];

					$url_to_area_map_es = [
						'todo-sotogrande' => 'all-sotogrande-es',
						'sotogrande-alto' => 'sotogrande-alto-es',
						'sotogrande-costa' => 'sotogrande-costa-es',
						'sotogrande-la-reserva' => 'sotogrande-la-reserva-es',
						'sotogrande-marina' => 'sotogrande-marina-es',
						'sotogrande-playa' => 'sotogrande-playa-es',
						'sotogrande-puerto' => 'sotogrande-puerto-es',
						'san-roque-club' => 'san-roque-club-es',
						'la-alcadeisa' => 'la-alcaidesa-es',
						'torreguadiaro' => 'torreguadiaro-es',
						'sotogrande' => 'all-sotogrande-es',
						'puerto-deportivo-de-sotogrande' => 'sotogrande-marina-es',
						'la-alcadeisa' => 'la-alcaidesa-es', // typo correction included
					];

					// Choose the correct map
					// Choose correct map
					$url_to_area_map = ($current_lang === 'es') ? $url_to_area_map_es : $url_to_area_map_en;

					// Break path into segments for exact match
					$path_segments = explode('/', $current_path);

					$matched_area_slug = '';

					// First try exact match by segment
					foreach ($path_segments as $segment) {
						if (array_key_exists($segment, $url_to_area_map)) {
							$matched_area_slug = $url_to_area_map[$segment];
							break;
						}
					}

					// Fallback: loose match using stripos if nothing matched
					if (!$matched_area_slug) {
						foreach ($url_to_area_map as $url_part => $area_slug) {
							if (stripos($current_path, $url_part) !== false) {
								$matched_area_slug = $area_slug;
								break;
							}
						}
					}

					if ($search_property_area): ?>
						<label for="property_area"><?php _e('ALL AREAS', 'default'); ?></label>
						<select name="property_area" id="property_area" class="search-form__select">
							<option value="" <?php echo (empty($_GET['property_area']) && !$matched_area_slug) ? 'selected' : ''; ?>>
								<?php _e('ALL AREAS', 'purple'); ?>
							</option>
							<?php foreach ($search_property_area as $search_property_area_term):
								$term_slug = $search_property_area_term->slug;
								$is_selected = (
									(isset($_GET['property_area']) && $_GET['property_area'] === $term_slug) ||
									(!isset($_GET['property_area']) && $matched_area_slug === $term_slug)
								);
								?>
								<option value="<?php echo esc_attr($term_slug); ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
									<?php echo esc_html($search_property_area_term->name); ?>
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
				<div class="search-bar-booking-start">
                    <label for="wpbs_search_start_date" class="css-clip"><?php _e( 'Check-in', 'default' ); ?></label>
					<input type="text" name="wpbs-search-start-date" id="wpbs-search-start-date" class="search-form__input datepicker" placeholder="<?php _e( 'Check-in', 'default' ); ?>" value="<?php if ( ! empty( $_GET['wpbs-search-start-date'] ) ) { echo esc_attr( date( 'd-m-Y', strtotime( $_GET['wpbs-search-start-date'] ) ) ); } ?>"/>

                    <!-- Hidden input field for specifying post type -->
                </div>
				<div class="search-bar-booking-end">
                    <label for="wpbs_search_end_date" class="css-clip"><?php _e( 'Check-out', 'default' ); ?></label>
					<input type="text" name="wpbs-search-end-date" id="wpbs-search-end-date" class="search-form__input datepicker" placeholder="<?php _e( 'Check-out', 'default' ); ?>" value="<?php if ( ! empty( $_GET['wpbs-search-end-date'] ) ) { echo esc_attr( date( 'd-m-Y', strtotime( $_GET['wpbs-search-end-date'] ) ) ); } ?>"/>

                    <!-- Hidden input field for specifying post type -->
				</div>
				<div class="search-bar-refence">
                    <label for="s" class="css-clip"><?php _e( 'REFERENCE', 'default' ); ?></label>
                    <input type="search" name="s" id="s" class="search-form__input" placeholder="<?php _e( 'REFERENCE', 'default' ); ?>" value="<?php echo get_search_query(); ?>"/>
                    <!-- Hidden input field for specifying post type -->
                    <input type="hidden" name="post_type" value="property">
                </div>
                <button type="submit" name="submit" class="search-form__submit w-full p-2 text-lg text-white uppercase bg-lsg-gold" id="searchsubmit" aria-label="<?php _e('Submit search','default'); ?>"><?php _e( 'Search', 'default' ); ?></button>
            </form>
			<script>
				jQuery(document).ready(function($) {
					$('#wpbs-search-start-date, #wpbs-search-end-date').datepicker({
						dateFormat: 'dd-mm-yy', // This gives DD-MM-YYYY format
						minDate: 0 // Optional: disables past dates
					});
				});
			</script>
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
	jQuery(document).ready(function($) {
		function toggleBookingFields() {
			var selected = $('#property_status').val();

			if (selected === 'holiday-rental') {
				$('.search-bar-booking-start, .search-bar-booking-end').show();
			} else {
				$('.search-bar-booking-start, .search-bar-booking-end').hide();

				// Clear values
				$('#wpbs-search-start-date').val('');
				$('#wpbs-search-end-date').val('');
			}
		}

		// Run on page load
		toggleBookingFields();

		// Run on dropdown change
		$('#property_status').on('change', function() {
			toggleBookingFields();
		});
	});
</script>