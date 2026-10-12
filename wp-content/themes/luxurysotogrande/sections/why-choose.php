<?php
/**
 * Renders the content section template
 */


$right_side = get_sub_field('right_sider')
?>

<section class="why-choose <?php echo $right_side == true ? 'why-choose-right' : ''; ?>">
	<?php if ( $bg = get_sub_field('image') ): ?>
		<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => 'bg-image-why-map')); ?>
	<?php endif; ?>
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
		<?php if ($text = get_sub_field('text')) : ?>
			<div class="why-choose__block" data-aos="fade-up">
				<?php echo $text; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

