<?php

/**
 * Provide a admin settings view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://theapphub.com.au
 * @since      1.0.0
 *
 * @package    Kubota_Connect
 * @subpackage Kubota_Connect/admin/partials
 */

?>

<div class="flex flex-row mt-4">
    <h2 class="text-2xl">Kubota Connect Settings</h2>
    <div class="flex-auto"></div>
    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/kubota-connect-logo.png' ?>" class="mr-5 h-6" alt="Kubota Connect Logo" />
</div>

<h3 class="text-lg font-bold pt-10">Connection Settings</h3>

<form action="options.php" method="post">
    <?php
    // outputs a unique nounce for our plugin options
    settings_fields('kc-settings-group');
    
    // generates a unique hidden field with our form handling url
    do_settings_sections('kubota-connect');
    ?>

    <!-- API Key Token -->
    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base">
            API Key Token
        </div>

        <div class="w-[56rem]">
            
                <div class="flex flex-row">
                <input type="text" class="w-[32rem]" name="kc_api_key_token" placeholder="YOUR-API-KEY-TOKEN" value="<?php echo $options['kc_token'] ?>" />
                    <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 ml-3 rounded">
                        Connect
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-3">Enter your API Key Token to connect to the Kubota Connect API.</p>
                <p class="text-sm text-gray-500 mt-2">The API Key Token is encrypted in the database, but for improved security we recommend using your site's WordPress configuration file to set your password.</p>
                <p class="text-sm text-gray-500 mt-2">Choose an appropriate location within the wp-config.php file to add the following constant. A common practice is to add new constants above the line that says <span class="italic">/* That's all, stop editing! Happy blogging. */</span>.</p>
                <p class="text-sm text-gray-500 mt-2">Use the define function in PHP to add the constant named <strong>KC_API_KEY_TOKEN</strong> with a value of your API key token:</p>
            
                <div class="bg-gray-800 text-white w-[32rem] rounded-md mt-3 p-4">
                    define('KC_API_KEY_TOKEN', 'YOUR-API-KEY-TOKEN');
                </div>
        </div>
    </div>

    <!-- Sync Now  -->
    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base text-justify">
            Data Sync
        </div>

        <div class="w-[56rem]">
            <p class="text-sm text-gray-500">Click the button to sync your data with the Kubota Connect API</p>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4  mt-3 rounded">
                Sync Now
            </button>
        </div>
    </div>

    <hr class="mt-8" />


    <!-- Data Sync Schedule -->
    <h3 class="text-lg font-bold pt-8">Data Sync Schedule</h3>
    <div class="flex flex-row mt-6">
    <div class="basis-64 text-base text-justify">
            Automatic Data Updates
        </div>

        <div class="w-[56rem]">
            <p class="text-sm text-gray-500">You can set regularly timed intervals at which data is fetched from the Kubota Connect API to ensure consistency and up-to-date information. This process can be configured to run at various frequencies depending the nature of the data being synchronized. Automated cron jobs are used to manage these sync operations, ensuring seamless and continuous data integration between your website and the Kubota information.</p>
            <p class="text-sm text-gray-500 mt-3">Please note that importing data from an external API is <strong>resource-intensive</strong> and should be performed only as often as absolutely necessary to maintain the website's smooth operation and optimal performance. Scheduled synchronisations run at 2am server time (please ensure that your server is configured to your local time zone).</p>
        </div>
    </div>

    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base text-justify">
            Products Sync
        </div>

        <div class="w-[56rem]">
            <select name="kc_product_sync" class="w-72">
                <?php $selected = $options['kc_product_sync']; ?>
                <option value="weekly" <?php echo ($selected == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="fortnightly" <?php echo ($selected == 'fortnightly') ? 'selected' : ''; ?>>Fortnightly</option>
                <option value="monthly" <?php echo ($selected == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
            <p class="text-sm text-gray-500 mt-3 italic">Select how often you would like Kubota products to be updated by the Kubota Connect API.</p>
            <p class="text-sm text-gray-500 mt-2">Product information do not change often, so frequent updates are not recommended due to the high resource intensity involved.</p>
            <p class="text-sm text-gray-500 mt-2">Our suggested schedule is <span class="italic">fortnighlty<span> or <span class="italic">monthly<span>.</p>

        </div>
    </div>
    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base text-justify">
            Finance Sync
        </div>

        <div class="w-[56rem]">
            <select name="kc_finance_sync" class="w-72">
                <?php $selected = $options['kc_finance_sync']; ?>
                <option value="daily" <?php echo ($selected == 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo ($selected == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="fortnightly" <?php echo ($selected == 'fortnightly') ? 'selected' : ''; ?>>Fortnightly</option>
                <option value="monthly" <?php echo ($selected == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
            
            <p class="text-sm text-gray-500 mt-2 italic">Select how often you would like Kubota finance offers to be updated by the Kubota Connect API.</p>

            <label class="inline-flex items-center cursor-pointer mt-3">
                <?php $checked = ($options['kc_finance_first_of_month']) == '1' ? 'checked' : ''; ?>
                <input type="checkbox" name="kc_finance_first_of_month" value="1" class="sr-only peer" <?php echo $checked ?>>
                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <span class="ms-3 text-sm font-medium text-gray">Update 1<sup>st</sup> of month</span>
            </label>
            <p class="text-sm text-gray-500 mt-3">Finance offers mainly get updated by Kubota on the first of a month.</p>
            <p class="text-sm text-gray-500 mt-2">Check this box to make sure to update on the first, regardless of the frequency settings.</p>
            <p class="text-sm text-gray-500 mt-2">Finance offers stay mostly unchanged for a few month, so our suggested schedule is <span class="italic">fortnighlty</span> or <span class="italic">monthly</span> plus update on the first of each month.</p>
        </div>
    </div>
    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base text-justify">
            Promotions Sync
        </div>

        <div class="w-[56rem]">
            <select name="kc_slider_sync" class="w-72">
                <?php $selected = $options['kc_slider_sync']; ?>
                <option value="daily" <?php echo ($selected == 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo ($selected == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="fortnightly" <?php echo ($selected == 'fortnightly') ? 'selected' : ''; ?>>Fortnightly</option>
                <option value="monthly" <?php echo ($selected == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
            <p class="text-sm text-gray-500 mt-2 italic">Select how often you would like Kubota promotion slides to be updated by the Kubota Connect API.</p>
            <p class="text-sm text-gray-500 mt-2">New promotions can be added by Kubota any day, so our suggested sync schedule is <span class="italic">daily<span>.</p>

        </div> 
    </div>

    <?php @submit_button('Save Settings'); ?>

</form>










