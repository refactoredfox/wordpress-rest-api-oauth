<div class="wrap">
    <h2>REST OAuth Options and Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields('refactoredfox-main'); ?>

        <?php do_settings_sections(RF_REST_OAUTH_PREFIX); ?>

        <?php submit_button(); ?>
    </form>
</div>
