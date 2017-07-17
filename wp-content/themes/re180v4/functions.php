<?php
/* enqueue js */

function re180_enqueue_styles() {
    wp_enqueue_script('main-js', get_stylesheet_uri().'/js/main.js', array('jQuery'));
}

add_action('wp_enqueue_scripts', 're180_enqueue_styles')

?>
