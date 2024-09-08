<?php
/**
 * Single Product Template
 */

 get_header();

?>

<div class="container mx-auto p-6">
    <!-- Hero Image -->
    <div class="mb-8">
        <?php echo do_shortcode('[kubota-connect-hero-image theme="material"]'); ?>
    </div>

    <div class="mb-24">
        <h1 class="flex items-center justify-center text-4xl font-bold mb-2 text-gray-800 ">
            <?php the_title(); ?>
        </h1>

        <!-- Model Names -->
        <div class="flex items-center justify-center text-ld font-medium mb-8 text-gray-700">
            <?php echo do_shortcode('[kubota-connect-product-model-names theme="material"]'); ?>
        </div>

        <div class="flex items-center justify-center mb-4">
            <?php echo do_shortcode('[kubota-connect-product-brochure theme="material"]'); ?>
        </div>
    </div>


    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-24">
        <!-- Product Description -->
        <div class="flex items-center justify-center md:justify-start">
            <div>
                <h2 class="text-xl font-semibold mb-4 text-gray-800"><?php the_title(); ?></h2>
                <div class="text-md text-gray-600"><?php echo do_shortcode('[kubota-connect-product-description theme="material"]'); ?></div>
            </div>
        </div>

        <!-- Product Image -->
        <div class="flex items-center justify-center">
                <?php echo do_shortcode('[kubota-connect-image theme="material"]'); ?>
        </div>
    </div>

    <!-- Product Features -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Product Features</h2>
        <?php echo do_shortcode('[kubota-connect-product-features theme="material"]'); ?>
    </div>

    <!-- Product Documents -->
    <div class="mb-24 flex flex-col md:flex-row md:justify-between">
        <div class="flex-shrink-0">
            <?php echo do_shortcode('[kubota-connect-product-brochure theme="material"]'); ?>
        </div>
        <div class="flex-shrink-0 mt-4 md:mt-0">
            <?php echo do_shortcode('[kubota-connect-product-documents theme="material"]'); ?>
        </div>
    </div>

    <!-- Specifications Table -->
    <div class="mb-6">
        <h2 class="text-3xl font-semibold mb-4 text-gray-800">Specifications</h2>
        <?php echo do_shortcode('[kubota-connect-product-specs-table theme="material"]'); ?>
    </div>
</div>

<?php
get_footer();
?>