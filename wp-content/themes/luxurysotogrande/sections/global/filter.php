<?php 
/**
 * Filter template part
 */
$property_areas = get_terms( [
    'taxonomy' => 'property_area',
    'hide_empty' => false,
    'hierarchical' => true,
    'exclude' => [363, 424, 2, 426]
] );

$property_status = get_terms( [
    'taxonomy' => 'property_status',
    'hide_empty' => false,
    'exclude' => [137, 246]
] );

$property_types = get_terms( [
    'taxonomy' => 'property_type',
    'hide_empty' => false
] );

// Initial statusId for filters
$status_id = match( true ) {
    // is_page( 224 ) => 'long-term-rental',
    // is_page( 224 ) => 'long-term-rental-es',
    is_page( 219 ) => 135,
    is_page( 1068 ) => 244,
    is_page( 221 ) => 136,
    is_page( 1073 ) => 245,
    default => ''
};

$filters = filter_params();
?>

<section class="px-5 uppercase bg-lsg-gray lg:px-0 search-bar-widget">
    <h2 class="text-center text-3xl font-normal lg:text-4xl mb-5"><?= label( 'property_search_label' ); ?></h2>
    <form 
        action="" 
        class="grid max-w-6xl mx-auto lg:grid-cols-4 gap-x-8 gap-y-4"
        x-data="{ 
            parentId: '<?= $filters['area_id'] ?? ''; ?>',
            subArea: '<?= $filters['subarea_id'] ?? ''; ?>',
            statusId: '<?= $status_id; ?>'
        }"
        autocomplete="off"
    >
        <div>
            <label for="pid" class="hidden"><?= label( 'property_id_label' ); ?></label>
            <input 
                type="text" 
                name="pid"
                value="<?= $filters['pid']; ?>" 
                id="pid"
                class="w-full p-3 uppercase bg-white placeholder:text-black focus:placeholder:text-gray-400" 
                placeholder="<?= label( 'property_id_label' ); ?>"
            >
        </div>

        <div>
            <label for="status" class="hidden"><?= label( 'property_status_label' ); ?></label>
            <select
                name="status"
                id="status"
                class="w-full p-3 uppercase bg-white"
                x-on:change="statusId = $event.target.value"
            >
                <option value=""><?= label( 'property_status_label' ); ?></option>
		        <?php foreach ( $property_status as $property_status ) : ?>
			        <?php if ( $property_status->term_id === 245 || $property_status->slug === 'sold' || $property_status->slug === 'rented' ) continue; ?>
                    <option
                        value="<?= $property_status->term_id; ?>"
				        <?= $property_status->slug === $filters['status'] ? 'selected' : ''; ?>
                    >
				        <?= $property_status->name; ?>
                    </option>
		        <?php endforeach; ?>
            </select>

        </div>

        <div>
            <label for="type" class="hidden uppercase"><?= label( 'property_type_label' ); ?></label>
            <select name="type" id="type" class="w-full p-3 uppercase bg-white">
                <option value=""><?= label( 'property_type_label' ); ?></option>
                <option value="all">All Types</option>
                <?php foreach ( $property_types as $property_type ) : ?>
                    <option 
                        value="<?= $property_type->term_id; ?>" 
                        <?= $property_type->slug === $filters['type'] ? 'selected' : ''; ?>
                    >
                        <?= $property_type->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="area" class="hidden 123"><?= label( 'property_area_label' ); ?></label>
            <select 
                name="area" 
                id="area" 
                class="w-full p-3 uppercase bg-white"
                x-model="parentId"
                x-on:change="subArea = ''">
                <option value=""><?= label( 'property_area_label' ); ?></option>
                <option value="<?php _e('2', 'purple'); ?>">
		            <?php _e('All Sotogrande', 'purple'); ?>
                </option>
                <?php foreach ( $property_areas as $property_area ) : ?>
                    <?php if ( $property_area->parent === 0 ) : ?>
                        <option 
                            value="<?= $property_area->term_id; ?>"
                            <?= $filters['area_id'] == $property_area->term_id ? 'selected' : ''; ?>>
                            <?= $property_area->name; ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
                <option value="<?php _e('363', 'purple'); ?>">
		            <?php _e('San Roque Club', 'purple'); ?>
                </option>
                <option value="<?php _e('424', 'purple'); ?>">
		            <?php _e('Torreguadiaro', 'purple'); ?>
                </option>
                <option value="<?php _e('426', 'purple'); ?>">
		            <?php _e('La Alcaidesa', 'purple'); ?>
                </option>
            </select>
        </div>

        <div>
            <label for="subarea" class="hidden"><?= label( 'sub_areas_label' ); ?></label>
            <select 
                name="subarea" 
                id="subarea" 
                class="w-full p-3 uppercase bg-white"
                x-model="subArea"
            >
                <option value=""><?= label( 'all_sub_areas_label' ); ?></option>
                <?php foreach ( $property_areas as $property_area ) : ?>
                    <?php if ( $property_area->parent != 0 ) : ?>
                        <template x-if="<?= $property_area->parent; ?> == parentId || parentId == 2">
                            <option 
                                value="<?= $property_area->term_id; ?>"
                                <?= $filters['subarea_id'] == $property_area->term_id ? 'selected' : ''; ?>
                            >
                                <?= $property_area->name; ?>
                            </option>
                        </template>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="min_price" class="hidden"><?= label( 'min_price_label' ); ?></label>
            <select 
                name="min_price" 
                id="min_price" 
                class="w-full p-3 uppercase bg-white disabled:text-gray-400 disabled:cursor-not-allowed"
                x-bind:disabled="statusId == 136 || statusId == 245"
            >
                <option value=""><?= label( 'min_price_label' ); ?></option>
                <option <?= $filters['min_price'] == '1000' ? 'selected' : ''; ?> value="1000">&euro;1000</option>
                <option <?= $filters['min_price'] == '5000' ? 'selected' : ''; ?> value="5000">&euro;5000</option>
                <option <?= $filters['min_price'] == '10000' ? 'selected' : ''; ?> value="10000">&euro;10,000</option>
                <option <?= $filters['min_price'] == '50000' ? 'selected' : ''; ?> value="50000">&euro;50,000</option>
                <option <?= $filters['min_price'] == '100000' ? 'selected' : ''; ?> value="100000">&euro;100,000</option>
                <option <?= $filters['min_price'] == '200000' ? 'selected' : ''; ?> value="200000">&euro;200,000</option>
                <option <?= $filters['min_price'] == '300000' ? 'selected' : ''; ?> value="300000">&euro;300,000</option>
                <option <?= $filters['min_price'] == '400000' ? 'selected' : ''; ?> value="400000">&euro;400,000</option>
                <option <?= $filters['min_price'] == '500000' ? 'selected' : ''; ?> value="500000">&euro;500,000</option>
                <option <?= $filters['min_price'] == '600000' ? 'selected' : ''; ?> value="600000">&euro;600,000</option>
                <option <?= $filters['min_price'] == '700000' ? 'selected' : ''; ?> value="700000">&euro;700,000</option>
                <option <?= $filters['min_price'] == '800000' ? 'selected' : ''; ?> value="800000">&euro;800,000</option>
                <option <?= $filters['min_price'] == '900000' ? 'selected' : ''; ?> value="900000">&euro;900,000</option>
                <option <?= $filters['min_price'] == '1000000' ? 'selected' : ''; ?> value="1000000">&euro;1,000,000</option>
                <option <?= $filters['min_price'] == '1500000' ? 'selected' : ''; ?> value="1500000">&euro;1,500,000</option>
                <option <?= $filters['min_price'] == '2000000' ? 'selected' : ''; ?> value="2000000">&euro;2,000,000</option>
                <option <?= $filters['min_price'] == '2500000' ? 'selected' : ''; ?> value="2500000">&euro;2,500,000</option>
                <option <?= $filters['min_price'] == '5000000' ? 'selected' : ''; ?> value="5000000">&euro;5,000,000</option>
            </select>
        </div>

        <div>
            <label for="max_price" class="hidden"><?= label( 'max_price_label' ); ?></label>
            <select 
                name="max_price" 
                id="max_price" 
                class="w-full p-3 uppercase bg-white disabled:text-gray-400 disabled:cursor-not-allowed"
                x-bind:disabled="statusId == 136 || statusId == 245"
            >
                <option value=""><?= label( 'max_price_label' ); ?></option>
                <option <?= $filters['max_price'] == '5000' ? 'selected' : ''; ?> value="5000">&euro;5000</option>
                <option <?= $filters['max_price'] == '10000' ? 'selected' : ''; ?> value="10000">&euro;10,000</option>
                <option <?= $filters['max_price'] == '50000' ? 'selected' : ''; ?> value="50000">&euro;50,000</option>
                <option <?= $filters['max_price'] == '100000' ? 'selected' : ''; ?> value="100000">&euro;100,000</option>
                <option <?= $filters['max_price'] == '200000' ? 'selected' : ''; ?> value="200000">&euro;200,000</option>
                <option <?= $filters['max_price'] == '300000' ? 'selected' : ''; ?> value="300000">&euro;300,000</option>
                <option <?= $filters['max_price'] == '400000' ? 'selected' : ''; ?> value="400000">&euro;400,000</option>
                <option <?= $filters['max_price'] == '500000' ? 'selected' : ''; ?> value="500000">&euro;500,000</option>
                <option <?= $filters['max_price'] == '600000' ? 'selected' : ''; ?> value="600000">&euro;600,000</option>
                <option <?= $filters['max_price'] == '700000' ? 'selected' : ''; ?> value="700000">&euro;700,000</option>
                <option <?= $filters['max_price'] == '800000' ? 'selected' : ''; ?> value="800000">&euro;800,000</option>
                <option <?= $filters['max_price'] == '900000' ? 'selected' : ''; ?> value="900000">&euro;900,000</option>
                <option <?= $filters['max_price'] == '1000000' ? 'selected' : ''; ?> value="1000000">&euro;1,000,000</option>
                <option <?= $filters['max_price'] == '1500000' ? 'selected' : ''; ?> value="1500000">&euro;1,500,000</option>
                <option <?= $filters['max_price'] == '2000000' ? 'selected' : ''; ?> value="2000000">&euro;2,000,000</option>
                <option <?= $filters['max_price'] == '2500000' ? 'selected' : ''; ?> value="2500000">&euro;2,500,000</option>
                <option <?= $filters['max_price'] == '5000000' ? 'selected' : ''; ?> value="5000000">&euro;5,000,000</option>
                <option <?= $filters['max_price'] == '10000000' ? 'selected' : ''; ?> value="10000000">&euro;12,000,000</option>
            </select>
        </div>

        <div>
            <input type="hidden" name="search" value="search">
            <button 
                type="submit"
                class="w-full p-2 text-lg text-white uppercase bg-lsg-gold"
            >
                <?= label( 'search_label' ); ?>
            </button>
        </div>
        
    </form>
</section>