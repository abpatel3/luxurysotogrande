<?php
/**
 * Template Name: NEW Search Page
 */
get_header(); ?>


<?php get_section_templates(); ?>


<?php //get_template_part('parts/search-bar'); ?>

<section class="search-bar">
    <div class="container">
        <form method="GET" class="search-dates">
            <label for="start_date">Start Date:</label>
            <input class="datepicker" type="date" id="start_date" name="start_date" value="<?php echo esc_attr($_GET['start_date'] ?? ''); ?>">

            <label for="end_date">End Date:</label>
            <input class="datepicker" type="date" id="end_date" name="end_date" value="<?php echo esc_attr($_GET['end_date'] ?? ''); ?>">

            <button class="search-form__submit w-full p-2 text-lg text-white uppercase bg-lsg-gold" type="submit">Search</button>
        </form>
    </div>
</section>


<section class="loop-search px-5 mx-auto my-10 max-w-screen-2xl lg:px-0 posts-container">
    <div class="container">
	    <?php
	    if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
		    $start_date = $_GET['start_date'];
		    $end_date = $_GET['end_date'];

		    $meta_query = [
			    'relation' => 'AND',
			    [
				    'key'     => 'start_date',
				    'value'   => $end_date,
				    'compare' => '<=',
				    'type'    => 'DATE',
			    ],
			    [
				    'key'     => 'end_date',
				    'value'   => $start_date,
				    'compare' => '>=',
				    'type'    => 'DATE',
			    ],
		    ];


		    $args = [
			    'post_type'      => 'property', // Убедитесь, что это ваш тип поста
			    'posts_per_page' => -1,
			    'meta_query'     => $meta_query,
		    ];

		    $query = new WP_Query($args);

		    if ($query->have_posts()) :
			    ?>
                    <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
					    <?php while ( $query->have_posts() ) :
						    $query->the_post();
						    get_template_part( 'parts/loop', 'teseo' ); // Post item
					    endwhile; ?>
                    </div>
			    <?php

		    else :
			    echo '<p>No properties found.</p>';
		    endif;
	    }
	    ?>
    </div>
</section>




<?php get_footer(); ?>

