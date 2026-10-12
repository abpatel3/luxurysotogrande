<?php
/**
 * Renders the global latest news
 */
$id =  apply_filters( 'wpml_object_id', get_sub_field( 'global_section' ), 'global_section' );
$title = get_field( 'title', $id );
$subtitle = get_field( 'subtitle', $id );
$numberOfPosts = get_field( 'number_of_posts', $id );

$news = get_posts( [
    'post_type' => 'post',
    'posts_per_page' => $numberOfPosts
] );
?>

<section class="max-w-6xl px-5 py-20 mx-auto lg:px-0">

    <div class="space-y-5 text-center uppercase mb-14 ">
        <h2 class="text-3xl font-normal lg:text-4xl"><?php echo $title; ?></h2>
        <h3 class="text-lg font-thin lg:text-xl"><?php echo $subtitle; ?></h3>
    </div>
    
    <?php if ( count($news) > 0 ) : ?>
            <div <?= count($news) > 1 ? 'class="mt-20 glide news-carousel lg:mt-0"' : '' ;?>>
                <?php if ( count($news) > 1 ) : ?>
                    <div class="absolute right-0 flex items-center gap-2 -top-6 glide__arrows lg:hidden" data-glide-el="controls">
                        <button class="relative p-1 border-0 shadow-none -left-0 glide__arrow glide__arrow--left bg-lsg-blue hover:bg-lsg-blue/50" data-glide-dir="<">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
                            </svg>
                        </button>
                        <button class="relative p-1 border-0 shadow-none -right-0 glide__arrow glide__arrow--right bg-lsg-blue hover:bg-lsg-blue/50" data-glide-dir=">">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <?php $i = 0; foreach( $news as $news_item ): ?>
                            <li class="glide__slide">
                                <div data-aos="fade-up" data-aos-delay="<?php echo $i * 100 ?>">
                                    <?php if ( has_post_thumbnail( $news_item->ID ) ) : ?>
                                        <?php echo get_the_post_thumbnail( $news_item->ID ); ?>
                                    <?php endif; ?>

                                    <div class="p-4 space-y-2 bg-white">
                                        <h2 class="text-2xl font-semibold uppercase"><?php echo $news_item->post_title; ?></h2>
                                    </div>
                                </div>
                            </li>
                        <?php $i++; endforeach; ?>
                    </ul>
                </div>

                <?php if ( count($news) > 1 ) : ?>
                    <div class="hidden glide__arrows lg:block" data-glide-el="controls">
                        <button class="border-0 shadow-none lg:-left-24 glide__arrow glide__arrow--left" data-glide-dir="<">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-left text-lsg-gold" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M9.224 1.553a.5.5 0 0 1 .223.67L6.56 8l2.888 5.776a.5.5 0 1 1-.894.448l-3-6a.5.5 0 0 1 0-.448l3-6a.5.5 0 0 1 .67-.223z"/>
                            </svg>
                        </button>
                        <button class="border-0 !shadow-none lg:-right-24 glide__arrow glide__arrow--right" data-glide-dir=">">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chevron-compact-right text-lsg-gold" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6.776 1.553a.5.5 0 0 1 .671.223l3 6a.5.5 0 0 1 0 .448l-3 6a.5.5 0 1 1-.894-.448L9.44 8 6.553 2.224a.5.5 0 0 1 .223-.671z"/>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
    <?php endif; ?>
</section>