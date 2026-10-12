<?php
/**
 * Renders the content section template
 */


?>

<?php
// Retrieve the image URL from the ACF field
$background_image_url = get_sub_field('background_image'); // This should return the image URL

// Get the attachment ID from the image URL
$background_image_id = attachment_url_to_postid($background_image_url);

?>

<section class="contact-form-section" id="contact-form-section">
	<?php if ( $background_image_url ): ?>
		<?php echo wp_get_attachment_image($background_image_id, 'full', false, array('class' => 'hero-cover__image')); ?>
	<?php endif; ?>
	<div class="contact-form-section__overlay"></div>
	<div class="contact-form-section__container">
		<div class="contact-form-section__block">
			<?php if ( $text = get_sub_field('title') ): ?>
				<h2 class="contact-form-section__title">
					<?php echo $text; ?>
				</h2>
			<?php endif; ?>
			<?php if ( $contact_form = get_sub_field('contact_form') ): ?>
				<?php echo do_shortcode($contact_form) ?>
			<?php endif; ?>
		</div>
	</div>
</section>
