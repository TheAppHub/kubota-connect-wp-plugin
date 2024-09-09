<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<h3 class="text-xl mb-4">Data Sync</h3>

<p class="text-sm text-gray-600 mb-4">The Kubota Connect plugin allows you to import and synchronize data from the Kubota Connect API to your website. This data includes Kubota products, finance offers, highlights, and categories. You can import this data manually or schedule regular synchronizations to keep your website up-to-date with the latest information from Kubota.</p>

<h4 class="text-lg mb-4">Sync Now</h4>
<p class="text-sm text-gray-600 mb-4">Click the buttons below to manually import data from the Kubota Connect API. You can import Kubota products, finance offers, and highlights. Please note that you need to enter your API Key Token to enable data imports.</p>

<form method="post" action="" class="mb-8">
    <input type="submit" name="import_products" class="button-primary mb-2" value="Import Products" <?php disabled(!$this->api_key); ?>>
    <input type="submit" name="import_finance_offers" class="button-primary mb-2" value="Import Finance Offers" <?php disabled(!$this->api_key); ?>>
    <input type="submit" name="import_highlights" class="button-primary mb-2" value="Import Highlights" <?php disabled(!$this->api_key); ?>>
</form>

<form method="post" action="options.php" class="mb-8">
    <?php
    settings_fields('import_settings_group'); // Keep this to handle the nonce and option group
    ?>

<h4 class="text-lg mb-4">Schedule Sync</h4>

    <p class="text-sm text-gray-600">You can set regularly timed intervals at which data is fetched from the Kubota Connect API to ensure consistency and up-to-date information. This process can be configured to run at various frequencies depending the nature of the data being synchronized. Automated cron jobs are used to manage these sync operations, ensuring seamless and continuous data integration between your website and the Kubota information.</p>
    <p class="text-sm text-gray-600 mt-3">Please note that importing data from an external API is <strong>resource-intensive</strong> and should be performed only as often as relly necessary to maintain the website's smooth operation and optimal performance. Scheduled synchronisations run at 2am server time (please ensure that your server is configured to your local time zone).</p>

    <table class="form-table">
        
        <!-- Products Section -->
        <tr>
            <th scope="row"><label for="product_schedule">Products</label></th>
            <td>
                <div>
                    <select name="product_schedule" id="product_schedule">
                        <option value="weekly" <?php selected(get_option('product_schedule'), 'weekly'); ?>>Weekly</option>
                        <option value="fortnightly" <?php selected(get_option('product_schedule'), 'fortnightly'); ?>>Fortnightly</option>
                        <option value="monthly" <?php selected(get_option('product_schedule'), 'monthly'); ?>>Monthly</option>
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
            <th scope="row"><label for="finance_offer_schedule">Finance Offers</label></th>
            <td>
                <div>
                    <select name="finance_offer_schedule" id="finance_offer_schedule">
                        <option value="weekly" <?php selected(get_option('finance_offer_schedule'), 'weekly'); ?>>Weekly</option>
                        <option value="fortnightly" <?php selected(get_option('finance_offer_schedule'), 'fortnightly'); ?>>Fortnightly</option>
                        <option value="monthly" <?php selected(get_option('finance_offer_schedule'), 'monthly'); ?>>Monthly</option>
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
            <th scope="row"><label for="highlight_schedule">Highlights</label></th>
            <td>
                <div>
                    <select name="highlight_schedule" id="highlight_schedule">
                        <option value="daily" <?php selected(get_option('highlight_schedule'), 'daily'); ?>>Daily</option>
                        <option value="weekly" <?php selected(get_option('highlight_schedule'), 'weekly'); ?>>Weekly</option>
                        <option value="fortnightly" <?php selected(get_option('highlight_schedule'), 'fortnightly'); ?>>Fortnightly</option>
                        <option value="monthly" <?php selected(get_option('highlight_schedule'), 'monthly'); ?>>Monthly</option>
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