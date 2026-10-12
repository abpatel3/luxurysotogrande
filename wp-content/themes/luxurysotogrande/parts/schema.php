<?php
$faq_data = array();

if (have_rows('faq')) :
	while (have_rows('faq')) : the_row();
		$title = get_sub_field('title');
		$text = get_sub_field('text');

		// Append each FAQ item to the $faq_data array
		$faq_data[] = array(
			'@type' => 'Question',
			'name' => $title,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text' => $text
			)
		);
	endwhile;
endif;
?>

	<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": <?php echo json_encode($faq_data); ?>
    }
</script>



<?php get_footer(); ?>