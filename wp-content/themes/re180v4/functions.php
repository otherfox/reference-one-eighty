<?php
/* enqueue js */

function re180_enqueue_styles() {
    wp_enqueue_script('main-js', get_stylesheet_directory_uri().'/js/main.js');
}

add_action('wp_enqueue_scripts', 're180_enqueue_styles')

?>
