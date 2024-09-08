<?php
/**
 * Class Kubota_Connect
 *
 * This class is responsible for handling the Kubota Connect functionality.
 * It contains methods for connecting to the Kubota API and retrieving data.
 */
class Kubota_Connect {
    private $plugin_name = 'kubota-connect';
    private $version = '1.0.0';

    private $product;
    private $category;
    private $finance_offer;
    private $highlight;
    private $api_key;
    private $password_manager;

    public function __construct() {
        $this->password_manager = new Kubota_Connect_Password_Manager();
        $api_client = new API_Client('https://api.kubota.io/dealers/v1', $this->password_manager);
        $this->api_key = $api_client->get_api_key();

        $this->product = new Product($api_client, 'product');
        $this->category = new Kubota_Connect_Category($api_client, 'product_category');
        $this->finance_offer = new Finance_Offer($api_client, 'finance_offer');
        $this->highlight = new Highlight($api_client, 'highlights');

        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_scriptes']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        add_action('admin_menu', [$this, 'importer_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        if ($this->api_key) {
            add_action('init', [$this, 'schedule_cron_jobs']);
            add_action('kubota-connect_import_products_event', [$this->product, 'import']);
            add_action('kubota-connect_import_categories_event', [$this->category, 'import']);
            add_action('kubota-connect_import_finance_offers_event', [$this->finance_offer, 'import']);
            add_action('kubota-connect_import_highlights_event', [$this->highlight, 'import']);
        }      
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
	 */
	public function enqueue_admin_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../admin/css/kubota-connect-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the stylesheets for the public area.
     *
     * @since    1.0.0
	 */
	public function enqueue_public_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../public/css/kubota-connect-public.css', array(), $this->version, 'all' );
    }

