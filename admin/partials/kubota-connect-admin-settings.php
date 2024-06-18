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
    settings_fields('kc-api-key-token');
    
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
                <input type="text" id="kc-token" class="w-[32rem]" name="kc_api_key_token" placeholder="YOUR-API-KEY-TOKEN" value="<?php echo $api_key_token ?>" />
                    <button id="kc-connect" class="bg-orange-500 disabled:opacity50 hover:bg-orange-700 text-white font-bold py-2 px-4 ml-3 rounded">
                        Connect
                    </button>
                    <button id="kc-connect-processing" type="button" class="hidden flex bg-orange-500 text-white font-bold py-2 px-4 ml-3 rounded" disabled>
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
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
            <?php if($sync_options['kc_dealer_name'] !== '') { ?>
            <p class="text-sm text-gray-500">This plugin is connected to the Kubota Connect API for <strong><?php echo $sync_options['kc_dealer_name'] ?></strong>.</p>
            <?php } ?>
            <p class="text-sm text-gray-500">Click the button to sync your data with the Kubota Connect API</p>
            <button id="kc-sync" <?php echo ($api_key_token === '') ? 'disabled' : '' ?> class="bg-orange-500 disabled:opacity-50 enabled:hover:bg-orange-700 text-white font-bold py-2 px-4 mt-3 rounded">
                Sync Now
            </button>
            <button id="kc-sync-processing" type="button" class="hidden flex bg-orange-500 text-white font-bold py-2 px-4 mt-3 rounded" disabled>
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
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
            <p class="text-sm text-gray-500 mt-3">Please note that importing data from an external API is <strong>resource-intensive</strong> and should be performed only as often as relly necessary to maintain the website's smooth operation and optimal performance. Scheduled synchronisations run at 2am server time (please ensure that your server is configured to your local time zone).</p>
        </div>
    </div>

    <div class="flex flex-row mt-6">
        <div class="basis-64 text-base text-justify">
            Products Sync
        </div>

        <div class="w-[56rem]">
            <select name="kc_product_sync" class="w-72">
                <?php $selected = $sync_options['kc_product_sync']; ?>
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
                <?php $selected = $sync_options['kc_finance_sync']; ?>
                <option value="daily" <?php echo ($selected == 'daily') ? 'selected' : ''; ?>>Daily</option>
                <option value="weekly" <?php echo ($selected == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                <option value="fortnightly" <?php echo ($selected == 'fortnightly') ? 'selected' : ''; ?>>Fortnightly</option>
                <option value="monthly" <?php echo ($selected == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
            </select>
            
            <p class="text-sm text-gray-500 mt-2 italic">Select how often you would like Kubota finance offers to be updated by the Kubota Connect API.</p>

            <label class="inline-flex items-center cursor-pointer mt-3">
                <?php $checked = ($sync_options['kc_finance_first_of_month']) == '1' ? 'checked' : ''; ?>
                <input type="checkbox" name="kc_finance_first_of_month" value="1" class="sr-only peer" <?php echo $checked ?>>
                <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300  peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
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
                <?php $selected = $sync_options['kc_slider_sync']; ?>
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

<div id="errorModal" class="hidden absolute z-50 left-0 top-0 w-full h-full overflow-auto bg-gray-900/[.7] -ml-5">
    <div class="relative top-24 p-4 w-full m-auto max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow ">
            <button type="button" class="kc-close-error absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-2 text-lg font-normal text-gray-500">Sorry, something went wrong!</h3>
                <p  id="kc-error-msg" class="mb-5 text-md font-normal text-gray-500">An error occured.</p>
                <button type="button" class="kc-close-error text-white font-bold bg-orange-600 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    OK, I can fix it
                </button>
                <a href="mailto:kubota-connect@theapphub.com.au?subject=Problem with Kubota Connect WordPress Plugin" class="kc-close-error py-3 px-5 ms-3 text-sm font-bold text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">I need help</a>
            </div>
        </div>
    </div>
</div>

<div id="successModal" class="hidden absolute z-50 left-0 top-0 w-full h-full overflow-auto bg-gray-900/[.7] -ml-5">
    <div class="relative top-24 p-4 w-full m-auto max-w-md max-h-full animate-fade-in">
        <div class="relative bg-white rounded-lg shadow ">
            <button type="button" class="kc-close-success absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="popup-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-2 text-lg font-normal text-gray-500">Congratulations!</h3>
                <p  id="kc-success-msg" class="mb-5 text-md font-normal text-gray-500">It's all great!</p>
                <button type="button" class="kc-close-success text-white bg-orange-600 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>







