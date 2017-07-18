<?php

/* enqueue js and css */

function re180_enqueue_styles() {
    wp_enqueue_script('main-js', get_stylesheet_directory_uri().'/js/main.js');
    wp_enqueue_script('calc-js', get_stylesheet_directory_uri().'/js/calc.js');
    wp_enqueue_style('main-css', get_stylesheet_directory_uri().'/css/calc.css');
}

add_action('wp_enqueue_scripts', 're180_enqueue_styles');

/* includes */

include_once(get_stylesheet_directory().'/func/calc.php'); // calculator

?>
