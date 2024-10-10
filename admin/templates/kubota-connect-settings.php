<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<h3 class="text-xl mb-4">Data Sync</h3>

<p class="text-sm text-gray-600 mb-4">The Kubota Connect plugin allows you to import and synchronize data from the Kubota Connect API to your website. This data includes Kubota products, finance offers, highlights, and categories. You can import this data manually or schedule regular synchronizations to keep your website up-to-date with the latest information from Kubota.</p>

<h4 class="text-lg mb-4">Sync Now</h4>
<p class="text-sm text-gray-600 mb-4">Click the buttons below to manually import data from the Kubota Connect API. You can import Kubota products, finance offers, and highlights. Please note that you need to enter your API Key Token to enable data imports.</p>

<form method="post" action="" class="mb-8">
    <button type="submit" name="import_products" class="button-primary mb-2 relative w-40 h-10 flex items-center justify-center" <?php disabled(!$this->api_key); ?>>   
        <span class="button-text opacity-100 visible">Import Products</span>
        <div class="loading-spinner absolute inset-0 flex items-center justify-center opacity-0 invisible">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Importing...</span>   
        </div>
    </button>

    <button type="submit" name="import_finance_offers" class="button-primary mb-2 relative w-40 h-10 flex items-center justify-center" <?php disabled(!$this->api_key); ?>>
        <span class="button-text opacity-100 visible">Import Finance Offers</span>
        <div class="loading-spinner absolute inset-0 flex items-center justify-center opacity-0 invisible">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Importing...</span>   
        </div>
    </button>

    <button type="submit" name="import_highlights" class="button-primary mb-2 relative w-40 h-10 flex items-center justify-center" <?php disabled(!$this->api_key); ?>>
        <span class="button-text opacity-100 visible">Import Highlights</span>
        <div class="loading-spinner absolute inset-0 flex items-center justify-center opacity-0 invisible">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Importing...</span>   
        </div>
    </button>
</form>

<script>
    document.querySelectorAll('form button[type="submit"]').forEach(button => {
        button.addEventListener('click', function() {
            const spinner = this.querySelector('.loading-spinner');
            const buttonText = this.querySelector('.button-text');
            spinner.classList.remove('opacity-0', 'invisible');
            spinner.classList.add('opacity-100', 'visible');
            buttonText.classList.add('opacity-0', 'invisible');
        });
    });
</script>


<form method="post" action="options.php" class="mb-8">
    <?php
    settings_fields('import_settings_group'); // Keep this to handle the nonce and option group
    ?>

<h4 class="text-lg mb-4">Schedule Sync</h4>

    <p class="text-sm text-gray-600">You can set regularly timed intervals at which data is fetched from the Kubota Connect API to ensure consistency and up-to-date information. This process can be configured to run at various frequencies depending the nature of the data being synchronized. Automated cron jobs are used to manage these sync operations, ensuring seamless and continuous data integration between your website and the Kubota information.</p>
    <p class="text-sm text-gray-600 mt-3">Please note that importing data from an external API is <strong>resource-intensive</strong> and should be performed only as often as really necessary to maintain the website's smooth operation and optimal performance. Scheduled synchronisations run at 2am server time (please ensure that your server is configured to your local time zone).</p>

    <table class="form-table">
        
        <!-- Products Section -->
        <tr>
            <th scope="row"><label for="kubota-product_schedule">Products</label></th>
            <td>
                <div>
                    <select name="kubota-product_schedule" id="product_schedule">
                        <option value="daily" <?php selected(get_option('kubota-product_schedule'), 'daily'); ?>>Weekly</option>
                        <option value="weekly" <?php selected(get_option('kubota-product_schedule'), 'weekly'); ?>>Weekly</option>
                    </select>
                </div>
                <div class="mt-4 mb-4">
                    <p class="text-sm text-gray-600 mt-3 italic">Select how often you would like Kubota products to be updated by the Kubota Connect API.</p>
                    <p class="text-sm text-gray-600 mt-2">Product information do not change often, so frequent updates are not recommended due to the high resource intensity involved.</p>
                    <p class="text-sm text-gray-600 mt-2 mb-3">Our suggested schedule is <span class="italic">fortnighlty<span> or <span class="italic">monthly<span>.</p>
                </div>
            </td>
        </tr>

        <!-- Finance Offers Section -->
        <tr>
            <th scope="row"><label for="kubota-finance_offer_schedule">Finance Offers</label></th>
            <td>
                <div>
                    <select name="kubota-finance_offer_schedule" id="finance_offer_schedule">
                        <option value="daily" <?php selected(get_option('kubota-finance_offer_schedule'), 'daily'); ?>>Weekly</option>
                        <option value="weekly" <?php selected(get_option('kubota-finance_offer_schedule'), 'weekly'); ?>>Weekly</option>
                    </select>
                </div>

                <div class="mt-4">
                <p class="text-sm text-gray-600 mt-2">Select how often you would like Kubota finance offers to be updated by the Kubota Connect API.</p>
                <p class="text-sm text-gray-600 mt-3">Finance offers mainly get updated by Kubota on the first of a month.</p>
                <p class="text-sm text-gray-600 mt-2">Finance offers stay mostly unchanged for a few month, so our suggested schedule is <span class="italic">fortnighlty</span> or <span class="italic">monthly</span> plus update on the first of each month.</p>
                </div>
            </td>
        </tr>

        <!-- Highlights Section -->
        <tr>
            <th scope="row"><label for="kubota-highlight_schedule">Highlights</label></th>
            <td>
                <div>
                    <select name="kubota-highlight_schedule" id="highlight_schedule">
                        <option value="daily" <?php selected(get_option('kubota-highlight_schedule'), 'daily'); ?>>Daily</option>
                        <option value="weekly" <?php selected(get_option('kubota-highlight_schedule'), 'weekly'); ?>>Weekly</option>
                    </select>
                </div>

                <div class="mt-4">
                <p class="text-sm text-gray-600 mt-2 italic">Select how often you would like Kubota promotion slides to be updated by the Kubota Connect API.</p>
                <p class="text-sm text-gray-600 mt-2">New promotions can be added by Kubota any day, so our suggested sync schedule is <span class="italic">daily<span>.</p>
                </div>

            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>