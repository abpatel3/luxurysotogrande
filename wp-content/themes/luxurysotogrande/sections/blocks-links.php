<?php
/**
 * Block Links
 */
$title = get_sub_field( 'title' );

$background_color = get_sub_field( 'background_color' );

$reverse_columns = get_sub_field( 'reverse_columns' );

$text = get_sub_field( 'text' );

$image = get_sub_field( 'image' );

$link = get_sub_field( 'link' );


?>

<section class="blocks-links" data-aos="fade-up" style="background-color: <?php echo $background_color == true ? $background_color : '#fff'?>">
	<div class="container flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
		<?php if ( $title ): ?>
			<h2 class="blocks-links__title">
				<?php echo $title; ?>
			</h2>
		<?php endif; ?>
		<?php if ( $reverse_columns ): ?>
			<div class="blocks-links__grid-reverse blocks-links-block">
				<?php if ( $image ): ?>
					<div class="blocks-links__image">
						<?php echo wp_get_attachment_image($image, 'full', false, array('class' => 'blocks-links__image-img')); ?>
					</div>
				<?php endif; ?>
				<div class="blocks-links__content">
					<?php if ( $text ): ?>
						<div class="blocks-links__text">
							<?php echo $text; ?>
						</div>
					<?php endif; ?>
					<?php if($link) : ?>
						<div class="blocks-links-button">
							<a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat text-uppercase" href="<?php echo $link['url']; ?>"
							   target="<?php echo $link['target'] ?: '_self'; ?>">
								<?php echo $link['title']; ?>
							</a>
						</div>
					<?php endif;?>
				</div>
			</div>
		<?php else:?>
			<div class="blocks-links__grid blocks-links-block">
				<div class="blocks-links__content">
					<?php if ( $text ): ?>
						<div class="blocks-links__text">
							<?php echo $text; ?>
						</div>
					<?php endif; ?>
					<?php if($link) : ?>
						<div class="blocks-links-button">
							<a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat text-uppercase" href="<?php echo $link['url']; ?>"
							   target="<?php echo $link['target'] ?: '_self'; ?>">
								<?php echo $link['title']; ?>
							</a>
						</div>
					<?php endif;?>
				</div>
				<?php if ( $image ): ?>
					<div class="blocks-links__image">
						<?php echo wp_get_attachment_image($image, 'full', false, array('class' => 'blocks-links__image-img')); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>
</section>