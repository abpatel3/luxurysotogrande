<div 
    class="block xl:hidden flex items-center"
    x-data="{ open: false }"
    @resize.window="() => {
        if (window.innerWidth >= 1280) {
            open = false
        }
    }"
    x-cloak
>
    <!-- Hamburger Open Button -->
    <button @click="open = true" aria-label="<?php esc_attr_e('Open Navigation Menu', 'luxurysotogrande'); ?>" class="text-[#2B2724] hover:text-[#8C7539] p-2 focus:outline-none transition">
        <?php get_template_part( 'template-parts/icons/menu' ); ?>
    </button>

    <!-- Overlay Backdrop -->
    <div
        class="fixed inset-0 bg-black/40 backdrop-blur-[2px] z-50 transition-opacity duration-300"
        :class="open ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
        @click="open = false"
    ></div>

    <!-- Mobile Drawer Panel -->
    <div 
        class="menu-mobile-wrapper fixed top-0 left-0 bottom-0 w-[320px] max-w-[85vw] h-full bg-[#FFFFFF] text-[#2B2724] z-50 shadow-2xl flex flex-col justify-between transform duration-300 ease-in-out transition-transform"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Top Drawer Header with Logo & Close Button -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-[#ECE7DE]">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="flex items-center" @click="open = false">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/luxurysotogrande-logo.svg' ); ?>" 
                     alt="<?php bloginfo('name'); ?>" 
                     class="h-7 w-auto">
            </a>
            <button @click="open = false" aria-label="<?php esc_attr_e('Close Navigation Menu', 'luxurysotogrande'); ?>" class="text-[#2B2724] hover:text-[#8C7539] p-1.5 focus:outline-none transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 overflow-y-auto">
            <?php
                wp_nav_menu( array(
                    'theme_location'  => 'primary',
                    'depth'           => 2,
                    'container'       => false,
                    'menu_class'      => 'mobile-menu',
                    'fallback_cb'     => false,
                ) );
            ?>
        </div>

        <!-- Drawer Footer: Language Switcher, Phone & Enquire -->
        <div class="px-6 py-5 border-t border-[#ECE7DE] bg-[#FAF8F5] space-y-3.5">
            <!-- Language Switcher -->
            <div class="header-lang-switch flex items-center justify-center">
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
            <div class="text-center">
                <a href="tel:<?php echo esc_attr($tel_clean); ?>" class="header-phone-link justify-center">
                    <span class="header-phone-icon">
                        <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                            <path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/>
                        </svg>
                    </span>
                    <span class="header-phone-text"><?php echo esc_html($tel); ?></span>
                </a>
            </div>

            <!-- Enquire CTA Button -->
            <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="header-enquire-btn w-full text-center">
                <?php _e('ENQUIRE', 'luxurysotogrande'); ?>
            </a>
        </div>
    </div>
</div>