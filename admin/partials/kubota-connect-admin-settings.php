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

<!-- <nav class="bg-white border-gray-200 dark:bg-gray-800">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl px-4 md:px-6 py-2.5">
            <a href="https://api.kubota.io/dealer-api-docs" class="flex items-center">
                <img src="<?php echo plugin_dir_path( dirname( __FILE__ ) ) . 'img/kubota-connect-logo.png' ?>" class="mr-3 h-6 sm:h-9" alt="Kubota Connect Logo" />
            </a>
            <div class="flex items-center">
                <a href="tel:5541251234" class="hidden mr-6 text-sm font-medium text-gray-900 dark:text-white hover:underline sm:inline">(555) 412-1234</a>
                <a href="#" class="text-sm font-medium sm:mr-6 text-primary-600 dark:text-primary-500 hover:underline">Contact us</a>
                <a href="#" class="hidden text-sm font-medium text-primary-600 dark:text-primary-500 hover:underline sm:inline">Login</a>
            </div>
        </div>
    </nav> -->

<h2 class="text-2xl pt-4">Kubota Connect Settings</h2>

<h3 class="text-lg font-bold pt-10">Connection Settings</h3>
<!-- API Key Token -->
<div class="flex flex-row mt-6">
    <div class="basis-64 text-base">
        API Key Token
    </div>

    <div class="w-[56rem]">
        <div class="flex flex-row">
            <input type="text" class="w-[32rem]" value="XX_YOUR_API_KEY_TOKEN_XX" />
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 ml-3 rounded">
                Test
            </button>
        </div>
        <p class="text-sm text-gray-500 mt-3">Enter your API Key Token to connect to the Kubota Connect API.</p>
        <p class="text-sm text-gray-500 mt-2">The API Key Token is encrypted in the database, but for improved security we recommend using your site's WordPress configuration file to set your password.</p>
        <p class="text-sm text-gray-500 mt-2">Choose an appropriate location within the wp-config.php file to add the following constant. A common practice is to add new constants above the line that says <span class="italic">/* That's all, stop editing! Happy blogging. */</span>.</p>
        <p class="text-sm text-gray-500 mt-2">Use the define function in PHP to add the constant named <strong>KC_API_KEY_TOKEN</strong> with a value of your API key token:</p>
    
        <div class="bg-gray-900 text-white w-[32rem] rounded-md mt-3 p-4">
            <div>
                define('KC_API_KEY_TOKEN', 'XX_YOUR_API_KEY_TOKEN_XX');
            </div>
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

<!-- Data Sync Schedule -->
<h3 class="text-lg font-bold pt-10">Data Sync Schedule</h3>
<div class="w-[72rem]">
    <p class="text-sm text-gray-500 mt-3">You can set regularly timed intervals at which data is fetched from the Kubota Connect API to ensure consistency and up-to-date information. This process can be configured to run at various frequencies depending the nature of the data being synchronized. Automated cron jobs are used to manage these sync operations, ensuring seamless and continuous data integration between your website and the Kubota information.</p>
    <p class="text-sm text-gray-500 mt-3">Please note that importing data from an external API is <strong>resource-intensive</strong> and should be performed only as often as absolutely necessary to maintain the website's smooth operation and optimal performance, preferably during less active times like night or very early morning.</p>
</div>

<div class="flex flex-row mt-6">
    <div class="basis-64 text-base text-justify">
        Products Sync
    </div>

    <div class="w-[56rem]">
        <select class="w-72">
            <option value="2">Weekly</option>
            <option value="3">Fortnighly</option>
            <option value="4">Monthly</option>
        </select>
        <p class="text-sm text-gray-500 mt-3">Select how often you would like Kubota products to sync with the Kubota Connect API.</p>
        <p class="text-sm text-gray-500 mt-2">Product information do not change often, so frequent updates are not recommended due to the high resource intensity involved.</p>
    </div>
</div>
<div class="flex flex-row mt-6">
    <div class="basis-64 text-base text-justify">
        Finance Sync
    </div>

    <div class="w-[56rem]">
        <select class="w-72">
            <option value="1">Daily</option>
            <option value="2">Weekly</option>
            <option value="3">Fortnighly</option>
            <option value="4">Monthly</option>
        </select>
        
        <p class="text-sm text-gray-500 mt-2">Select how often you would like Kubota finance offers to sync with the Kubota Connect API.</p>

        <div class="flex items-center mt-3">
            <input checked id="first-of-month-checkbox" type="checkbox" value="" class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="first-of-month-checkbox" class="ms-2 text-sm font-medium text-gray-900">Sync 1st of month</label>
        </div>
        <p class="text-sm text-gray-500 mt-2">Finance offers maily update on the first of a month.</p>
        <p class="text-sm text-gray-500 mt-2">Check this box to make sure to update on the first, regardless of the frequency settings.</p>
    </div>
</div>
<div class="flex flex-row mt-6">
    <div class="basis-64 text-base text-justify">
        Promotions Sync
    </div>

    <div class="w-[56rem]">
        <select class="w-72">
            <option value="1">Daily</option>
            <option value="2">Weekly</option>
            <option value="3">Fortnighly</option>
            <option value="4">Monthly</option>
        </select>
        <p class="text-sm text-gray-500 mt-2">Select how often you would like Kubota promotion slides to sync with the Kubota Connect API.</p>
    </div>
</div>












