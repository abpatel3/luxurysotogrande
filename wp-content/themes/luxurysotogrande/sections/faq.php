<?php
/**
 * Renders the content section template
 */


?>

<section class="faq-section">
	<div class="flex flex-col justify-center h-full max-w-6xl px-5 mx-auto lg:px-0 p-0-15">
		<?php if ($title = get_sub_field('title')) : ?>
            <h2 class="faq-section__title featured-properties-section__title">
				<?php echo $title; ?>
            </h2>
		<?php endif; ?>
		<?php if ( have_rows( 'faq' ) ): ?>
            <div class="faq-section__items">
	            <?php while ( have_rows( 'faq' ) ): the_row();
		            $title     = get_sub_field( 'title' );
		            $text      = get_sub_field( 'text' );  ?>
                    <div class="faq-item">
                        <div class="faq-item__title">
                            <h5><?php echo $title; ?></h5>
                            <span class="plus-minus"></span>
                        </div>
                        <div class="faq-item__content">
                            <p><?php echo $text; ?></p>
                        </div>
                    </div>
	            <?php endwhile; ?>
            </div>
		<?php endif; ?>
    </div>
</section>
