<?php
/**
 * Header Mega Menu Template Part (Fully Dynamic 3-Level Hierarchy)
 * 
 * Works directly from WP-Admin -> Appearance -> Menus:
 * - Level 1 (Top Level): Main Menu (Header bar) -> e.g. "For Sale", "Holiday Rental"
 * - Level 2 (1 indent): Column Header -> e.g. "Sotogrande Costa", "Sotogrande Alto"
 * - Level 3 (2 indents): Column Submenu links -> e.g. "Paseo del Rio", "El Casar"
 */

$menu_tree = function_exists('lsg_get_primary_menu_tree') ? lsg_get_primary_menu_tree() : [];

// Find all Level 1 items that have Mega Menus (i.e. at least one column has child items/pages)
$mega_items = [];
if (!empty($menu_tree)) {
    foreach ($menu_tree as $l1_id => $l1) {
        $has_mega = function_exists('lsg_menu_item_has_mega') ? lsg_menu_item_has_mega($l1) : !empty($l1['columns']);
        if ($has_mega) {
            $mega_items[$l1_id] = $l1;
        }
    }
}
?>

<?php if (!empty($mega_items)) : ?>
    <!-- DYNAMIC MEGA MENUS PARSED FROM WORDPRESS ADMIN -->
    <?php foreach ($mega_items as $l1_id => $l1) : 
        $main_item = $l1['item'];
        $cols = $l1['columns'];
    ?>
        <div id="megamenu-panel-<?php echo esc_attr($l1_id); ?>" 
             class="header-megamenu-panel"
             data-menu-id="<?php echo esc_attr($l1_id); ?>"
             role="region" 
             aria-label="<?php echo esc_attr($main_item->title); ?> <?php esc_attr_e('Mega Menu', 'luxurysotogrande'); ?>">
            
            <div class="megamenu-inner-container">
                <div class="megamenu-grid">
                    <?php foreach ($cols as $col_id => $col) : 
                        $col_item  = $col['item'];
                        $col_title = $col_item->title;
                        $col_url   = (!empty($col_item->url) && $col_item->url !== '#' && $col_item->url !== '') ? $col_item->url : '';
                        
                        // Check if this column has dynamic child posts/pages from helper
                        $col_data  = function_exists('lsg_get_megamenu_col_items') ? lsg_get_megamenu_col_items($col) : ['items' => $col['sub_items'] ?? [], 'all_link' => null, 'is_auto' => false];
                        $sub_items = $col_data['items'];
                        $all_link  = $col_data['all_link'];
                    ?>
                        <div class="megamenu-col">
                            <div class="megamenu-col-header">
                                <?php if ($col_url) : ?>
                                    <a href="<?php echo esc_url($col_url); ?>" class="megamenu-heading megamenu-heading-link">
                                        <?php echo esc_html($col_title); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="megamenu-heading"><?php echo esc_html($col_title); ?></span>
                                <?php endif; ?>
                                <span class="megamenu-heading-line"></span>
                            </div>

                            <?php if (!empty($sub_items) || !empty($all_link)) : ?>
                                <ul class="megamenu-list">
                                    <?php if (!empty($sub_items)) : ?>
                                        <?php foreach ($sub_items as $sub) : 
                                            $sub_classes = is_array($sub->classes) ? $sub->classes : [];
                                            $is_all_link = in_array('all-link', $sub_classes) || 
                                                           in_array('view-all', $sub_classes) || 
                                                           (stripos($sub->title, 'All ') === 0) || 
                                                           (stripos($sub->title, 'Todos ') === 0) || 
                                                           (strpos($sub->title, '→') !== false) || 
                                                           (strpos($sub->title, '&rarr;') !== false);
                                            $li_class = $is_all_link ? ' class="megamenu-all-link"' : '';
                                            $target   = !empty($sub->target) ? $sub->target : '_self';
                                        ?>
                                            <li<?php echo $li_class; ?>>
                                                <a href="<?php echo esc_url($sub->url); ?>" target="<?php echo esc_attr($target); ?>">
                                                    <?php echo esc_html($sub->title); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($all_link)) : ?>
                                        <li class="megamenu-all-link">
                                            <a href="<?php echo esc_url($all_link->url); ?>">
                                                <?php echo esc_html($all_link->title); ?> &rarr;
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php else : ?>
    <!-- DEFAULT FALLBACK IF NO WP-ADMIN MENU HAS 3-LEVEL STRUCTURE YET -->
    <?php
    if (!function_exists('lsg_megamenu_url')) {
        function lsg_megamenu_url($path) {
            $page = get_page_by_path($path);
            if ($page) {
                return get_permalink($page->ID);
            }
            return home_url('/' . ltrim($path, '/') . '/');
        }
    }
    ?>
    <div id="header-megamenu-panel" 
         class="header-megamenu-panel is-fallback"
         role="region" 
         aria-label="<?php esc_attr_e('Properties Mega Menu', 'luxurysotogrande'); ?>">
        
        <div class="megamenu-inner-container">
            <div class="megamenu-grid">

                <!-- Column 1: BROWSE -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('BROWSE', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('properties-for-sale') ); ?>"><?php _e('All Properties for Sale', 'luxurysotogrande'); ?></a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('new-developments') ); ?>"><?php _e('New Developments', 'luxurysotogrande'); ?></a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('off-market-collection') ); ?>"><?php _e('Off-Market Collection', 'luxurysotogrande'); ?></a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('invitation-only') ); ?>"><?php _e('Invitation Only', 'luxurysotogrande'); ?></a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('plots-projects') ); ?>"><?php _e('Plots & Projects', 'luxurysotogrande'); ?></a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('recently-sold') ); ?>"><?php _e('Recently Sold', 'luxurysotogrande'); ?></a></li>
                    </ul>
                </div>

                <!-- Column 2: SOTOGRANDE COSTA -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('SOTOGRANDE COSTA', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/el-casar') ); ?>">El Casar</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/el-polo') ); ?>">El Polo</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/kings-queens') ); ?>">Kings & Queens</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/la-loma') ); ?>">La Loma</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/lacasitos') ); ?>">Lacasitos</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/los-granados') ); ?>">Los Granados</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/polo-gardens') ); ?>">Polo Gardens</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/villas-de-paniagua') ); ?>">Villas de Paniagua</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/zone-a') ); ?>">Zone A</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa/zone-b') ); ?>">Zone B</a></li>
                        <li class="megamenu-all-link">
                            <a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-costa') ); ?>">
                                <?php _e('All Sotogrande Costa', 'luxurysotogrande'); ?> &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: SOTOGRANDE ALTO -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('SOTOGRANDE ALTO', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/14-homes') ); ?>">14 Homes</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/casas-cortijo') ); ?>">Casas Cortijo</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/hacienda-valderrama') ); ?>">Hacienda Valderrama</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/las-cimas') ); ?>">Las Cimas</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/las-terrazas') ); ?>">Las Terrazas</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/los-altos-de-valderrama') ); ?>">Los Altos de Valderrama</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/los-carmenes') ); ?>">Los Carmenes</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/los-gazules') ); ?>">Los Gazules</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/los-patios-de-valderrama') ); ?>">Los Patios de Valderrama</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/mirador-del-golf') ); ?>">Mirador del Golf</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/sotogolf') ); ?>">Sotogolf</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/tenis') ); ?>">Tenis</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/valgrande') ); ?>">Valgrande</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/zone-c') ); ?>">Zone C</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/zone-d') ); ?>">Zone D</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/zone-e') ); ?>">Zone E</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/zone-f') ); ?>">Zone F</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto/zone-g') ); ?>">Zone G</a></li>
                        <li class="megamenu-all-link">
                            <a href="<?php echo esc_url( lsg_megamenu_url('sotogrande-alto') ); ?>">
                                <?php _e('All Sotogrande Alto', 'luxurysotogrande'); ?> &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: LA RESERVA -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('LA RESERVA', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva') ); ?>">La Reserva</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/the-seven') ); ?>">The Seven</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/the-fifteen') ); ?>">The Fifteen</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/the-collection') ); ?>">The Collection</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/village-verde') ); ?>">Village Verde</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/la-finca-de-kronos') ); ?>">La Finca de Kronos</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/las-villas-sotogrande') ); ?>">Las Villas - Sotogrande</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/los-cortijos-de-la-reserva') ); ?>">Los Cortijos de la Reserva</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('la-reserva/senda-chica') ); ?>">Senda Chica</a></li>
                    </ul>
                </div>

                <!-- Column 5: MARINA -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('MARINA', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/guadalmarina') ); ?>">Guadalmarina</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/isla-carey') ); ?>">Isla Carey</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/isla-de-la-vela') ); ?>">Isla de la Vela</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/isla-del-pez-barbero') ); ?>">Isla del Pez Barbero</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/isla-del-pez-volador') ); ?>">Isla del Pez Volador</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/isla-tortuga') ); ?>">Isla Tortuga</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/jungla-del-loro') ); ?>">Jungla del Loro</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/pier-1-2') ); ?>">Pier 1 & 2</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-de-alboaire') ); ?>">Ribera de Alboaire</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-de-la-golondrina') ); ?>">Ribera de la Golondrina</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-de-la-necora') ); ?>">Ribera de la Necora</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-de-la-romana') ); ?>">Ribera de la Romana</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-de-la-tenca') ); ?>">Ribera de la Tenca</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-arlequin') ); ?>">Ribera del Arlequin</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-candil') ); ?>">Ribera del Candil</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-corvo') ); ?>">Ribera del Corvo</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-delfin') ); ?>">Ribera del Delfin</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-emperador') ); ?>">Ribera del Emperador</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-gurami') ); ?>">Ribera del Gurami</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-marlin') ); ?>">Ribera del Marlin</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-obispo') ); ?>">Ribera del Obispo</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-paraiso') ); ?>">Ribera del Paraiso</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-pez-luna') ); ?>">Ribera del Pez Luna</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('marina/ribera-del-rio') ); ?>">Ribera del Rio</a></li>
                        <li class="megamenu-all-link">
                            <a href="<?php echo esc_url( lsg_megamenu_url('marina') ); ?>">
                                <?php _e('All Marina', 'luxurysotogrande'); ?> &rarr;
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 6: BEACH & BEYOND -->
                <div class="megamenu-col">
                    <div class="megamenu-col-header">
                        <span class="megamenu-heading"><?php _e('BEACH & BEYOND', 'luxurysotogrande'); ?></span>
                        <span class="megamenu-heading-line"></span>
                    </div>
                    <ul class="megamenu-list">
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/paseo-del-mar') ); ?>">Paseo del Mar</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/paseo-del-rio') ); ?>">Paseo del Rio</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/puerto-deportivo') ); ?>">Puerto Deportivo</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/san-roque-club') ); ?>">San Roque Club</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/la-alcaidesa') ); ?>">La Alcaidesa</a></li>
                        <li><a href="<?php echo esc_url( lsg_megamenu_url('beach-beyond/torreguadiaro') ); ?>">Torreguadiaro</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
<?php endif; ?>
