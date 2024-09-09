<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="swiper kubota-highlight-slider w-full max-w-screen-2xl border rounded-md">
    <div class="swiper-wrapper">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <div class="swiper-slide">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <?php echo do_shortcode('[kubota-connect-image]') ?>
                    </div>
                    <div class="flex items-center justify-center p-4 bg-white h-full">
                        <div class="p-0 xl:px-12 2xl:px-16">
                            <h2 class="text-2xl xl:text-4xl font-bold mb-2 xl:mb-5"><?php the_title(); ?></h2>
                            <p class="mb-4 xl:mb-8 text-sm md:text-base xl:text-lg"><?php echo $this->shortcode_description([]); ?></p>
                            <a href="<?php echo esc_url($this->shortcode_link([])); ?>" class="block md:inline-block bg-[#E4551C] rounded-md py-2 xl:py2.5 px-4 xl:px-5 mb-4 border border-transparent text-center text-sm xl:text-base text-white transition-all shadow-md" target="_blank" rel="noopener noreferrer" style="color:white">
                                <?php echo esc_html($this->shortcode_button_text([])); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>