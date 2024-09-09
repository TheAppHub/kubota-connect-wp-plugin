<?php
class Kubota_Connect_Admin {
    private $plugin_name = 'kubota-connect';
    private $version = '1.0.0';
    private $api_key;

    private $product;
    private $category;
    private $finance_offer;
    private $highlight;

    public function __construct() {
        $api_client = new API_Client('https://api.kubota.io/dealers/v1');
        $this->api_key = $api_client->get_api_key();

        $this->product = new Product($api_client, 'product');
        $this->category = new Kubota_Connect_Category($api_client, 'product_category');
        $this->finance_offer = new Finance_Offer($api_client, 'finance_offer');
        $this->highlight = new Highlight($api_client, 'highlights');


        add_action('admin_menu', [$this, 'importer_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    
        if ($this->api_key) {
            add_action('init', [$this, 'schedule_cron_jobs']);
            add_action('kubota-connect_import_products_event', [$this->product, 'import']);
            add_action('kubota-connect_import_categories_event', [$this->category, 'import']);
            add_action('kubota-connect_import_finance_offers_event', [$this->finance_offer, 'import']);
            add_action('kubota-connect_import_highlights_event', [$this->highlight, 'import']);
        }
    }

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
    
        include plugin_dir_path(__FILE__) . 'templates/kubota-connect-admin-template.php';
    }
    
    public function register_settings() {
        // API Key Settings
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
    
        // Product Template Settings
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
    
        // Import Schedule Settings
        add_settings_section(
            'import_schedule_section',
            'Import Schedule Settings',
            null,
            'import_settings_page'
        );
    
        $cpts = [
            'product' => 'Products',
            'finance_offer' => 'Finance Offers',
            'highlight' => 'Highlights',
            'category' => 'Categories'
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

    public function enqueue_admin_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../admin/css/kubota-connect-admin.css', array(), $this->version, 'all');
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../admin/js/kubota-connect-admin.js', array(), $this->version, 'all');
    }

    public function schedule_cron_jobs() {
        $this->schedule_single_cron_job('category', 'kubota-connect_import_categories_event');
        $this->schedule_single_cron_job('product', 'kubota-connect_import_products_event');
        $this->schedule_single_cron_job('finance_offer', 'kubota-connect_import_finance_offers_event');
        $this->schedule_single_cron_job('highlight', 'kubota-connect_import_highlights_event');
    }

    private function schedule_single_cron_job($cpt, $hook) {
        $schedule = get_option($cpt . '_schedule', 'daily');

        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }

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