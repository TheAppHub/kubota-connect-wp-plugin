<?php
get_header();
?>
<h1>Archive Product Template</h1>

<div class="container mx-auto my-8 px-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <!-- Archive Title -->
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Kubota Products</h2>

        <!-- Filter by Category -->
        <div class="mb-6">
            <form method="GET" action="">
                <label for="kubota-category" class="block text-sm font-medium text-gray-700">Filter by Category</label>
                <select id="kubota-category" name="kubota-category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="">All Categories</option>
                    <?php
                    $terms = get_terms([
                        'taxonomy' => 'kubota_category',
                        'hide_empty' => false,
                    ]);
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    ?>
                </select>
                <button type="submit" class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Apply Filter
                </button>
            </form>
        </div>

        <!-- Product List -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="bg-white shadow rounded-lg p-4">
                        <a href="<?php the_permalink(); ?>" class="text-lg font-bold text-blue-600 hover:text-blue-800">
                            <?php the_title(); ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="text-gray-500">No products found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            <?php the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => __('« Previous', 'textdomain'),
                'next_text' => __('Next »', 'textdomain'),
                'screen_reader_text' => __('', 'textdomain'),
                'class' => 'pagination justify-center',
            ]); ?>
        </div>
    </div>
</div>

<?php
get_footer();
?>
