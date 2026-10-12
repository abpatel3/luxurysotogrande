<?php
/**
 * Renders the content section template
 */


?>


<section class="banner-section">
	<div class="hero-section__overlay"></div>
	<?php if ( $bg = get_sub_field('image') ): ?>
		<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => 'bg-image-center')); ?>
	<?php endif; ?>
	<div class="banner-section__container flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0">
		<?php if ($text = get_sub_field('text')) : ?>
			<div class="banner-section__block">
				<div class="banner-section__block-text">
					<?php echo $text; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
