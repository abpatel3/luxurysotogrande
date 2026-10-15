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

    <header id="site-header" class="sticky top-0 z-50 w-full !bg-light-beige">
        <div class="ls-container h-16 flex items-center justify-between">
            
            <!-- Left: LS Brand Logo -->
            <div class="flex items-center shrink-0">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center header-logo-link" aria-label="<?php bloginfo('name'); ?>">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/luxurysotogrande-logo.svg" alt="<?php bloginfo('name'); ?>" class="h-[34px] w-auto" width="250" height="34">
                </a>
            </div>

            <!-- Center: Primary Desktop Navigation -->
            <nav class="hidden xl:flex items-center justify-center flex-1 px-8 header-navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'luxurysotogrande'); ?>">
                <?php
                $lsg_menu_tree = function_exists('lsg_get_primary_menu_tree') ? lsg_get_primary_menu_tree() : [];
                if (!empty($lsg_menu_tree)) :
                ?>
                    <ul class="header-primary-nav">
                        <?php foreach ($lsg_menu_tree as $l1_id => $l1) : 
                            $item         = $l1['item'];
                            $has_columns  = !empty($l1['columns']);
                            $has_mega     = function_exists('lsg_menu_item_has_mega') ? lsg_menu_item_has_mega($l1) : false;
                            $is_dropdown  = $has_columns && !$has_mega;
                            $is_current   = (get_queried_object_id() == $item->object_id);
                            
                            $li_classes = 'menu-item menu-item-' . $l1_id;
                            if ($has_mega) $li_classes .= ' has-megamenu';
                            if ($is_dropdown) $li_classes .= ' menu-item-has-children';
                            if ($is_current) $li_classes .= ' current-menu-item';
                        ?>
                            <li class="<?php echo esc_attr($li_classes); ?>" data-menu-id="<?php echo esc_attr($l1_id); ?>">
                                <a href="<?php echo esc_url($item->url); ?>" target="<?php echo esc_attr($item->target ?: '_self'); ?>">
                                    <?php echo esc_html($item->title); ?>
                                </a>
                                <?php if ($is_dropdown) : ?>
                                    <ul class="sub-menu">
                                        <?php foreach ($l1['columns'] as $sub_col) : 
                                            $sub_item = $sub_col['item'];
                                            $sub_current = (get_queried_object_id() == $sub_item->object_id);
                                            $sub_li_cls = 'menu-item' . ($sub_current ? ' current-menu-item' : '');
                                        ?>
                                            <li class="<?php echo esc_attr($sub_li_cls); ?>">
                                                <a href="<?php echo esc_url($sub_item->url); ?>" target="<?php echo esc_attr($sub_item->target ?: '_self'); ?>">
                                                    <?php echo esc_html($sub_item->title); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : 
                    wp_nav_menu(array(
                        'theme_location'  => 'primary',
                        'depth'           => 3,
                        'container'       => false,
                        'menu_class'      => 'header-primary-nav',
                        'fallback_cb'     => false,
                    ));
                endif;
                ?>
            </nav>

            <!-- Right: Actions (Language, Phone, Enquire, Mobile Toggle) -->
            <div class="flex items-center space-x-5 lg:space-x-6 shrink-0 header-actions">
                
                <!-- Language Switcher -->
                <div class="header-lang-switch hidden sm:flex items-center">
                    <?php
                    $wpml_languages = apply_filters('wpml_active_languages', NULL, 'skip_empty=0&orderby=custom');
                    if (!empty($wpml_languages)) {
                        $lang_count = 0;
                        foreach ($wpml_languages as $l) {
                            if ($lang_count > 0) {
                                echo '<span class="lang-divider">|</span>';
                            }
                            $flag = $l['language_code'] === 'en' ? '🇬🇧' : ($l['language_code'] === 'es' ? '🇪🇸' : '');
                            $active_cls = $l['active'] ? ' active-lang' : '';
                            echo '<a href="' . esc_url($l['url']) . '" class="header-lang-item' . $active_cls . '">';
                            if ($flag) {
                                echo '<span class="lang-flag">' . $flag . '</span>';
                            }
                            echo '<span class="lang-text">' . esc_html(strtoupper($l['language_code'])) . '</span>';
                            echo '</a>';
                            $lang_count++;
                        }
                    } elseif (has_nav_menu('language')) {
                        wp_nav_menu(array(
                            'theme_location'  => 'language',
                            'depth'           => 1,
                            'container'       => false,
                            'menu_class'      => 'language-menu flex items-center space-x-2',
                            'fallback_cb'     => false,
                        ));
                    } else {
                        ?>
                        <span class="header-lang-item active-lang">
                            <span class="lang-flag">🇬🇧</span>
                            <span class="lang-text">EN</span>
                        </span>
                        <span class="lang-divider">|</span>
                        <a href="<?php echo esc_url( home_url('/es/') ); ?>" class="header-lang-item">
                            <span class="lang-flag">🇪🇸</span>
                            <span class="lang-text">ES</span>
                        </a>
                        <?php
                    }
                    ?>
                </div>

                <!-- Phone Link -->
                <?php 
                $tel = get_field('telephone', 'options');
                if (!$tel) {
                    $tel = '+34 956 123 456';
                }
                $tel_clean = preg_replace('/[^0-9+]/', '', $tel);
                ?>
                <a href="tel:<?php echo esc_attr($tel_clean); ?>" class="header-phone-link hidden md:inline-flex group">
                    <span class="header-phone-icon">
                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
                        </svg>
                    </span>
                    <span class="header-phone-text"><?php echo esc_html($tel); ?></span>
                </a>

                <!-- Enquire CTA Button -->
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="header-enquire-btn hidden sm:inline-flex">
                    <?php _e('ENQUIRE', 'luxurysotogrande'); ?>
                </a>

                <!-- Mobile Hamburger Toggle -->
                <div class="xl:hidden flex items-center">
                    <?php get_template_part('template-parts/mobile-menu'); ?>
                </div>

            </div>
        </div>

        <!-- 6-Column Mega Menu Dropdown -->
        <?php get_template_part('template-parts/header-megamenu'); ?>
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