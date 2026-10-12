<?php
/**
 * Renders the content section template
 */


?>

<?php $bg = get_sub_field('background_image'); ?>

<?php $google_map = get_sub_field('google_map'); ?>


<section class="sotogrande-map">
    <?php if($google_map) : ?>
        <div class="sotogrande-map__iframe">
            <?php echo $google_map; ?>
        </div>
	<?php elseif ( $bg ): ?>
		<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => 'bg-image-sotogrande-map')); ?>
	<?php endif; ?>
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0">
		<?php if ($text = get_sub_field('text')) : ?>
			<div class="sotogrande-map__block" data-aos="fade-up" data-aos-delay="100">
				<?php echo $text; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
