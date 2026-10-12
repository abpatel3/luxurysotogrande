<?php
/**
 * Renders the content section template
 */


?>


<section class="text-gold-bg bg-lsg-gold">
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
        <div class="text-gold-bg__block">
	        <?php if ($text = get_sub_field('text')) : ?>
                <div class="text-gold-bg__block" data-aos="fade-up">
	                <?php echo $text; ?>
                </div>
	        <?php endif;?>
	        <?php if($link = get_sub_field('link')) : ?>
                <div class="text-gold-bg__block-button" data-aos="fade-up" data-aos-delay="100">
                    <a class="text-lg font-light lg:text-xl bg-lsg-gold subtitle-cat" href="<?php echo $link['url']; ?>"
                       target="<?php echo $link['target'] ?: '_self'; ?>">
				        <?php echo $link['title']; ?>
                    </a>
                </div>
	        <?php endif;?>
        </div>
	</div>
</section>
