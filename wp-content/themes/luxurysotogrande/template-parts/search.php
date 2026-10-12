<div 
    x-data="{ 
        search: '',
        results: null,
        loading: false,
        fetchResults() {
            this.loading = true
            this.results = null
            fetch(`http://acf-starter-theme.test/wp-json/wp/v2/search?search=${encodeURIComponent(this.search)}&per_page=10`)
            .then(response => response.json())
            .then(response => {
                if (this.search !== '') {
                    this.results = response
                } else {
                    this.results = null
                }

                this.loading = false
            })
        },
        showModal() {
            $refs.searchModal.showModal()
            $refs.searchInput.focus()
        },
        clear() {
            this.search = ''
            this.results = null
        }
    }"
>
    <button @click="showModal" class="p-9 | hover:bg-stone-800/50" aria-label="Search Button">
        <?php get_template_part( 'template-parts/icons/search' ); ?>
    </button>

    <dialog 
        x-ref="searchModal" 
        class="w-11/12 lg:w-2/5 | bg-stone-800 | px-5 lg:px-10 py-5"
        @close="clear"
    >
        <input 
            type="text" 
            x-model="search"
            x-ref="searchInput"
            @input.debounce.500ms="fetchResults"
            placeholder="Search..."
            class="w-full | bg-transparent | text-white text-lg lg:text-xl | p-5 | outline-none"
        >
        <div x-show="loading" x-transition.opacity>
            <?php get_template_part( 'template-parts/icons/spinner', null, ['margin' => 'mx-auto my-5'] ); ?>
        </div>

        <!-- Search Results -->
        <ul x-show="results" x-transition>
            <template x-for="result in results">
                <li>
                    <a 
                        x-bind:href="result.url" 
                        x-text="result.title"
                        class="block | text-white text-lg lg:text-xl | border-t border-stone-700/50 | hover:bg-stone-700/50 | p-5"
                    ></a>
                </li>
            </template>
        </ul>

        <form class="fixed right-2 top-2" method="dialog">
            <button class="bg-stone-800 hover:bg-stone-700 | p-5 | text-xl text-white" aria-label="Close Search Button">
                <?php get_template_part( 'template-parts/icons/close', null, ['width' => 'w-10', 'height' => 'h-10'] ); ?>
            </button>
        </form>
    </dialog>
</div>