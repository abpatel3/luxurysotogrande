<?php
/**
 * Template: New Search Section (Luxury Clean 4-Column Design)
 * Matches the design mockup exactly with dual range price slider,
 * clean editorial title, subtitle, and luxury dropdowns.
 * 
 * NOTE: This is self-contained so that parts/search-bar.php remains 100% untouched.
 */

$raw_title = get_sub_field('title');
$title = !empty($raw_title) ? $raw_title : "LET'S TAKE YOU ON A JOURNEY";
$raw_subtitle = get_sub_field('subtitle');
$subtitle     = !empty($raw_subtitle) ? $raw_subtitle : '';

// Fetch filter taxonomy options
$search_property_status = get_field('search_property_status', 'options');
$search_property_area   = get_field('search_property_area', 'options');
$search_property_types  = get_field('search_property_types', 'options');

// Current request params
$curr_status   = isset($_GET['property_status']) ? sanitize_text_field($_GET['property_status']) : '';
$curr_area     = isset($_GET['property_area']) ? sanitize_text_field($_GET['property_area']) : '';
$curr_subarea  = isset($_GET['sub-property_area']) ? sanitize_text_field($_GET['sub-property_area']) : '';
$curr_type     = isset($_GET['property_type']) ? sanitize_text_field($_GET['property_type']) : '';
$curr_bedrooms   = isset($_GET['bedrooms']) ? sanitize_text_field($_GET['bedrooms']) : '';
$curr_ref        = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$curr_start_date = !empty($_GET['wpbs-search-start-date']) ? sanitize_text_field($_GET['wpbs-search-start-date']) : '';
$curr_end_date   = !empty($_GET['wpbs-search-end-date']) ? sanitize_text_field($_GET['wpbs-search-end-date']) : '';
$curr_min        = (isset($_GET['price_min']) && is_numeric($_GET['price_min'])) ? (int)$_GET['price_min'] : (in_array($curr_status, ['holiday-rental', 'holiday-rental-es'], true) ? 0 : 650000);
$curr_max        = (isset($_GET['price_max']) && is_numeric($_GET['price_max'])) ? (int)$_GET['price_max'] : (in_array($curr_status, ['holiday-rental', 'holiday-rental-es'], true) ? 20000000 : 19550000);

// Subareas mapping for JavaScript
$sub_areas_map = [];
if (!empty($search_property_area) && is_array($search_property_area)) {
    foreach ($search_property_area as $area_term) {
        $child_terms = get_terms([
            'taxonomy'   => 'property_area',
            'parent'     => $area_term->term_id,
            'hide_empty' => false,
        ]);
        if (!empty($child_terms) && !is_wp_error($child_terms)) {
            $formatted_children = [];
            foreach ($child_terms as $ct) {
                $formatted_children[] = [
                    'slug' => $ct->slug,
                    'name' => $ct->name,
                ];
            }
            $sub_areas_map[$area_term->slug] = $formatted_children;
        }
    }
}
?>

