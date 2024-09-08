<?php

class Kubota_Connect_Settings_Page {
    private $options;

    public function __construct() {
        $this->options = get_option('kubota_connect_options');
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_menu_page(
            'Kubota Connect Settings', 
            'Kubota Connect', 
            'manage_options', 
            'kubota-connect-settings', 
            [$this, 'render_settings_page'], 
            'dashicons-admin-generic'
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1 class="text-3xl font-bold mb-4">Kubota Connect Settings</h1>
            <div class="tabs">
                <ul class="tab-list flex border-b">
                    <li class="mr-4">
                        <a href="#settings-tab" class="tab-link inline-block p-2 text-blue-500 border-b-2 border-transparent hover:border-blue-500">Settings</a>
                    </li>
                    <li>
                        <a href="#documentation-tab" class="tab-link inline-block p-2 text-blue-500 border-b-2 border-transparent hover:border-blue-500">Documentation</a>
                    </li>
                </ul>
                <div id="settings-tab" class="tab-content">
                    <form method="post" action="options.php" class="p-4 bg-white shadow-md">
                        <?php
                        settings_fields('kubota_connect_settings_group');
                        do_settings_sections('kubota_connect_settings_page');
                        submit_button();
                        ?>
                    </form>
                </div>
                <div id="documentation-tab" class="tab-content hidden">
                    <div class="p-4 bg-white shadow-md">
                        <h2 class="text-2xl font-bold mb-4">Documentation</h2>
                        <p>The Kubota Connect plugin provides various shortcodes to display product and highlight information on your site. Each shortcode will generate specific <code>kubota-connect</code> CSS classes that you can target for custom styling.</p>
                        <h3 class="text-xl font-bold mt-4">Product Shortcodes</h3>
                        <p>Use the following shortcode to display product details:</p>
                        <pre><code>[kubota-connect-product id="123"]</code></pre>
                        <p>This shortcode will output a table of model specifications, grouped by section, with classes like <code>kubota-connect-product</code> for styling.</p>
                        <h3 class="text-xl font-bold mt-4">Highlight Shortcodes</h3>
                        <p>Use the following shortcode to display a highlight:</p>
                        <pre><code>[kubota-connect-highlight id="456"]</code></pre>
                        <p>This shortcode will create a div with the class <code>kubota-connect-highlight</code> for custom styling.</p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tabLinks = document.querySelectorAll('.tab-link');
                const tabContents = document.querySelectorAll('.tab-content');

                tabLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();

                        tabLinks.forEach(link => link.classList.remove('border-blue-500'));
                        tabContents.forEach(content => content.classList.add('hidden'));

                        link.classList.add('border-blue-500');
                        document.querySelector(link.getAttribute('href')).classList.remove('hidden');
                    });
                });

                // Activate the first tab by default
                tabLinks[0].click();
            });
        </script>
        <?php
    }

    public function register_settings() {
        // Register settings for API key and import schedule
        register_setting('kubota_connect_settings_group', 'kc_api_key');
        register_setting('kubota_connect_settings_group', 'product_schedule');
        register_setting('kubota_connect_settings_group', 'finance_offer_schedule');
        register_setting('kubota_connect_settings_group', 'highlight_schedule');

        // API Key Section
        add_settings_section(
            'api_key_section',
            'API Key Settings',
            null,
            'kubota_connect_settings_page'
        );

        add_settings_field(
            'kc_api_key',
            'API Key',
            [$this, 'api_key_field_callback'],
            'kubota_connect_settings_page',
            'api_key_section'
        );

        // Import Schedule Section
        add_settings_section(
            'import_schedule_section',
            'Import Schedule Settings',
            null,
            'kubota_connect_settings_page'
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
                'kubota_connect_settings_page',
                'import_schedule_section',
                ['cpt' => $cpt]
            );
        }
    }

    public function api_key_field_callback() {
        $api_key = get_option('kc_api_key');
        echo '<input type="password" name="kc_api_key" value="' . esc_attr($api_key) . '" class="block w-full p-2 border border-gray-300 rounded">';
        if ($api_key) {
            echo '<p class="text-green-500 mt-2">API Key is set.</p>';
        }
    }

    public function schedule_field_callback($args) {
        $cpt = $args['cpt'];
        $schedule = get_option($cpt . '_schedule', 'daily');
        ?>
        <select name="<?php echo esc_attr($cpt . '_schedule'); ?>" class="block w-full p-2 border border-gray-300 rounded">
            <option value="daily" <?php selected($schedule, 'daily'); ?>>Daily</option>
            <option value="weekly" <?php selected($schedule, 'weekly'); ?>>Weekly</option>
            <option value="fortnightly" <?php selected($schedule, 'fortnightly'); ?>>Fortnightly</option>
            <option value="monthly" <?php selected($schedule, 'monthly'); ?>>Monthly</option>
        </select>
        <?php
    }
}
