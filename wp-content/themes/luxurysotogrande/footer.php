<?php wp_footer(); ?>


<!-- <div class="footer-embedcode">
    <div id="mc_embed_shell">
        <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
        <style type="text/css">
			#mc_embed_signup{background:#fff; false;clear:left; font:14px Helvetica,Arial,sans-serif;}
			/* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
			   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
        </style>
        <div id="mc_embed_signup">
            <form action="https://luxurysotogrande.us19.list-manage.com/subscribe/post?u=79e202e69f896bcf952522b28&amp;id=dd8f844a7a&amp;f_id=00ec8fe3f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                <div id="mc_embed_signup_scroll"><h2>Subscribe to our newsletter for exclusive insights and be the first to know about our listings</h2>
                    <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                    <div class="mc-field-group"><label for="mce-EMAIL">Dirección de correo electrónico <span class="asterisk">*</span></label><input type="email" name="EMAIL" class="required email" id="mce-EMAIL" value="" required=""></div><div class="mc-field-group"><label for="mce-FNAME">Nombre <span class="asterisk">*</span></label><input type="text" name="FNAME" class="required text" id="mce-FNAME" value="" required=""></div><div class="mc-field-group"><label for="mce-LNAME">Apellidos <span class="asterisk">*</span></label><input type="text" name="LNAME" class="required text" id="mce-LNAME" value="" required=""></div><div class="mc-field-group"><label for="mce-PHONE">Número de teléfono </label><input type="text" name="PHONE" class="REQ_CSS" id="mce-PHONE" value=""></div>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display: none;"></div>
                        <div class="response" id="mce-success-response" style="display: none;"></div>
                    </div><div aria-hidden="true" style="position: absolute; left: -5000px;"><input type="text" name="b_79e202e69f896bcf952522b28_dd8f844a7a" tabindex="-1" value=""></div><div class="clear"><input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Subscribe"></div>
                </div>
            </form>
        </div>
        <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[4]='PHONE';ftypes[4]='phone';fnames[3]='ADDRESS';ftypes[3]='address';fnames[5]='BIRTHDAY';ftypes[5]='birthday';fnames[6]='COMPANY';ftypes[6]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script></div>
</div> -->


<footer class="bg-lsg-brown">
    <div class=" ls-container pt-14 pb-9 space-y-9">
        <div class="grid gap-7 grid-cols-12">
        <div id="sidebar-about" class="space-y-2.5 col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/footer-ls-logo.svg' ); ?>" alt="<?php bloginfo('name'); ?>" class="w-full max-w-[188px]"></a>
            <?php dynamic_sidebar('about'); ?>
        </div>

        <div id="sidebar-import-links" class="col-span-12 sm:col-span-6 lg:col-span-2 xl:col-span-3">
            <?php dynamic_sidebar('important-links'); ?>
        </div>

        <div id="sidebar-areas" class="col-span-12 sm:col-span-6 lg:col-span-2 xl:col-span-3">
            <?php dynamic_sidebar('areas'); ?>
        </div>

        <div id="sidebar-contact" class="col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
          <?php dynamic_sidebar('contact'); ?>
        </div>
        </div>
        <hr class="border-[#4A443C]">
        <div class="grid gap-7 lg:grid-cols-2">
            <p class="text-[#C4BCAF] font-roboto text-xs">© 2026 Luxury Sotogrande. Design and Build by <a href="https://textmimedia.com">Textme Media</a></p>
            <div id="footer-bottom-menu" class="flex items-center justify-start lg:justify-end">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'bottom_footer',
                        'menu'           => 'bottom footer',
                        'container'      => false,
                        'menu_class'     => 'flex flex-wrap items-center gap-x-5 gap-y-2',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    )
                );
                ?>
            </div>
        </div>
    </div>
</footer>

</body>

</html>