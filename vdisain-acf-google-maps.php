<?php

/**
 * Plugin Name: Custom ACF Google Maps vDisain
 * Description: Adds a Google Map with a shortcode and ACF integration.
 * Version: 1.0
 * Author: Risto Tõldsep vDisain
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class VdisainACFGoogleMaps
{

    public function __construct()
    {
        add_filter('acf/fields/google_map/api', [$this, 'set_acf_google_map_api']);
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_post_import_acf_google_maps', [$this, 'import_acf_google_maps']); // Handle the import on button click
        add_shortcode('acf_google_map', [$this, 'render_shortcode']);
    }

    public function add_settings_page()
    {
        add_menu_page(
            'vDisain Google Maps',
            'vDisain Google Maps',
            'manage_options',
            'my-acf-google-maps',
            [$this, 'settings_page'],
            'dashicons-location-alt'
        );
    }

    public function enqueue_admin_assets($hook)
    {
        // Only enqueue on the plugin's settings page
        if ($hook !== 'toplevel_page_my-acf-google-maps') {
            return;
        }

        // Enqueue the WordPress media uploader scripts
        wp_enqueue_media();

        // Optionally, enqueue any custom styles/scripts for the settings page
        wp_enqueue_script('my-acf-google-maps-admin-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', ['jquery'], null, true);
        wp_enqueue_style('my-acf-google-maps-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', [], null);
    }

    public function register_settings()
    {
        register_setting('my_acf_google_maps_group', 'my_acf_google_maps_api_key');
        register_setting('my_acf_google_maps_group', 'my_acf_google_maps_marker_icon'); // Register the marker icon URL
    }

    public function settings_page()
    {
        include 'settings-page.php';
    }

    public function set_acf_google_map_api($api)
    {
        $api['key'] = get_option('my_acf_google_maps_api_key');
        return $api;
    }

    public function enqueue_assets()
    {
        wp_enqueue_style('my-acf-google-maps-css', plugin_dir_url(__FILE__) . 'assets/css/map.css');
        wp_enqueue_script('my-acf-google-maps-js', plugin_dir_url(__FILE__) . 'assets/js/map.js', ['jquery'], null, true);

        $api_key = get_option('my_acf_google_maps_api_key');
        if ($api_key) {
            wp_enqueue_script('google-maps-api', '//maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key) . '&v=weekly&libraries=marker', [], null, false);
        }
    }

    public function render_shortcode($atts)
    {
        // Retrieve the Google Maps API key from settings
        $api_key = get_option('my_acf_google_maps_api_key');

        // Check if API key is missing
        if (empty($api_key)) {
            // Return message to inform the user to enter an API key
            return '<p style="padding:20px;background-color:#f1f1f1;">Please enter your Google Maps API key in the plugin settings.</p>';
        }

        // Retrieve the Google Maps group field from ACF
        $google_maps_group = get_field('custom_google_maps'); // Custom Google Maps is ACF Group field

        if ($google_maps_group && !empty($google_maps_group['kaardi_asukohad'])) { // Kaardi_asukohad is ACF Repeater field
            $output = '<div class="acf-map" data-zoom="16">';

            foreach ($google_maps_group['kaardi_asukohad'] as $location_data) {
                $location = $location_data['aadress']; // Google Maps location field
                $title = $location_data['pealkiri']; // Title (h3)
                $address_text = $location_data['address_text']; // Address text

                // Retrieve the custom marker icon or fallback to the default icon in plugin folder
                $marker_icon = get_option('my_acf_google_maps_marker_icon') ?: plugin_dir_url(__FILE__) . 'assets/images/fallback_marker_icon.png';

                $output .= '<div class="marker" data-lat="' . esc_attr($location['lat']) . '" data-lng="' . esc_attr($location['lng']) . '"';
                $output .= ' data-name="' . esc_attr(!empty($location['name']) ? $location['name'] : $location['address']) . '"';
                $output .= ' data-icon="' . esc_url($marker_icon) . '">';

                $output .= '<div class="map_info_content">';

                if ($title) {
                    $output .= '<h3>' . esc_html($title) . '</h3>';
                }

                if ($address_text) {
                    $output .= '<p class="map_info_content__small_title">' . __('Aadress:', 'propaan') . '</p>';
                    $output .= '<p class="map_info_content__text">' . esc_html($address_text) . '</p>';
                }

                $output .= '</div></div>';
            }

            $output .= '</div>';

            return $output;
        }

        return ''; // Return empty string if no locations are found
    }


    // Handle ACF Google Maps Field Group Import when the button is clicked
    public function import_acf_google_maps()
    {
        // Check if ACF is active
        if (function_exists('acf_import_field_group')) {
            $file_path = plugin_dir_path(__FILE__) . 'google_maps_field_group.json';

            if (file_exists($file_path)) {
                $json_data = file_get_contents($file_path);
                $field_groups = json_decode($json_data, true);

                if ($field_groups && is_array($field_groups)) {
                    foreach ($field_groups as $field_group) {
                        acf_import_field_group($field_group);
                    }

                    // Redirect to settings page with success message
                    wp_redirect(admin_url('admin.php?page=my-acf-google-maps&import_success=1'));
                    exit;
                } else {
                    // Redirect to settings page with error message
                    wp_redirect(admin_url('admin.php?page=my-acf-google-maps&import_error=1'));
                    exit;
                }
            } else {
                // Redirect to settings page with error message
                wp_redirect(admin_url('admin.php?page=my-acf-google-maps&import_error=1'));
                exit;
            }
        }
    }
}

new VdisainACFGoogleMaps();