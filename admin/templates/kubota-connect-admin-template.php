<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<div class="wrap">
    <h2></h2>
    <div class="flex flex-row mt-4 mb-4">
        <h2 class="text-2xl">Kubota Connect</h2>
        <div class="flex-auto"></div>
        <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'img/kubota-connect-logo.png' ?>" class="mr-5 h-6" alt="Kubota Connect Logo" />
    </div>
    <div class="bg-white shadow rounded-lg p-4">
        <div class="mb-4 border-b border-gray-200">
            <nav class="flex">
                <button id="settings-tab" class="text-gray-600 py-2 px-4 block focus:outline-none hover:bg-gray-100 active:bg-gray-200">Settings</button>
                <button id="documentation-tab" class="text-gray-600 py-2 px-4 block focus:outline-none hover:bg-gray-100 active:bg-gray-200">Documentation</button>
            </nav>
        </div>
        <div id="settings-content" class="tab-content">
            <?php include 'kubota-connect-settings.php'; ?>
        </div>
        <div id="documentation-content" class="tab-content hidden">
            <?php include 'kubota-connect-documentation.php'; ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const settingsTab = document.getElementById('settings-tab');
        const documentationTab = document.getElementById('documentation-tab');
        const settingsContent = document.getElementById('settings-content');
        const documentationContent = document.getElementById('documentation-content');

        settingsTab.addEventListener('click', function () {
            settingsTab.classList.add('active');
            documentationTab.classList.remove('active');
            settingsContent.classList.remove('hidden');
            documentationContent.classList.add('hidden');
        });

        documentationTab.addEventListener('click', function () {
            documentationTab.classList.add('active');
            settingsTab.classList.remove('active');
            documentationContent.classList.remove('hidden');
            settingsContent.classList.add('hidden');
        });
    });
</script>