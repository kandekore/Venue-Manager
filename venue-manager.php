<?php
/**
 * Plugin Name: Venue Manager
 * Description: Professional venue post type with clean templates and Font Awesome icons.
 * Version: 1.1.1
 * Author: Darren Kandekore
 */


if (!defined('ABSPATH')) exit;

define('VM_PATH', plugin_dir_path(__FILE__));
define('VM_URL', plugin_dir_url(__FILE__));

/* ======================================================
   CPT + TAXONOMY
====================================================== */

add_action('init', function () {

    register_post_type('venues', [
        'labels' => [
            'name' => 'Venues',
            'singular_name' => 'Venue'
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'venues'],
        'menu_icon' => 'dashicons-location-alt',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true
    ]);

    register_taxonomy('venue_category', 'venues', [
        'label' => 'Venue Categories',
        'hierarchical' => true,
        'show_in_rest' => true
    ]);
});

/* ======================================================
   ASSETS
====================================================== */

// Front-end Assets
add_action('wp_enqueue_scripts', function () {
 // Load Font Awesome Pro Kit
wp_enqueue_script(
    'font-awesome-pro',
    'https://kit.fontawesome.com/6654c52133.js',
    [],
    null,
    false
);

// Add the required 'crossorigin' attribute to the script tag
add_filter('script_loader_tag', function ($tag, $handle) {
    if ('font-awesome-pro' !== $handle) return $tag;
    return str_replace(' src', ' crossorigin="anonymous" src', $tag);
}, 10, 2);

    // 2. Slick Slider Assets
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], null, true);

    // 3. PhotoSwipe Assets (Lightbox)
    wp_enqueue_style('photoswipe', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css');
    wp_enqueue_style('photoswipe-skin', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css');
    wp_enqueue_script('photoswipe-js', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js', [], null, true);
    wp_enqueue_script('photoswipe-ui', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js', [], null, true);

    // 4. Plugin styles (Ensuring it depends on Font Awesome)
    wp_enqueue_style(
        'venue-css',
        VM_URL . 'assets/venue.css',
        ['font-awesome'], // This forces your CSS to load AFTER Font Awesome
        '1.1.0'
    );

    // 5. JavaScript & Google Maps Logic
    $api_key = get_option('vm_google_maps_api_key');
    $deps = ['jquery', 'slick-js', 'photoswipe-js'];
    
    if (!empty($api_key)) {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key), [], null, true);
        $deps[] = 'google-maps';
    }

    wp_enqueue_script('venue-js', VM_URL . 'assets/venue.js', $deps, '1.1.0', true);
});
// Admin Assets (Fixes the Media Library)
add_action('admin_enqueue_scripts', function ($hook) {
    global $post;
    if ( ( $hook == 'post-new.php' || $hook == 'post.php' ) && 'venues' === $post->post_type ) {
        wp_enqueue_media(); // Required for the media picker to pop up
        wp_enqueue_script('venue-js', VM_URL . 'assets/venue.js', ['jquery'], '1.0.1', true);
    }
});

/* ======================================================
   META BOX
====================================================== */

add_action('add_meta_boxes', function () {
    add_meta_box('venue_details', 'Venue Details', 'vm_fields', 'venues', 'normal', 'high');
});

function vm_fields($post) {
    wp_nonce_field('vm_save', 'vm_nonce');

    $fields = [
        'logo'        => 'Venue Logo',
        'address'     => 'Address',
        'lat'         => 'Latitude',
        'lng'         => 'Longitude',
        'phone'       => 'Telephone',
        'email'       => 'Email',
        'website'     => 'Website URL',
        'facebook'    => 'Facebook URL',
        'instagram'   => 'Instagram URL',
        'x'           => 'X (Twitter) URL',
        'tripadvisor' => 'TripAdvisor URL',
        'gallery'     => 'Gallery'
    ];

    foreach ($fields as $key => $label) {

        $value = get_post_meta($post->ID, "vm_$key", true);

        echo '<p><strong>' . esc_html($label) . '</strong><br>';

        // Logo & Gallery (media picker)
        if (in_array($key, ['logo', 'gallery'], true)) {

            echo '<input type="hidden"
                        id="vm_' . esc_attr($key) . '"
                        name="vm_' . esc_attr($key) . '"
                        value="' . esc_attr($value) . '">';

            echo '<button type="button"
                        class="button vm-media"
                        data-target="vm_' . esc_attr($key) . '">
                        Select ' . esc_html($label) . '
                  </button>';

        // Standard text fields
        } else {

            echo '<input type="text"
                        style="width:100%"
                        name="vm_' . esc_attr($key) . '"
                        value="' . esc_attr($value) . '">';
        }

        echo '</p>';
    }
}


