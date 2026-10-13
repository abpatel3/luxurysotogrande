<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php // get_template_part('template-parts/seo-meta-tags');
    ?>

    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/glide.min.js"></script>
<!--wp-headers-and-footers Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '689537527375045');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=689537527375045&ev=PageView&noscript=1"
/></noscript>
<!--wp-headers-and-footers End Meta Pixel Code -->
	
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-821110381"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-821110381');
</script>
    <script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/79e202e69f896bcf952522b28/e2f7a50f2be763db86df908bb.js");</script>
</head>

<body <?php body_class(); ?>>
<!-- Start cookieyes banner -->
<!--<script id="cookieyes" type="text/javascript" src="https://cdn-cookieyes.com/client_data/f860ac93c0d72911ecead176/script.js"></script>-->
<!-- End cookieyes banner -->
    <?= get_field('body_scripts', 'option'); ?>

    <header class="sticky top-0 z-50 w-full 123">
        <div class="px-5 py-3 lg:p-0 bg-lsg-blue header-small">
            <div class="flex items-center justify-end p-3 mx-auto text-white md:justify-between flex-flow md:flex-row max-w-screen-2xl">
                <div class="grid justify-center w-full space-y-5 text-center md:flex md:space-x-5 md:space-y-0 md:flex-row lg:justify-end">
                    <div>
                        <?php
                        wp_nav_menu(array(
                            'theme_location'  => 'language',
                            'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
                            'container'       => '',
                            'container_class' => '',
                            'container_id'    => '',
                            'menu_class'      => 'language-menu flex justify-center space-x-2'
                        ));
                        ?>
                    </div>
                    <!-- Contact -->
                    <a href="tel:<?php the_field('telephone', 'options'); ?>" class="flex items-center justify-center">
                        <?php get_template_part('template-parts/icons/telephone', null, ['classes' => 'w-5 fill-current mr-2']); ?>
                        <span><?php the_field('telephone', 'options'); ?></span>
                    </a>
                    <a href="mailto:<?php the_field('email', 'options'); ?>" class="flex items-center justify-center">
                        <?php get_template_part('template-parts/icons/email', null, ['classes' => 'w-5 fill-current mr-2']); ?>
                        <span><?php the_field('email', 'options'); ?></span>
                    </a>
                    <button id="OpenForm">
                        <img alt="<?php _e('Search Purple', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/search_icon.svg" width="20" height="20">
                    </button>
                </div>
                <div id="SearchForm" class="header__links-form">
                    <!-- BEGIN of search form -->
                    <form method="get" id="searchform" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <label for="s" class="css-clip"><?php _e( 'Search', 'default' ); ?></label>
                        <input type="search" name="s" id="s" class="search-form__input" placeholder="<?php _e( 'Search...', 'default' ); ?>" value="<?php echo get_search_query(); ?>"/>
                        <img alt="<?php _e('Search Form Icon', 'purple'); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/search_icon.svg" width="20" height="20">
                    </form>
                    <!-- END of search form -->
                </div>
            </div>
        </div>
        <div class="bg-lsg-gold header-background">
            <div class="flex items-center justify-between p-4 mx-auto text-white max-w-screen-2xl lg:p-0 header-row">
                <?php if ($id = get_field('logo', 'options')) : ?>
                    <a class="header-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo wp_get_attachment_image($id, 'full', null, ['class' => 'w-3/5 lg:w-auto skip-lazy']); ?></a>
                <?php endif; ?>
                <div class="flex items-center">
                    <?php
                    wp_nav_menu(array(
                        'theme_location'  => 'primary',
                        'depth'           => 3, // 1 = no dropdowns, 2 = with dropdowns.
                        'container'       => '',
                        'container_class' => '',
                        'container_id'    => '',
                        'menu_class'      => 'primary-menu | hidden lg:flex',
                        'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                    ));
                    ?>
                    <!-- Search -->
                    <?php // get_template_part( 'template-parts/search' ); 
                    ?>
                    <!-- Mobile Menu -->
                    <?php get_template_part('template-parts/mobile-menu'); ?>
                </div>
            </div>
        </div>
    </header>
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-821110381">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-821110381');
</script>