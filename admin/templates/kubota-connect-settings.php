<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>

<?php if ($api_key_error): ?>
    <div class="notice notice-error"><p><?php echo esc_html($api_key_error); ?></p></div>
<?php endif; ?>
<form method="post" action="" class="mb-8">
    <input type="submit" name="import_products" class="button-primary mb-2" value="Import Products Now" <?php disabled(!$this->api_key); ?>>
    <input type="submit" name="import_finance_offers" class="button-primary mb-2" value="Import Finance Offers Now" <?php disabled(!$this->api_key); ?>>
    <input type="submit" name="import_highlights" class="button-primary mb-2" value="Import Highlights Now" <?php disabled(!$this->api_key); ?>>
    <input type="submit" name="import_categories" class="button-primary mb-2" value="Import Categories Now" <?php disabled(!$this->api_key); ?>>
</form>
<form method="post" action="options.php" class="mb-8">
    <?php
    settings_fields('api_key_settings_group');
    do_settings_sections('api_key_settings_page');
    submit_button();
    ?>
</form>
<form method="post" action="options.php" class="mb-8">
    <?php
    settings_fields('import_settings_group');
    do_settings_sections('import_settings_page');
    submit_button();
    ?>
</form>