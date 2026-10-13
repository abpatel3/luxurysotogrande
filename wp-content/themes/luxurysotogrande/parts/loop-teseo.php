<?php $price = get_field('property_price'); ?>
<?php 
// 13-10-2026: Initialize property_reduced_price at top to avoid PHP undefined variable warning and enable reduction banner
$property_reduced_price = get_field('property_reduced_price'); 
?>
<?php $property_id = get_field('property_id'); ?>
<?php $bedrooms = get_field('bedrooms'); ?>
<?php $bathrooms = get_field('bathrooms'); ?>
<?php $plot_size = get_field('plot_size');
$slug = get_post_field('post_name');
$poa = get_field('poa'); ?>
<?php $rented_from = get_field('rented_from'); ?>
<?php $rented_to = get_field('rented_to');
$sold = get_field('sold'); ?>
<?php
$start_date = isset($_GET['wpbs-search-start-date']) ? $_GET['wpbs-search-start-date'] : '';
$end_date   = isset($_GET['wpbs-search-end-date']) ? $_GET['wpbs-search-end-date'] : '';
if ($start_date && $end_date) {
    $date_parts = explode('-', $start_date);
    $month = $date_parts[1];
    $year  = $date_parts[2];
	
	$start_date_parts = explode('-', $start_date);
	$start_date_formatted = implode('-', array_reverse($start_date_parts));
	
	$end_date_parts = explode('-', $end_date);
	$end_date_formatted = implode('-', array_reverse($end_date_parts));

    // Construct the new query string
    $new_query = add_query_arg([
        'wpbs-start-month'      => ltrim($month, '0'), // remove leading 0
        'wpbs-start-year'       => $year,
        'wpbs-selection-start'  => $start_date_formatted,
        'wpbs-selection-end'    => $end_date_formatted,
    ], '');
}
?>

<div class="property-item-new property-<?php echo esc_attr($slug); ?>" style="overflow:hidden;">
    <?php if ($rented_from || $rented_to ) : ?>
    <div class="card-rented bg-lsg-gold">
        <h2 class="card-rented__title">
            <?php _e('RENTED: ', 'purple'); ?>
        </h2>
        <div class="card-rented__block">
            <?php if ($rented_from ) : ?>
            <p class="card-rented__from">
                <?php echo $rented_from; ?>
            </p>
            <?php endif; ?>
            <span>
                -
            </span>
            <?php if ($rented_from ) : ?>
            <p class="card-rented__to">
                <?php echo $rented_from; ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="card-top-wrapper">
        <?php if ($sold) : ?>
        <p class="sold-banner bg-lsg-gold">
            <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status' ) ) : ?>
            <?php _e('Sold', 'luxury'); ?>
            <?php else: ?>
            <?php _e('Rented', 'luxury'); ?>
            <?php endif;?>

        </p>
        <?php else: ?>
        <?php 
        // 13-10-2026: Old was isset($property_reduced_price), changed to !empty to only show banner if reduced price exists
        if (!empty($property_reduced_price)) : 
        ?>
        <p class="sold-banner bg-lsg-gold">
            <?php _e('Price Reduction', 'luxury'); ?>
        </p>
        <?php endif; ?>
        <?php endif; ?>

        <div class="card-top">
    <?php $post_password = get_post_field('post_password', get_the_ID()); ?>

    <?php if ( ! empty($post_password) ) : ?>

        <!-- Password Protected: Show only default image -->
        <div class="property-item-new-image">
            <a target="_self" href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>">
                <img class="featured-image-block" width="1280" height="800"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/POCKET-LISTING.jpg"
                    alt="Password Protected">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png"
                    width="298" height="90" alt="lsg-logo-1" class="watermark">
            </a>
        </div>

    <?php else : ?>

        <!-- Existing image + gallery carousel -->
        <div class="property-item-new-image">
            <?php if ( has_post_thumbnail() ) : ?>
                <a target="_self" href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>">
                    <?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'large', false, ['class' => 'featured-image-block']); ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png"
                        width="298" height="90" alt="lsg-logo-1" class="watermark">
                </a>
            <?php else : ?>
                <a target="_self" href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>">
                    <img class="featured-image-block" width="1280" height="800"
                        src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/POCKET-LISTING.jpg"
                        alt="No Photo">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png"
                        width="298" height="90" alt="lsg-logo-1" class="watermark">
                </a>
            <?php endif; ?>
        </div>

        <?php
        $images = get_field('gallery');
        $size = 'large';

        if ($images) :
            $images = array_slice($images, 1, 5);
            foreach ($images as $image_id) : ?>
                <div class="property-page-image">
                    <a target="_self" href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>">
                        <?php echo wp_get_attachment_image($image_id['ID'], $size, false, ['class' => 'featured-image-block']); ?>
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/lsg-logo-1.png"
                            width="298" height="90" alt="lsg-logo-1" class="watermark">
                    </a>
                </div>
            <?php endforeach;
        endif; ?>

    <?php endif; ?>
