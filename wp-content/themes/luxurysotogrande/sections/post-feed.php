<?php

/**
 * Displays the Post Feed section
 */
$post_types = get_sub_field('post_types');
$per_page = get_sub_field('per_page');
$show_thumbnails = get_sub_field('show_thumbnails');
$show_excerpts = get_sub_field('show_excerpts');
$full_link = get_sub_field('full_link');
$pagination = get_sub_field('pagination');

$cat = $_GET['cat'] ?? '';

$posts = new WP_Query([
    'post_type' => $post_types,
    'posts_per_page' => $per_page,
    'paged' => get_query_var('paged'),
    'cat' => $cat
]);

$categories = get_categories([
    'hide_empty' => true
]);

$card_classes = "bg-white flex flex-col justify-between"
?>

<div class="px-5 mx-auto my-10 max-w-screen-2xl lg:px-0 posts-container">

    <?php if ($pagination) : ?>
        <?php if ($posts->have_posts()) : ?>
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-3">
                <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                    <?php echo $full_link ? "<a href='" . get_the_permalink() . "' class='" . $card_classes . "'>" : "<div class='" . $card_classes . "'>"; ?>
                    <div class="posts-items">
                        <?php if ($show_thumbnails) : ?>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="relative flex items-center justify-center post-item-<?php echo get_the_ID(); ?> posts-items-item">
                                    <?php the_post_thumbnail('medium_large', ['class' => 'object-cover w-full h-60 lg:h-96']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="flex flex-col justify-between flex-1 p-4 space-y-2 bg-lsg-gray posts-items-item-flex">
                            <h2 class="text-xl font-medium uppercase truncate"><?php the_title(); ?></h2>
                            <?php if ($show_excerpts) : ?>
                                <p class="posts-items__excerpt"><?php the_field('excerpt'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!$full_link) : ?>
                        <a href="<?php the_permalink(); ?>" class="block w-full p-4 font-semibold text-center text-white bg-lsg-gold hover:bg-lsg-gold-hover">Read More</a>
                    <?php endif; ?>
                    <?php echo $full_link ? "</a>" : "</div>"; ?>
                <?php endwhile; ?>
            </div>
            <div class="flex items-center justify-center gap-2 mt-20 text-center">
                <?php echo paginate_links(['total' => $posts->max_num_pages]); ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div x-data="{
            page: 1,
            perPage: 10,
            totalPages: null,
            posts: [],
            getPosts() {
                fetch(`/wp-json/wp/v2/posts?per_page=${this.perPage}&page=${this.page}&_embed`)
                    .then(response => {
                        this.totalPages = response.headers.get('x-wp-totalpages')
                        return response.json()
                    })
                    .then(data => {
                        this.posts.push(...data)
                        console.log(this.posts)
                    })
                    .catch(error => console.log(error))
            },
            loadMore() {
                if (this.page < this.totalPages) {
                    this.page++
                    this.getPosts()
                }
            }
        }" x-init="getPosts()">
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="post in posts">
                    <?php echo $full_link ? "<a x-bind:href='post.link' class='bg-white'>" : "<div class='bg-white'>"; ?>
                    <?php if (get_sub_field('show_thumbnails')) : ?>
                        <header>
                            <img x-bind:src="post._embedded['wp:featuredmedia']?.['0'].media_details.sizes.medium_large.source_url" x-bind:alt="post._embedded['wp:featuredmedia']?.['0'].alt_text">
                        </header>
                    <?php endif; ?>
                    <h2 class="text-xl font-bold" x-text="post.title.rendered"></h2>
                    <?php echo $full_link ? "</a>" : "</div>"; ?>
                </template>
            </div>
            <button x-on:click="loadMore" x-bind:disabled="page == totalPages">
                Load More Posts
            </button>
        </div>
    <?php endif; ?>
</div>