    /**
     * Enqueues the necessary admin scripts.
     *
     * This method is responsible for enqueueing the required scripts for the admin area.
     * It is called within the class `Kubota_Connect` located in the file
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../admin/js/kubota-connect-admin.js', array(), $this->version, 'all' );
    }

    /**
     * Enqueues the public scripts.
     *
     * This method is responsible for enqueueing the necessary scripts for the public-facing pages of the Kubota Connect plugin.
     * The scripts are enqueued using the WordPress `wp_enqueue_script` function.
     *
     * @since 1.0.0
     */
    public function enqueue_public_scriptes() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../public/js/kubota-connect-public.js', array(), $this->version, 'all' );
    }

    /**
     * Creates the importer menu.
     */
    public function importer_menu() {
        add_menu_page(
            'Kubota Connect',
            'Kubota Connect',
            'manage_options',
            'kubota-connect',
            [$this, 'importer_page'],
            'dashicons-kubota-connect',
            80
        );
    }

    /**
     * Displays the importer page.
     *
     * This method is responsible for rendering the importer page in the WordPress admin area.
     * It is called when the corresponding menu item is clicked.
     */
    public function importer_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['import_categories'])) {
            $this->category->import();
            echo '<div class="updated"><p>Categories imported successfully!</p></div>';
        }

        if (isset($_POST['import_products'])) {
            $updated = $this->product->import();
            if($updated) echo '<div class="updated"><p>Products imported successfully!</p></div>';
        }

        if (isset($_POST['import_finance_offers'])) {
            $this->finance_offer->import();
            echo '<div class="updated"><p>Finance offers imported successfully!</p></div>';
        }

        if (isset($_POST['import_highlights'])) {
            $this->highlight->import();
            echo '<div class="updated"><p>Highlights imported successfully!</p></div>';
        }

        $api_key_error = !$this->api_key ? 'Please enter an API key to enable imports.' : '';


        ?>
        <div class="wrap">

        <h2></h2>
        
            <div class="flex flex-row mt-4 mb-4">
                <h2 class="text-2xl">Kubota Connect</h2>
                <div class="flex-auto"></div>
                <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'admin/img/kubota-connect-logo.png' ?>" class="mr-5 h-6" alt="Kubota Connect Logo" />
            </div>

            <div class="bg-white shadow rounded-lg p-4">
                <div class="mb-4 border-b border-gray-200">
                    <nav class="flex">
                        <button id="settings-tab" class="text-gray-600 py-2 px-4 block focus:outline-none hover:bg-gray-100 active:bg-gray-200">Settings</button>
                        <button id="documentation-tab" class="text-gray-600 py-2 px-4 block focus:outline-none hover:bg-gray-100 active:bg-gray-200">Documentation</button>
                    </nav>
                </div>

                <div id="settings-content" class="tab-content">
                    <?php if ($api_key_error): ?>
                        <div class="notice notice-error"><p><?php echo esc_html($api_key_error); ?></p></div>
                    <?php endif; ?>

                    <form method="post" action="" class="mb-8">
                        <input type="submit" name="import_products" class="button-primary mb-2" value="Import Products Now" <?php disabled(!$this->api_key); ?>>
                        <input type="submit" name="import_finance_offers" class="button-primary mb-2" value="Import Finance Offers Now" <?php disabled(!$this->api_key); ?>>
                        <input type="submit" name="import_highlights" class="button-primary mb-2" value="Import Highlights Now" <?php disabled(!$this->api_key); ?>>
                        <input type="submit" name="import_categories" class="button-primary mb-2" value="Import Categories Now" <?php disabled(!$this->api_key); ?>>
                    </form>
                
                    <form method="post" action="options.php" class="mb-8">
                        <?php
                        settings_fields('api_key_settings_group');
                        do_settings_sections('api_key_settings_page');
                        submit_button();
                        ?>
                    </form>

                    <div class="mt-4 p-4 bg-blue-100 border border-blue-200 rounded">
                        <p class="text-blue-700">
                            <strong>Note:</strong> The best way to store the API Key is to define it in the <code>wp-config.php</code> file for better security.
                        </p>
                    </div>
                    
                    <form method="post" action="options.php" class="mb-8">
                        <?php
                        settings_fields('import_settings_group');
                        do_settings_sections('import_settings_page');
                        submit_button();
                        ?>
                    </form>

                    <!-- <form method="post" action="options.php" class="mb-8">
                        <?php
                        settings_fields('product_template_settings_group');
                        do_settings_sections('product_template_settings_page');
                        submit_button();
                        ?>
                    </form> -->
                </div>

                <div id="documentation-content" class="tab-content hidden">
                    <h2 class="text-xl font-semibold mb-2">Shortcodes</h2>
                    <p>The Kubota Connect plugin provides various shortcodes to display product and highlight information on your site.</p>
                    <p class="mb-4">Each shortcode will generate specific <code>kubota-connect</code> CSS classes that you can target for custom styling. Additionally, you can pass the attribute <code>theme="material"</code> to any shortcode to output a pre-styled result using Material Design principles, offering a polished and modern appearance. If needed, you can also pass the attribute <code>post_id</code> to specify the post from which to pull the data.</p>

                    <h3 class="text-lg font-semibold mb-2">Product Shortcodes</h3>
                    <ul class="list-disc list-inside mb-4">
                        <li><code>[kubota-connect-product-description]</code> - Displays the product description.</li>
                        <li><code>[kubota-connect-product-features]</code> - Displays a list of product features.</li>
                        <li><code>[kubota-connect-product-brochure]</code> - Displays a link to the product brochure.</li>
                        <li><code>[kubota-connect-product-documents]</code> - Displays links to additional product documents.</li>
                        <li><code>[kubota-connect-product-model-names]</code> - Displays the model names in an HTML unordered list.</li>
                        <li><code>[kubota-connect-product-specs-table]</code> - Displays the models and specifications in a table.</li>
                        <li><code>[kubota-connect-image]</code> - Displays the image of the product.</li>
                        <li><code>[kubota-connect-hero-image]</code> - Displays the hero image of the product.</li>
                    </ul>

                    <h3 class="text-lg font-semibold mb-2">Category Shortcodes</h3>
                    <ul class="list-disc list-inside mb-4">
                        <li><code>[kubota-connect-category-description]</code> - Displays the Kubota category description.</li>
                    </ul>

                    <h3 class="text-lg font-semibold mt-4 mb-2">Highlight Shortcodes</h3>
                    <ul class="list-disc list-inside">
                        <li><code>[kubota-connect-highlight-title]</code> - Displays the highlight title.</li>
                        <li><code>[kubota-connect-highlight-description]</code> - Displays the highlight description.</li>
                        <li><code>[kubota-connect-image]</code> - Displays the highlight image.</li>
                    </ul>

                    <h3 class="text-lg font-semibold mt-4 mb-2">Finance Offers Shortcodes</h3>
                    <ul class="list-disc list-inside">
                        <li><code>[kubota-connect-finance-offer-type]</code> - Displays the offer type.</li>
                        <li><code>[kubota-connect-finance-rate-type]</code> - Displays the type of finance.</li>
                        <li><code>[kubota-connect-finance-rate]</code> - Displays the rate in %.</li>
                        <li><code>[kubota-connect-finance-term-in-months]</code> - Displays the finance term in months.</li>
                        <li><code>[kubota-connect-finance-deposit]</code> - Displays the finance minimum deposit.</li>
                        <li><code>[kubota-connect-finance-terms]</code> - Displays the finance terms and conditions.</li>
                        <li><code>[kubota-connect-image]</code> - Displays the finance image.</li>
                        <li><code>[kubota-connect-hero-image]</code> - Displays the finance hero image.</li>
                        <li><code>[kubota-connect-finance-offer]</code> - Displays the finance offer.</li>
                    </ul>

                    <h3 class="text-md font-semibold mt-6 mb-2">Usage Example</h3>
                    <p>To display a table of product models and specifications from a specific post with Material Design styling, you can use the following shortcode:</p>
                    <pre><code>[kubota-connect-product-specs-table post_id="123" theme="material"]</code></pre>
                    <p>In this example, the shortcode pulls data from the post with ID 123 and applies Material Design styling to the table output.</p>


                    <h2 class="text-xl font-semibold mt-8 mb-2">Custom Content</h2>
                    <p>Kubota Products and Kubota Finance Offers retain the default WordPress editor, allowing the administrator to add custom copy to each post.</p>
                    <p>This editor field is the only one that remains editable by admins. All other fields will be automatically overwritten during each content update to ensure the latest information from the Kubota Connect API is reflected.</p>
                </div>
            </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const settingsTab = document.getElementById('settings-tab');
            const documentationTab = document.getElementById('documentation-tab');
            const settingsContent = document.getElementById('settings-content');
            const documentationContent = document.getElementById('documentation-content');

            settingsTab.addEventListener('click', function () {
                settingsTab.classList.add('active');
                documentationTab.classList.remove('active');
                settingsContent.classList.remove('hidden');
                documentationContent.classList.add('hidden');
            });

            documentationTab.addEventListener('click', function () {
                documentationTab.classList.add('active');
                settingsTab.classList.remove('active');
                documentationContent.classList.remove('hidden');
                settingsContent.classList.add('hidden');
            });
        });
    </script>
        
        <?php
    }

    /**
     * Registers the settings for the Kubota Connect plugin.
     */
    public function register_settings() {
        // API Key Section
        add_settings_section(
            'api_key_section',
            'API Key Settings',
            null,
            'api_key_settings_page'
        );

        add_settings_field(
            'api_key',
            'API Key',
            [$this, 'api_key_field_callback'],
            'api_key_settings_page',
            'api_key_section'
        );

        register_setting('api_key_settings_group', 'kc_api_key');

        // Product Template Section
        add_settings_section(
            'product_template_section',
            'Product Template Settings',
            null,
            'product_template_settings_page'
        );

        add_settings_field(
            'use_custom_template',
            'Use Custom Product Template',
            [$this, 'template_field_callback'],
            'product_template_settings_page',
            'product_template_section'
        );

        register_setting('product_template_settings_group', 'use_custom_template');

        // Import Schedule Section
        add_settings_section(
            'import_schedule_section',
            'Import Schedule Settings',
            null,
            'import_settings_page'
        );

        $cpts = [
            'product' => 'Products',
            'finance_offer' => 'Finance Offers',
            'highlight' => 'Highlights'
        ];

        foreach ($cpts as $cpt => $label) {
            add_settings_field(
                $cpt . '_schedule',
                $label . ' Import Schedule',
                [$this, 'schedule_field_callback'],
                'import_settings_page',
                'import_schedule_section',
                ['cpt' => $cpt]
            );

            register_setting('import_settings_group', $cpt . '_schedule');
        }
    }

    public function template_field_callback() {
        $use_custom_template = get_option('use_custom_template', 'no');
        ?>
        <input type="checkbox" name="use_custom_template" value="yes" <?php checked($use_custom_template, 'yes'); ?>>
        <label for="use_custom_template">Check this box to use the custom template file (single-product-template.php). Uncheck to use the default template.</label>
        <?php
    }

    public function schedule_field_callback($args) {
        $cpt = $args['cpt'];
        $schedule = get_option($cpt . '_schedule', 'daily');
        ?>
        <select name="<?php echo esc_attr($cpt . '_schedule'); ?>">
            <option value="daily" <?php selected($schedule, 'daily'); ?>>Daily</option>
            <option value="weekly" <?php selected($schedule, 'weekly'); ?>>Weekly</option>
            <option value="fortnightly" <?php selected($schedule, 'fortnightly'); ?>>Fortnightly</option>
            <option value="monthly" <?php selected($schedule, 'monthly'); ?>>Monthly</option>
        </select>
        <?php
    }

    public function api_key_field_callback() {
        if (defined('KC_API_KEY_TOKEN')) {
            echo '<p style="color: green;">API Key is set in wp-config.php.</p>';
        } else {
            $api_key = get_option('kc_api_key');
            echo '<input type="password" name="kc_api_key" value="' . esc_attr($api_key) . '" placeholder="Enter your API Key">';
            if ($api_key) {
                echo '<p style="color: green;">API Key is set.</p>';
            }
        }
    }

    public function schedule_cron_jobs() {
        $this->schedule_single_cron_job('category', 'kubota-connect_import_categories_event');
        $this->schedule_single_cron_job('product', 'kubota-connect_import_products_event');
        $this->schedule_single_cron_job('finance_offer', 'kubota-connect_import_finance_offers_event');
        $this->schedule_single_cron_job('highlight', 'kubota-connect_import_highlights_event');
    }

    private function schedule_single_cron_job($cpt, $hook) {
        $schedule = get_option($cpt . '_schedule', 'daily');

        // Unschedule any existing cron job
        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }

        // Determine the next 4 AM on the first of the month
        $next_run_time = $this->get_next_run_time($schedule);
        wp_schedule_event($next_run_time, $schedule, $hook);
    }

    private function get_next_run_time($schedule) {
        $current_time = current_time('timestamp');

        if ($schedule === 'monthly') {
            $next_run_time = strtotime('first day of next month 04:00:00');
        } elseif ($schedule === 'fortnightly') {
            $next_run_time = strtotime('first day of next month 04:00:00');
            if ($current_time > $next_run_time) {
                $next_run_time = strtotime('+14 days', $next_run_time);
            }
        } else {
            $next_run_time = strtotime('04:00:00 tomorrow');
        }

        return $next_run_time;
    }
}