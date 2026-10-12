<?php
/**
 * Renders the content section template
 */


?>


<section class="contacts">
	<div class="contacts__container">
		<?php if ( $title = get_sub_field('title') ): ?>
			<h1 class="contacts__title">
				<?php echo $title; ?>
			</h1>
		<?php endif; ?>
		<div class="contacts__block">
			<div class="contacts__row">
				<div class="contacts__image" data-aos="zoom-in">
					<?php if($image = get_sub_field('image')) : ?>
						<?php echo wp_get_attachment_image($image, 'full'); ?>
					<?php endif;?>
				</div>
				<div class="contacts__form">
					<div class="contacts__form-space"></div>
					<?php if ( $contact_form = get_sub_field('contact_form') ): ?>
						<div data-aos="fade-up" data-aos-easing="ease" data-aos-delay="400">
							<?php echo do_shortcode( $contact_form ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( have_rows( 'columns' ) ): ?>
				<div class="contacts__columns">
					<div class="contacts__columns-row">
						<?php while ( have_rows( 'columns' ) ): the_row(); ?>
							<?php if ( $text = get_sub_field('text') ): ?>
								<div class="contacts-column-text">
									<?php echo $text; ?>
								</div>
							<?php endif; ?>
						<?php endwhile; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
