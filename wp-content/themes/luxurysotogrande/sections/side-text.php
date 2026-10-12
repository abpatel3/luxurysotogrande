<?php
/**
 * Renders the content section template
 */


?>


<section class="side-text">
	<div class="side-text__container">
		<div class="side-tex__row flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 z-index-5">
			<?php if ( $text = get_sub_field('text') ): ?>
				<div class="side-text__content">
					<?php echo $text; ?>
				</div>
			<?php endif; ?>
			<?php if ( $bg = get_sub_field('image') ): ?>
				<div class="side-text__image">
					<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => '')); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
