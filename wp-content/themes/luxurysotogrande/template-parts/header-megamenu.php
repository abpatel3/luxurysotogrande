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

<?php
// Helper for resolving fallback URLs if not exists
if (!function_exists('lsg_megamenu_url')) {
    function lsg_megamenu_url($path) {
        $clean_path = trim($path, '/');
        $page = get_page_by_path($clean_path);
        if (!$page) {
            $parts = explode('/', $clean_path);
            $slug = end($parts);
            $page = get_page_by_path($slug);
        }
        if ($page) {
            return get_permalink($page->ID);
        }
        return home_url('/' . $clean_path . '/');
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
            
            <div class="megamenu-inner-container ls-container">
                <div class="megamenu-grid megamenu-grid-dynamic" style="--megamenu-cols: 6; grid-template-columns: repeat(6, minmax(0, 1fr)) !important;">
                    
                    <?php foreach ($cols as $col_id => $col) : 
                        $col_item  = $col['item'];
                        $col_title = trim($col_item->title);
                        $col_url   = (!empty($col_item->url) && $col_item->url !== '#' && $col_item->url !== '') ? $col_item->url : '';
                        
                        // Query dynamic child posts/pages from helper (or explicit sub_items from WP Menu)
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
                                            $sub_classes = is_array($sub->classes ?? null) ? $sub->classes : [];
                                            $is_all_link = in_array('all-link', $sub_classes) || 
                                                           in_array('view-all', $sub_classes) || 
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
<?php endif; ?>

