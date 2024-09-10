<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<?php if ($api_key_error): ?>
    <div class="notice notice-error"><p><?php echo esc_html($api_key_error); ?></p></div>
<?php endif; ?>

    <h2 class="text-xl font-bold text-gray-800 mb-4">Important: Secure Storage of API Keys</h2>
    
    <p class="text-gray-600 mb-2">
        Storing API keys in the database is not recommended due to potential security vulnerabilities. If your site is ever compromised, attackers could easily access your API keys, potentially exposing sensitive data.
    </p>

    <p class="text-gray-600 mb-2">
        To improve security, we recommend defining your API keys directly in the <code class="bg-gray-200 p-1 rounded">wp-config.php</code> file. This keeps your keys out of the database and makes them less accessible to potential attackers.
    </p>

    <p class="text-gray-600 mb-4">
        Below is an example of how to define the <code class="bg-gray-200 p-1 rounded">KC_API_KEY_TOKEN</code> in your <code class="bg-gray-200 p-1 rounded">wp-config.php</code> file:
    </p>

    <div class="mb-8">
        <code class="text-sm inline-flex text-left items-center space-x-4 bg-gray-800 text-white rounded-lg p-3 pl-5">
            <span class="flex gap-4">
                <span class="flex-1">
                    <span>
                        define('KC_API_KEY_TOKEN', 
                    </span>
                    <span class="text-yellow-500">
                        'your-api-key-token'
                    </span>
                    <span>
                        );
                    </span>
                </span>
            </span>

            <!-- SVG icon for copying the text -->
            <svg id="copy-svg" class="shrink-0 h-5 w-5 transition text-gray-500 cursor-pointer hover:text-white" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
                <path
                    d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z">
                </path>
            </svg>
        </code>
    </div>

    <form method="post" action="options.php" class="mb-8">
        <?php
        settings_fields('api_key_settings_group');
        do_settings_sections('api_key_settings_page');
        submit_button();
        ?>
    </form>




<script>
    // JavaScript for copying the text to clipboard
    const svgElement = document.getElementById('copy-svg');
    const textToCopy = "define( 'KC_API_KEY_TOKEN', 'your-api-key-token' );"; // Text to be copied

    svgElement.addEventListener('click', function() {
        navigator.clipboard.writeText(textToCopy).then(function() {
            alert('Copied: ' + textToCopy + ' to clipboard.'); // Optional alert or you can add a visual indicator
        }).catch(function(error) {
            console.error('Error copying text: ', error);
        });
    });
</script>


