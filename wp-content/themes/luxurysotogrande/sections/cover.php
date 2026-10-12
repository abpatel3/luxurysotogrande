<?php
/**
 * Renders the content section template
 */


?>


<section class="hero-cover">
	<div class="hero-cover__overlay"></div>
	<?php if ( $bg = get_sub_field('background_image') ): ?>
		<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => 'hero-cover__image')); ?>
	<?php endif; ?>
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 z-index-5 hero-cover-block">
		<?php if ( $logo = get_sub_field('logo') ): ?>
            <a class="hero-cover__logo-image" href="<?php echo home_url(); ?>">
	            <?php echo wp_get_attachment_image($logo, 'full', false, array('class' => 'hero-cover__logo')); ?>
            </a>
		<?php endif; ?>
		<?php if ($text = get_sub_field('title')) : ?>
			<div class="hero-cover__block">
				<h1 class="hero-cover__block-text text-uppercase">
					<?php echo $text; ?>
				</h1>
				<?php if($link = get_sub_field('link')) : ?>
					<div class="hero-cover__block-button">
						<a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat text-uppercase" href="<?php echo $link['url']; ?>"
						   target="<?php echo $link['target'] ?: '_self'; ?>">
							<?php echo $link['title']; ?>
						</a>
					</div>
				<?php endif;?>
			</div>
		<?php endif; ?>
	</div>
</section>