<section class="luxury-search-section ">
    <div class="ls-container">
        
        <!-- Header Row: Title on Left, Subtitle on Right -->
        <div class="luxury-search-header">
            <h2 class="luxury-search-title">
                <?php echo esc_html($title); ?>
            </h2>
            <?php if (!empty($subtitle)) : ?>
                <p class="luxury-search-subtitle">
                    <?php echo esc_html($subtitle); ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- 4-Column Search Form Grid -->
        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="luxury-search-form" id="luxury-search-form">
            
            <!-- Column 1, Row 1: ALL PROPERTIES -->
            <div class="luxury-search-col luxury-search-col-status">
                <div class="luxury-select-wrapper">
                    <select name="property_status" id="luxury_property_status" class="luxury-select" aria-label="<?php esc_attr_e('All Properties', 'luxurysotogrande'); ?>">
                        <option value="" <?php echo empty($curr_status) ? 'selected' : ''; ?>>
                            <?php _e('ALL PROPERTIES', 'luxurysotogrande'); ?>
                        </option>
                        <?php if (!empty($search_property_status) && is_array($search_property_status)) : ?>
                            <?php foreach ($search_property_status as $status_term) : ?>
                                <option value="<?php echo esc_attr($status_term->slug); ?>" <?php echo $curr_status === $status_term->slug ? 'selected' : ''; ?>>
                                    <?php echo esc_html(strtoupper($status_term->name)); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="luxury-select-chevron">
                        <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L4.5 4L8 1" stroke="#4A453E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column 2, Row 1: ALL AREAS -->
            <div class="luxury-search-col luxury-search-col-area">
                <div class="luxury-select-wrapper">
                    <select name="property_area" id="luxury_property_area" class="luxury-select" aria-label="<?php esc_attr_e('All Areas', 'luxurysotogrande'); ?>">
                        <option value="" <?php echo empty($curr_area) ? 'selected' : ''; ?>>
                            <?php _e('ALL AREAS', 'luxurysotogrande'); ?>
                        </option>
                        <?php if (!empty($search_property_area) && is_array($search_property_area)) : ?>
                            <?php foreach ($search_property_area as $area_term) : ?>
                                <option value="<?php echo esc_attr($area_term->slug); ?>" <?php echo $curr_area === $area_term->slug ? 'selected' : ''; ?>>
                                    <?php echo esc_html(strtoupper($area_term->name)); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="luxury-select-chevron">
                        <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L4.5 4L8 1" stroke="#4A453E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column 3, Row 1: ALL SUBAREAS -->
            <div class="luxury-search-col luxury-search-col-subarea">
                <div class="luxury-select-wrapper">
                    <select name="sub-property_area" id="luxury_sub_property_area" class="luxury-select" aria-label="<?php esc_attr_e('All Subareas', 'luxurysotogrande'); ?>">
                        <option value=""><?php _e('ALL SUBAREAS', 'luxurysotogrande'); ?></option>
                    </select>
                    <span class="luxury-select-chevron">
                        <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L4.5 4L8 1" stroke="#4A453E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column 4, Row 1: DUAL RANGE PRICE SLIDER -->
            <div class="luxury-search-col luxury-search-col-price">
                <div class="luxury-price-slider-box">
                    <div class="luxury-price-labels">
                        <span id="luxury-price-min-display"><?php echo number_format($curr_min, 0, '.', ','); ?> €</span>
                        <span id="luxury-price-max-display"><?php echo number_format($curr_max, 0, '.', ','); ?> €</span>
                    </div>
                    <div id="luxury-price-slider"></div>
                    <input type="hidden" name="price_min" id="luxury_price_min" value="<?php echo esc_attr($curr_min); ?>">
                    <input type="hidden" name="price_max" id="luxury_price_max" value="<?php echo esc_attr($curr_max); ?>">
                </div>
            </div>

            <!-- Column 1, Row 2: ALL TYPES -->
            <div class="luxury-search-col luxury-search-col-type">
                <div class="luxury-select-wrapper">
                    <select name="property_type" id="luxury_property_type" class="luxury-select" aria-label="<?php esc_attr_e('All Types', 'luxurysotogrande'); ?>">
                        <option value="" <?php echo empty($curr_type) ? 'selected' : ''; ?>>
                            <?php _e('ALL TYPES', 'luxurysotogrande'); ?>
                        </option>
                        <?php if (!empty($search_property_types) && is_array($search_property_types)) : ?>
                            <?php foreach ($search_property_types as $type_term) : ?>
                                <option value="<?php echo esc_attr($type_term->slug); ?>" <?php echo $curr_type === $type_term->slug ? 'selected' : ''; ?>>
                                    <?php echo esc_html(strtoupper($type_term->name)); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="luxury-select-chevron">
                        <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L4.5 4L8 1" stroke="#4A453E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column 2, Row 2: NO. BEDROOMS -->
            <div class="luxury-search-col luxury-search-col-bedrooms">
                <div class="luxury-select-wrapper">
                    <select name="bedrooms" id="luxury_bedrooms" class="luxury-select" aria-label="<?php esc_attr_e('No. Bedrooms', 'luxurysotogrande'); ?>">
                        <option value="" <?php echo empty($curr_bedrooms) ? 'selected' : ''; ?>><?php _e('NO. BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="1" <?php echo $curr_bedrooms === '1' ? 'selected' : ''; ?>><?php _e('1 BEDROOM', 'luxurysotogrande'); ?></option>
                        <option value="2" <?php echo $curr_bedrooms === '2' ? 'selected' : ''; ?>><?php _e('2 BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="3" <?php echo $curr_bedrooms === '3' ? 'selected' : ''; ?>><?php _e('3 BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="4" <?php echo $curr_bedrooms === '4' ? 'selected' : ''; ?>><?php _e('4 BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="5" <?php echo $curr_bedrooms === '5' ? 'selected' : ''; ?>><?php _e('5 BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="6" <?php echo $curr_bedrooms === '6' ? 'selected' : ''; ?>><?php _e('6 BEDROOMS', 'luxurysotogrande'); ?></option>
                        <option value="7" <?php echo $curr_bedrooms === '7' ? 'selected' : ''; ?>><?php _e('7+ BEDROOMS', 'luxurysotogrande'); ?></option>
                    </select>
                    <span class="luxury-select-chevron">
                        <svg width="9" height="5" viewBox="0 0 9 5" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L4.5 4L8 1" stroke="#4A453E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column: Holiday Rental CHECK-IN (Visible when Holiday Rental is chosen) -->
            <div class="luxury-search-col luxury-search-col-booking luxury-search-col-booking-start" style="display: none;">
                <div class="luxury-input-wrapper">
                    <input type="text" 
                           name="wpbs-search-start-date" 
                           id="luxury_search_start_date" 
                           class="luxury-input luxury-datepicker" 
                           placeholder="<?php esc_attr_e('CHECK-IN', 'luxurysotogrande'); ?>" 
                           value="<?php if (!empty($curr_start_date)) { echo esc_attr(date('d-m-Y', strtotime($curr_start_date))); } ?>" 
                           autocomplete="off" />
                    <span class="luxury-calendar-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4A453E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column: Holiday Rental CHECK-OUT (Visible when Holiday Rental is chosen) -->
            <div class="luxury-search-col luxury-search-col-booking luxury-search-col-booking-end" style="display: none;">
                <div class="luxury-input-wrapper">
                    <input type="text" 
                           name="wpbs-search-end-date" 
                           id="luxury_search_end_date" 
                           class="luxury-input luxury-datepicker" 
                           placeholder="<?php esc_attr_e('CHECK-OUT', 'luxurysotogrande'); ?>" 
                           value="<?php if (!empty($curr_end_date)) { echo esc_attr(date('d-m-Y', strtotime($curr_end_date))); } ?>" 
                           autocomplete="off" />
                    <span class="luxury-calendar-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4A453E" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Column: REFERENCE -->
            <div class="luxury-search-col luxury-search-col-ref">
                <input type="search" 
                       name="s" 
                       id="luxury_search_ref" 
                       class="luxury-input" 
                       placeholder="<?php esc_attr_e('REFERENCE', 'luxurysotogrande'); ?>" 
                       value="<?php echo esc_attr($curr_ref); ?>"
                       autocomplete="off" />
                <input type="hidden" name="post_type" value="property">
            </div>

            <!-- Column: SEARCH BUTTON -->
            <div class="luxury-search-col luxury-search-col-submit">
                <button type="submit" name="submit" class="luxury-search-btn" id="luxury-search-submit">
                    <?php _e('SEARCH', 'luxurysotogrande'); ?>
                </button>
            </div>

        </form>

    </div>
</section>

<script>
jQuery(document).ready(function($) {
    // 0. Initialize Select2 for luxury dropdowns with wrapper parent to prevent admin-bar offset
    if (typeof $.fn.select2 !== 'undefined') {
        $('.luxury-select').each(function() {
            var $select = $(this);
            $select.select2({
                width: '100%',
                minimumResultsForSearch: Infinity,
                dropdownParent: $select.closest('.luxury-select-wrapper'),
                dropdownCssClass: 'luxury-select2-dropdown',
                containerCssClass: 'luxury-select2-container'
            });
        });
    }

    // 1. Dynamic Subareas population when Area changes
    var subAreasData = <?php echo json_encode($sub_areas_map); ?>;
    var $areaSelect = $('#luxury_property_area');
    var $subAreaSelect = $('#luxury_sub_property_area');
    var initialSubArea = "<?php echo esc_js($curr_subarea); ?>";

    function updateSubAreas(selectedArea, preselectedVal) {
        $subAreaSelect.empty().append('<option value=""><?php echo esc_js(__('ALL SUBAREAS', 'luxurysotogrande')); ?></option>');
        if (selectedArea && subAreasData[selectedArea] && subAreasData[selectedArea].length > 0) {
            $.each(subAreasData[selectedArea], function(idx, term) {
                var isSelected = (preselectedVal && preselectedVal === term.slug) ? ' selected' : '';
                $subAreaSelect.append('<option value="' + term.slug + '"' + isSelected + '>' + term.name.toUpperCase() + '</option>');
            });
            $subAreaSelect.prop('disabled', false);
        } else {
            $subAreaSelect.prop('disabled', false);
        }
        if (typeof $.fn.select2 !== 'undefined') {
            $subAreaSelect.trigger('change.select2');
        }
    }

    // Initialize on page load
    if ($areaSelect.val()) {
        updateSubAreas($areaSelect.val(), initialSubArea);
    }

    $areaSelect.on('change', function() {
        updateSubAreas($(this).val(), '');
    });

    // 2. Initialize Luxury noUiSlider
    var priceSliderEl = document.getElementById('luxury-price-slider');
    if (priceSliderEl && typeof noUiSlider !== 'undefined') {
        var startMin = <?php echo (int)$curr_min; ?>;
        var startMax = <?php echo (int)$curr_max; ?>;
        
        noUiSlider.create(priceSliderEl, {
            start: [startMin, startMax],
            connect: true,
            range: {
                'min': 0,
                'max': 20000000
            },
            step: 50000,
            format: {
                to: function(val) {
                    return Math.round(val);
                },
                from: function(val) {
                    return Number(val);
                }
            }
        });

        priceSliderEl.noUiSlider.on('update', function(values) {
            var minVal = parseInt(values[0], 10);
            var maxVal = parseInt(values[1], 10);
            $('#luxury-price-min-display').text(minVal.toLocaleString() + ' €');
            $('#luxury-price-max-display').text(maxVal.toLocaleString() + ' €');
            $('#luxury_price_min').val(minVal);
            $('#luxury_price_max').val(maxVal);
        });
    }

    // 3. Holiday Rental Check-in / Check-out Fields Toggle & Datepicker
    var $statusSelect = $('#luxury_property_status');
    var $bookingCols = $('.luxury-search-col-booking');
    var $searchForm = $('#luxury-search-form');

    function toggleLuxuryBookingFields(isUserAction) {
        var statusVal = $statusSelect.val();
        var isHoliday = (statusVal === 'holiday-rental' || statusVal === 'holiday-rental-es');

        if (isHoliday) {
            $bookingCols.show();
            $searchForm.addClass('has-holiday-rental');
            if (isUserAction && priceSliderEl && priceSliderEl.noUiSlider) {
                priceSliderEl.noUiSlider.set([0, 20000000]);
            }
        } else {
            $bookingCols.hide();
            $searchForm.removeClass('has-holiday-rental');
            $('#luxury_search_start_date').val('');
            $('#luxury_search_end_date').val('');
        }
    }

    // Initialize toggle on load
    toggleLuxuryBookingFields(false);

    // Listen on status dropdown change
    $statusSelect.on('change', function() {
        toggleLuxuryBookingFields(true);
    });

    // Initialize jQuery UI Datepicker for booking dates
    if (typeof $.fn.datepicker !== 'undefined') {
        $('#luxury_search_start_date, #luxury_search_end_date').datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: 0
        });
    }
});
</script>
