<?php
/**
 * Plugin Name: Venue Manager
 * Description: Professional venue post type with clean templates and Font Awesome icons.
 * Version: 1.0.0
 * Author: Darren Kandekore
 */

if (!defined('ABSPATH')) exit;

define('VM_PATH', plugin_dir_path(__FILE__));
define('VM_URL', plugin_dir_url(__FILE__));

/* -----------------------------
   CPT + TAXONOMY
----------------------------- */

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

/* -----------------------------
   ASSETS
----------------------------- */

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style('font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');

    wp_enqueue_style('venue-css', VM_URL . 'assets/venue.css');

    wp_enqueue_script('venue-js', VM_URL . 'assets/venue.js', ['jquery'], null, true);
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_media();
});

/* -----------------------------
   META BOX
----------------------------- */

add_action('add_meta_boxes', function () {
    add_meta_box('venue_details', 'Venue Details', 'vm_fields', 'venues', 'normal', 'high');
});

function vm_fields($post) {
    wp_nonce_field('vm_save', 'vm_nonce');

    $fields = [
        'logo' => 'Venue Logo',
        'address' => 'Address',
        'lat' => 'Latitude',
        'lng' => 'Longitude',
        'phone' => 'Telephone',
        'email' => 'Email',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'x' => 'X',
        'tripadvisor' => 'TripAdvisor',
        'gallery' => 'Gallery'
    ];

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, "vm_$key", true);

        if ($key === 'logo' || $key === 'gallery') {
            echo "<p><strong>$label</strong><br>
            <input type='hidden' id='vm_$key' name='vm_$key' value='$value'>
            <button class='button vm-media' data-target='vm_$key'>Select</button></p>";
        } else {
            echo "<p><strong>$label</strong><br>
            <input type='text' style='width:100%' name='vm_$key' value='" . esc_attr($value) . "'></p>";
        }
    }
}

add_action('save_post_venues', function ($post_id) {
    if (!isset($_POST['vm_nonce']) || !wp_verify_nonce($_POST['vm_nonce'], 'vm_save')) return;
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'vm_') === 0) update_post_meta($post_id, $k, sanitize_text_field($v));
    }
});

/* -----------------------------
   TEMPLATE LOADER
----------------------------- */

add_filter('template_include', function ($template) {

    if (is_singular('venues')) {
        return locate_template('single-venues.php') ?: VM_PATH . 'templates/single-venues.php';
    }

    if (is_post_type_archive('venues')) {
        return locate_template('archive-venues.php') ?: VM_PATH . 'templates/archive-venues.php';
    }

    return $template;
});

/* -----------------------------
   SCHEMA.ORG
----------------------------- */

add_action('wp_head', function () {
    if (!is_singular('venues')) return;

    $id = get_the_ID();

    $schema = [
        "@context" => "https://schema.org",
        "@type" => "LocalBusiness",
        "name" => get_the_title(),
        "address" => get_post_meta($id, 'vm_address', true),
        "telephone" => get_post_meta($id, 'vm_phone', true),
        "email" => get_post_meta($id, 'vm_email', true),
        "url" => get_permalink()
    ];

    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
});
