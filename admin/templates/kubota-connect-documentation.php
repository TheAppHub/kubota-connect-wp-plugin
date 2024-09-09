<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<h2 class="text-xl font-semibold mb-2">Shortcodes</h2>
<p class="text-gray-600">The Kubota Connect plugin provides various shortcodes to display product and highlight information on your site.</p>
<p class="mb-4 text-gray-600">Each shortcode will generate specific <code>kubota-connect</code> CSS classes that you can target for custom styling. Additionally, you can pass the attribute <code>theme="material"</code> to any shortcode to output a pre-styled result using Material Design principles, offering a polished and modern appearance. If needed, you can also pass the attribute <code>post_id</code> to specify the post from which to pull the data.</p>
<h3 class="text-lg font-semibold mb-2">Product Shortcodes</h3>
<ul class="list-disc list-inside mb-4">
    <li class="text-gray-600"><code>[kubota-connect-product-description]</code> - Displays the product description.</li>
    <li class="text-gray-600"><code>[kubota-connect-product-features]</code> - Displays a list of product features.</li>
    <li class="text-gray-600"><code>[kubota-connect-product-brochure]</code> - Displays a link to the product brochure.</li>
    <li class="text-gray-600"><code>[kubota-connect-product-documents]</code> - Displays links to additional product documents.</li>
    <li class="text-gray-600"><code>[kubota-connect-product-model-names]</code> - Displays the model names in an HTML unordered list.</li>
    <li class="text-gray-600"><code>[kubota-connect-product-specs-table]</code> - Displays the models and specifications in a table.</li>
    <li class="text-gray-600"><code>[kubota-connect-image]</code> - Displays the image of the product.</li>
    <li class="text-gray-600"><code>[kubota-connect-hero-image]</code> - Displays the hero image of the product.</li>
</ul>
<h3 class="text-lg font-semibold mb-2">Category Shortcodes</h3>
<ul class="list-disc list-inside mb-4">
    <li class="text-gray-600"><code>[kubota-connect-category-description]</code> - Displays the Kubota category description.</li>
</ul>
<h3 class="text-lg font-semibold mt-4 mb-2">Highlight Shortcodes</h3>
<ul class="list-disc list-inside">
    <li class="text-gray-600"><code>[kubota-connect-highlight-title]</code> - Displays the highlight title.</li>
    <li class="text-gray-600"><code>[kubota-connect-highlight-description]</code> - Displays the highlight description.</li>
    <li class="text-gray-600"><code>[kubota-connect-image]</code> - Displays the highlight image.</li>
</ul>
<h3 class="text-lg font-semibold mt-4 mb-2">Finance Offers Shortcodes</h3>
<ul class="list-disc list-inside ">
    <li class="text-gray-600"><code>[kubota-connect-finance-offer-type]</code> - Displays the offer type.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-rate-type]</code> - Displays the type of finance.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-rate]</code> - Displays the rate in %.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-term-in-months]</code> - Displays the finance term in months.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-deposit]</code> - Displays the finance minimum deposit.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-offer-expiry-date]</code> - Displays the expiry date of the offer.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-terms]</code> - Displays the finance terms and conditions.</li>
    <li class="text-gray-600"><code>[kubota-connect-image]</code> - Displays the finance image.</li>
    <li class="text-gray-600"><code>[kubota-connect-hero-image]</code> - Displays the finance hero image.</li>
    <li class="text-gray-600"><code>[kubota-connect-finance-offer]</code> - Displays the finance offer.</li>
</ul>
<h3 class="text-md font-semibold mt-6 mb-2">Usage Example</h3>
<p class="text-gray-600">To display a table of product models and specifications from a specific post with Material Design styling, you can use the following shortcode:</p>
<pre><code>[kubota-connect-product-specs-table post_id="123" theme="material"]</code></pre>
<p>In this example, the shortcode pulls data from the post with ID 123 and applies Material Design styling to the table output.</p>
<h2 class="text-xl font-semibold mt-8 mb-2">Custom Content</h2>
<p class="text-gray-600">Kubota Products and Kubota Finance Offers retain the default WordPress editor, allowing the administrator to add custom copy to each post.</p>
<p>This editor field is the only one that remains editable by admins. All other fields will be automatically overwritten during each content update to ensure the latest information from the Kubota Connect API is reflected.</p>