</div>
    </div>
    <div class="card-content">
        <div class="card-content__top">
            <div class="card-body">
                <a target="_self"
                    href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>">
                    <div class="card-body__top">
                        <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status' ) ) : ?>
                        <?php 
                        // 13-10-2026: Old: //$property_reduced_price = get_field('property_reduced_price');
                        // Variable $property_reduced_price is already fetched at top
                        ?>
                        <?php if ($poa) : ?>
                        <h4 class="card-body__top-price">
                            <p class="normal-price">
                                <?php _e('POA', 'purple'); ?>
                            </p>
                        </h4>
                        <?php else: ?>
                        <?php if ($price) : ?>
                        <h4
                            class="card-body__top-price <?php echo (!empty($property_reduced_price)) ? 'card-body__top-price-reduced' : ''; ?>">
                            <p class="normal-price"><?php echo number_format($price, 0, '.', '.'); ?> <span>€</span></p>
                            <?php if (!empty($property_reduced_price)) : ?>
                            <p>
                                <?php echo number_format($property_reduced_price, 0, '.', '.'); ?> <span>€</span>
                            </p>
                            <?php endif; ?>
                        </h4>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php elseif ( has_term( ['long-term-rental', 'alquiler-a-largo-plazo'], 'property_status' ) ) : ?>
                        <?php if ($price) : ?>
                        <h4 class="card-body__top-price">
                            <p class="normal-price"><?php echo number_format($price, 0, '.', '.'); ?> <span>€</span></p>
                        </h4>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($property_id) : ?>
                        <h5 class="card-body__top-id">
                            <?php echo $property_id; ?>
                        </h5>
                        <?php if (has_term( ['holiday-rental', 'holiday-rental-es'], 'property_status' ) ) : ?>
                        <?php if ($bedrooms or $bathrooms) : ?>
                        <div class="card-body__top-bed">
                            <?php if ($bedrooms) : ?>
                            <h5>
                                <span><?php _e('Beds:', 'purple'); ?></span>
                                <?php echo $bedrooms; ?>
                            </h5>
                            <?php endif; ?>
                            <?php if ($bathrooms) : ?>
                            <h5>
                                <span><?php _e('Baths:', 'purple'); ?></span>
                                <?php echo $bathrooms; ?>
                            </h5>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <h2 class="card-body__title"><?php the_title(); ?></h2>
                    <div class="card-body__excerpt">
                        <?php
				$excerpt = get_the_excerpt();
				echo wp_trim_words($excerpt, 22, '...'); // The third argument is the custom ending (set to empty string to remove '...').
				?>
                    </div>
                </a>
            </div>
        </div>
        <div class="card-content__bottom">
            <?php if (! has_term( ['holiday-rental', 'holiday-rental-es'], 'property_status' ) ) : ?>
            <div class="card-body__bottom">
                <?php if ($bedrooms) : ?>
                <div class="card-body__bottom-bed">
                    <span><?php _e('Beds:', 'purple'); ?></span>
                    <?php echo $bedrooms; ?>
                </div>
                <?php endif; ?>
                <?php if ($bathrooms) : ?>
                <div class="card-body__bottom-bed">
                    <span><?php _e('Baths:', 'purple'); ?></span>
                    <?php echo $bathrooms; ?>
                </div>
                <?php endif; ?>
                <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status' ) ) : ?>
                <?php if( have_rows('internal_size') ): ?>
                <?php while( have_rows('internal_size') ): the_row();
				    $size = get_sub_field('size');
				    $postfix_text = get_sub_field('postfix_text'); ?>
                <?php if ($size) : ?>
                <div class="card-body__size">
                    <span><?php _e('Internal:', 'purple'); ?></span>
                    <?php echo $size; ?>
                    <?php if ($postfix_text) : ?>
                    <?php echo $postfix_text; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endwhile; ?>
                <?php endif; ?>
                <?php endif; ?>

                <?php if ( has_term( ['for-sale', 'for-sale-es'], 'property_status' ) ) : ?>
                <?php if( have_rows('plot_size') ): ?>
                <?php while( have_rows('plot_size') ): the_row();
				    $size = get_sub_field('size');
				    $postfix_text = get_sub_field('postfix_text'); ?>
                <?php if ($size) : ?>
                <div class="card-body__size">
                    <span><?php _e('Plot:', 'purple'); ?></span>
                    <?php echo $size; ?>
                    <?php if ($postfix_text) : ?>
                    <?php echo $postfix_text; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endwhile; ?>
                <?php endif; ?>
                <?php endif; ?>

            </div>
            <?php endif; ?>
            <?php if(!empty($start_date) && !empty($end_date)){ ?>
            <?php 
			$start = new DateTime($start_date);
    		$end = new DateTime($end_date);
    		$diff = $start->diff($end); 
					
			$calendar_id = get_field('calendar', get_the_ID());
			$cleaning_fee = get_field('cleaning_fee', get_the_ID());
			$security_deposit = get_field('security_deposit', get_the_ID());
	
			// Create an array to hold prices
			$price_groups = [];

			// Loop through each day
			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($start, $interval, $end); // Excludes $end itself

			foreach ($period as $date) {
				$year = $date->format('Y');
				$month = $date->format('n');
				$day = $date->format('j');

				global $wpdb;
				$price = $wpdb->get_var( $wpdb->prepare(
					"SELECT price FROM wp_awzpvncy_wpbs_events 
					 WHERE calendar_id = %d AND date_year = %d AND date_month = %d AND date_day = %d",
					$calendar_id, $year, $month, $day
				));

				if ($price !== null) {
					if (!isset($price_groups[$price])) {
						$price_groups[$price] = 0;
					}
					$price_groups[$price]++;
				}
			}
			$total_price = !empty($cleaning_fee) ? $cleaning_fee : 0;
		?>
            <div class="card-body__bottom_price_calc" style="padding: 0px 20px 0 20px;">
                <?php if(!empty($price_groups)){ ?>
                <table class="w-full mb-5">
                    <tbody>
                        <tr>
                            <td>Start Date</td>
                            <td class="text-right"><?php echo date('j F Y', strtotime($start_date)); ?></td>
                        </tr>
                        <tr>
                            <td>End Date</td>
                            <td class="text-right"><?php echo date('j F Y', strtotime($end_date)); ?></td>
                        </tr>
                        <?php foreach ($price_groups as $price => $count) { ?>
                        <?php $subtotal = $price * $count; ?>
                        <tr>
                            <td><?php echo $price; ?>€ × <?php echo $count; ?></td>
                            <td class="text-right"><?php echo $subtotal; ?>€</td>
                        </tr>
                        <?php $total_price += $subtotal; ?>
                        <?php } ?>
                        <?php if(!empty($cleaning_fee)){ ?>
                        <tr>
                            <td>Cleaning Fee</td>
                            <td class="text-right"><?php echo $cleaning_fee; ?>€</td>
                        </tr>
                        <?php } ?>
                        <?php if($total_price != 0 && !empty($price_groups)){ ?>
                        <tr>
                            <td class="font-bold">Total</td>
                            <td class="text-right font-bold">
                                <?php echo $total_price; ?><?php if($total_price != 0){ ?>€<?php } ?></td>
                        </tr>
                        <?php } ?>
                        <?php if(!empty($security_deposit)){ ?>
                        <tr>
                            <td>Security Deposit</td>
                            <td class="text-right">
                                <?php echo $security_deposit; ?><?php if (strpos($security_deposit, '%') == false) { ?>€<?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <?php $post_id = get_the_ID(); ?>
                <?php $password = get_post_field( 'post_password', $post_id ); ?>
                <?php if ( ! empty( $password ) ) { ?>
                <p style="padding-bottom: 20px;color: #b09d76;"><span class="font-bold">PASSWORD PROTECTED</span> - For
                    More
                    Info Please Enquire</p>
                <?php } else { ?>
                <p style="padding-bottom: 20px;color: #b09d76;"><span class="font-bold">POCKET LISTING</span> - PRICE ON APPLICATION.</p>
                <?php } ?>
                <?php } ?>
            </div>
            <?php } ?>
            <div class="card-body__bottom_btn" style="padding: 0px 20px 20px 20px;">
                <a href="<?php the_permalink()?><?php if(!empty($new_query)){ echo esc_url($new_query); }; ?>"
                    class="btn">Enquire Now</a>
            </div>
        </div>
    </div>
</div>