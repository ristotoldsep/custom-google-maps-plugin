<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Google Maps API Settings</h1>


    <?php if (isset($_GET['import_success']) && $_GET['import_success'] == 1): ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Google Maps ACF field group imported successfully!</strong></p>
        </div>
    <?php elseif (isset($_GET['import_error']) && $_GET['import_error'] == 1): ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>There was an error importing the Google Maps ACF field group. Please try again.</strong></p>
        </div>
    <?php endif; ?>

    <!-- Instructions Section -->
    <div class="instructions-box">
        <h2>How to Set Up Your Google Maps API Key</h2>
        <p>To use the Google Maps functionality on your site, you need an API key. Follow these simple steps to generate your API key:</p>
        <ol>
            <li>Go to the <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google Maps API Key Setup Page</a>.</li>
            <li>Follow the instructions to create your API key.</li>
            <li>Once you have the key, paste it in the field below.</li>
        </ol>
        <p>For more details, visit the official Google Maps documentation.</p>
    </div>

    <!-- API Key Input and Marker Icon Upload Form -->
    <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields('my_acf_google_maps_group'); ?>
        <?php do_settings_sections('my_acf_google_maps_group'); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">Google Maps API Key</th>
                <td>
                    <input type="text" name="my_acf_google_maps_api_key" value="<?php echo esc_attr(get_option('my_acf_google_maps_api_key')); ?>" style="width: 50%;">
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Custom Marker Icon</th>
                <td>
                    <input type="hidden" name="my_acf_google_maps_marker_icon" id="my_acf_google_maps_marker_icon" value="<?php echo esc_attr(get_option('my_acf_google_maps_marker_icon')); ?>" />
                    <button class="button button-secondary" id="upload_marker_icon_button">Upload Marker Icon</button>
                    <div id="marker-icon-preview">
                        <?php
                        $icon_url = get_option('my_acf_google_maps_marker_icon');
                        if ($icon_url): ?>
                            <img src="<?php echo esc_url($icon_url); ?>" alt="Marker Icon" />
                            <br><button class="button button-secondary" id="remove_marker_icon_button">Remove Marker Icon</button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <!-- Import ACF Field Group Button -->
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="import_acf_google_maps" />
        <?php submit_button('Import Google Maps ACF Field Group', 'primary', 'import_acf_google_maps'); ?>
    </form>

    <!-- Shortcode Usage Instructions -->
    <div class="instructions-box">
        <h2>How to Use the Google Maps Shortcode</h2>
        <p>After you have configured the API key and the marker icon, you can display the map on any page by using the following shortcode:</p>
        <pre style="background: #f1f1f1; padding: 10px; border-left: 4px solid #0073aa;">[acf_google_map]</pre>
        <p>Simply paste this shortcode into the editor of the page where you want the map to appear. Ensure that you have set up ACF fields with Google Maps data on the page where this is used.</p>
    </div>
</div>