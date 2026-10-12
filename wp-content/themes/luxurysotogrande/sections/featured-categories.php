<?php $blocks = get_sub_field('blocks'); ?>
<?php $title = get_sub_field('title');
$subtitle = get_sub_field('subtitle')?>

<?php $url = get_sub_field('url'); ?>

<?php $background_image = get_sub_field('background_image'); ?>

<?php if ( have_rows( 'blocks' ) ): ?>
	<section class="max-w-6xl px-5  mx-auto lg:px-0 featured-categories">
		<?php if ( $title ): ?>
            <h2 class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat">
				<?php echo $title; ?>
            </h2>
		<?php endif; ?>
		<?php if ( $subtitle  ): ?>
            <h3 class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat"><?php echo $subtitle; ?></h3>
		<?php endif; ?>
		<div class="grid-2">
			<?php while ( have_rows( 'blocks' ) ): the_row(); ?>
				<?php $title = get_sub_field('title'); ?>
				<?php $url = get_sub_field('url'); ?>
				<?php $background_image = get_sub_field('background_image'); ?>
				<?php if ( $url ): ?>
					<a target="_self" title="<?php echo $title; ?>" href="<?php echo $url; ?>">
						<?php if ( $title ): ?>
							<h3 class="inline-block w-auto px-4 py-2 text-xl uppercase md:px-8 md:py-4 md:text-3xl bg-lsg-gold text-center title-gold">
								<?php echo $title; ?>
							</h3>
						<?php endif; ?>
						<?php if ( $background_image ): ?>
							<?php echo wp_get_attachment_image( $background_image, 'full', false, ['class' => 'w-full h-full object-cover object-center'] ); ?>
						<?php endif; ?>
					</a>
				<?php endif; ?>
			<?php endwhile; ?>
		</div>
	</section>
<?php endif; ?>



