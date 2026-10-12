<?php
/**
 * Renders the content section template
 */


?>


<section class="hero-section">
    <div class="hero-section__overlay"></div>
	<?php if ( $bg = get_sub_field('image') ): ?>
		<?php echo wp_get_attachment_image($bg, 'full', false, array('class' => 'bg-image-center')); ?>
	<?php endif; ?>
    <div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0">
	    <?php if ($text = get_sub_field('text')) : ?>
		    <div class="hero-section__block">
                <div class="hero-section__block-text">
                    <?php echo $text; ?>
                </div>
			    <?php if($link = get_sub_field('link')) : ?>
                    <div class="hero-section__block-button">
                        <a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat" href="<?php echo $link['url']; ?>"
                           target="<?php echo $link['target'] ?: '_self'; ?>">
						    <?php echo $link['title']; ?>
                        </a>
                    </div>
			    <?php endif;?>
            </div>
	    <?php endif; ?>
    </div>
</section>


