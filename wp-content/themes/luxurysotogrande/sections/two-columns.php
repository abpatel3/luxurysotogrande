<?php
/**
 * Renders the content section template
 */


?>

<?php $bg = get_sub_field('background_color'); ?>

<?php $column_reverse = get_sub_field('column_reverse'); ?>

<?php $image = get_sub_field('image'); ?>

<?php $text = get_sub_field('text'); ?>

<section class="two-columns-section" style="background-color: <?php echo $bg; ?>">
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
		<?php if ( $column_reverse ): ?>
            <div class="two-columns-section__grid two-columns-section__grid-reverse" data-aos="fade-up">
	            <?php if ( $text ): ?>
                    <div class="two-columns-section__text">
			            <?php echo $text; ?>
                    </div>
	            <?php endif; ?>
				<?php if ( $image ): ?>
                    <div class="two-columns-section__image">
						<?php echo wp_get_attachment_image($image, 'full', false, array('class' => 'bg-image-columns')); ?>
                    </div>
				<?php endif; ?>
            </div>
		<?php else: ?>
            <div class="two-columns-section__grid" data-aos="fade-up">
				<?php if ( $image ): ?>
                    <div class="two-columns-section__image">
						<?php echo wp_get_attachment_image($image, 'full', false, array('class' => 'bg-image-columns')); ?>
                    </div>
				<?php endif; ?>
				<?php if ( $text ): ?>
                    <div class="two-columns-section__text">
						<?php echo $text; ?>
                    </div>
				<?php endif; ?>
            </div>
		<?php endif; ?>
	</div>
</section>