add_action('save_post_venues', function ($post_id) {
    // 1. Basic security and saving of text fields
    if (!isset($_POST['vm_nonce']) || !wp_verify_nonce($_POST['vm_nonce'], 'vm_save')) return;
    
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'vm_') === 0) {
            update_post_meta($post_id, $k, sanitize_text_field($v));
        }
    }

    // 2. Automated Geocoding Logic
    $address = get_post_meta($post_id, 'vm_address', true);
    $api_key = get_option('vm_google_maps_api_key');

    if (!empty($address) && !empty($api_key)) {
        // Fetch coordinates from Google
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . esc_attr($api_key);
        $response = wp_remote_get($url);

        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if ($body['status'] === 'OK') {
                $lat = $body['results'][0]['geometry']['location']['lat'];
                $lng = $body['results'][0]['geometry']['location']['lng'];

                // Automatically update the hidden Lat/Lng fields
                update_post_meta($post_id, 'vm_lat', $lat);
                update_post_meta($post_id, 'vm_lng', $lng);
            }
        }
    }
});
/* ======================================================
   TEMPLATE LOADER
====================================================== */

add_filter('template_include', function ($template) {

    if (is_singular('venues')) {
        return locate_template('single-venues.php') ?: VM_PATH . 'templates/single-venues.php';
    }

    if (is_post_type_archive('venues')) {
        return locate_template('archive-venues.php') ?: VM_PATH . 'templates/archive-venues.php';
    }

    return $template;
});

/* ======================================================
   SCHEMA.ORG
====================================================== */

add_action('wp_head', function () {

    if (!is_singular('venues')) return;

    $id = get_the_ID();

    $schema = [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "name" => get_the_title(),
        "image" => get_the_post_thumbnail_url($id,'full'),
        "url" => get_post_meta($id,'vm_website',true) ?: get_permalink(),

        "telephone" => get_post_meta($id,'vm_phone',true),
        "email" => get_post_meta($id,'vm_email',true),
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => get_post_meta($id,'vm_address',true)
        ],
        "geo" => [
            "@type" => "GeoCoordinates",
            "latitude" => get_post_meta($id,'vm_lat',true),
            "longitude" => get_post_meta($id,'vm_lng',true)
        ],
        "sameAs" => array_filter([
            get_post_meta($id,'vm_facebook',true),
            get_post_meta($id,'vm_instagram',true),
            get_post_meta($id,'vm_x',true),
            get_post_meta($id,'vm_tripadvisor',true)
        ])
    ];

    echo '<script type="application/ld+json">'.json_encode($schema).'</script>';
});

/* ======================================================
   ADMIN SETTINGS – GOOGLE MAPS API KEY
====================================================== */

add_action('admin_menu', function () {
    add_options_page(
        'Venue Manager Settings',
        'Venue Manager',
        'manage_options',
        'venue-manager-settings',
        'vm_settings_page'
    );
});

function vm_settings_page() {
    ?>
    <div class="wrap">
        <h1>Venue Manager Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('vm_settings');
                do_settings_sections('venue-manager-settings');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {

    register_setting('vm_settings', 'vm_google_maps_api_key');

    add_settings_section(
        'vm_maps_section',
        'Google Maps Configuration',
        null,
        'venue-manager-settings'
    );

    add_settings_field(
        'vm_google_maps_api_key',
        'Google Maps API Key',
        'vm_maps_key_field',
        'venue-manager-settings',
        'vm_maps_section'
    );
});

function vm_maps_key_field() {
    $key = esc_attr(get_option('vm_google_maps_api_key'));
    echo "<input type='text' name='vm_google_maps_api_key' value='$key' style='width:400px'>";
    echo "<p class='description'>
        Enter your Google Maps JavaScript API key.<br>
        Required for map-based venue archive.
    </p>";
}
