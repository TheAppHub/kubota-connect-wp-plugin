<?php
class Kubota_Connect_Admin {
    private $plugin_name;
    private $version;
    private $api_key;

    private $product;
    private $category;
    private $finance_offer;
    private $highlight;

    public function __construct() {
        $this->plugin_name = Kubota_Connect_Config::getConfig('plugin_name');
        $this->version = Kubota_Connect_Config::getConfig('version');
        
        $api_client = new API_Client();
        $this->api_key = API_Key_Manager::get_api_key();

        $this->product = new Product($api_client, 'product');
        $this->category = new Kubota_Connect_Category($api_client, 'product_category');
        $this->finance_offer = new Finance_Offer($api_client, 'finance_offer');
        $this->highlight = new Highlight($api_client, 'highlights');

        add_action('admin_menu', [$this, 'importer_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
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
            $updated = $this->category->import();
            if($updated) echo '<div class="updated"><p>Categories imported successfully!</p></div>';
        }
    
        if (isset($_POST['import_products'])) {
            $updated = $this->product->import();
            if($updated) echo '<div class="updated"><p>Products imported successfully!</p></div>';
        }
    
        if (isset($_POST['import_finance_offers'])) {
            $updated = $this->finance_offer->import();
            if($updated) echo '<div class="updated"><p>Finance offers imported successfully!</p></div>';
        }
    
        if (isset($_POST['import_highlights'])) {
            $updated = $this->highlight->import();
            if($updated === true) echo '<div class="updated"><p>Highlights imported successfully!</p></div>';
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

    
        // Import Schedule Settings
        add_settings_section(
            'import_schedule_section',
            'Import Schedule Settings',
            null,
            'import_settings_page'
        );
    
        $cpts = [
            'kubota-product' => 'Products',
            'kubota-finance_offer' => 'Finance Offers',
            'kubota-highlight' => 'Highlights',
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



    public function api_key_field_callback() {
        if (defined('KC_API_KEY_TOKEN')) {
            echo '<p style="color: green;">Great, the API Key is set in wp-config.php.</p>';
        } else {
            $api_key = API_Key_Manager::get_api_key();
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
}