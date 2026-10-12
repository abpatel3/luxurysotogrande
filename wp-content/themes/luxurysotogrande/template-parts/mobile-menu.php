<div 
    class="block lg:hidden | flex items-center"
    x-data="{ open: false }"
    @resize.window="() => {
        if (window.innerWidth > 1024) {
            open = false
        }
    }"
    x-cloak
>
    <button @click="open = true" aria-label="Mobile Menu Button">
        <?php get_template_part( 'template-parts/icons/menu' ); ?>
    </button>

    <div
        class="fixed left-0 top-0 | h-screen | w-full | bg-slate-900/50 | z-40"
        :class="open ? '' : 'hidden'"
        @click.self="open = false"
    ></div>

    <div 
        class="menu-mobile-wrapper fixed inset-0 | transform | duration-200 | w-80 h-screen | bg-lsg-blue text-white | z-50 | transition-all"
        :class="open ? 'translate-x-0 ease-in' : '-translate-x-full ease-out'"
    >
        <!-- Mobile Menu -->
        <?php
            wp_nav_menu( array(
                'theme_location'  => 'primary',
                'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
                'container'       => '',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => 'mobile-menu',
                'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
            ) );
        ?>
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
</div>