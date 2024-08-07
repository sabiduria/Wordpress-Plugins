<?php
/*
Plugin Name: Metal Price Plugin
Description: A plugin to display prices of metals like copper and cobalt with custom design.
Version: 1.0
Author: Antoine de Padoue Sabiduria
*/

// include the Simple HTML DOM parser library
include_once("simple_html_dom.php");

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register the shortcode
add_shortcode('metal_prices', 'display_metal_prices');

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'enqueue_metal_price_styles_and_scripts');

function enqueue_metal_price_styles_and_scripts() {
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('metal-price-styles', plugin_dir_url(__FILE__) . 'styles.css');
    wp_enqueue_script('metal-price-scripts', plugin_dir_url(__FILE__) . 'scripts.js', array('jquery'), null, true);
}

// Function to fetch and display metal prices
function display_metal_prices() {
    // Fetch metal prices (for example, using a dummy API endpoint)
    $copper_price = fetch_metal_price('copper');
    $cobalt_price = fetch_metal_price('cobalt');
    
    // Display prices with custom design
    $output = "<div class='row text-center'>";
    $output .= "<div class='col-lg-6 col-sm-6 col-xs-12 wow fadeInUp' data-wow-duration='1s' data-wow-delay='0.1s' data-wow-offset='0'>";
    $output .= "<div class='single-pricing single-pricing-white'>";
    $output .= "<div class='price-head'>";
    $output .= "<div class='copper'></div>";
    $output .= "<h2>Copper</h2>";
    $output .= "<span></span><span></span><span></span><span></span><span></span><span></span>";
    $output .= "</div>";
    $output .= "<span class='price-label'>Price</span>";
    $output .= "<h1 class='price'>$$copper_price</h1>";
    $output .= "<h5>Last Update : ".date("Y-m-d")."</h5>";
    $output .= "</div>";
    $output .= "</div>";

    $output .= "<div class='col-lg-6 col-sm-6 col-xs-12 wow fadeInUp' data-wow-duration='1s' data-wow-delay='0.1s' data-wow-offset='0'>";
    $output .= "<div class='single-pricing single-pricing-white'>";
    $output .= "<div class='price-head'>";
    $output .= "<div class='cobalt'></div>";
    $output .= "<h2>Cobalt</h2>";
    $output .= "<span></span><span></span><span></span><span></span><span></span><span></span>";
    $output .= "</div>";
    $output .= "<span class='price-label'>Price</span>";
    $output .= "<h1 class='price'>$$cobalt_price</h1>";
    $output .= "<h5>Last Update : ".date("Y-m-d")."</h5>";
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";

    return $output;
}

function fetch_metal_price($metal){
    // specify the target website's URL
    $cobalt_url = "https://ycharts.com/indicators/us_cobalt_spot_price";
    $copper_url = "https://markets.businessinsider.com/commodities/copper-price";
    $price = 0;
    $url = "";
    // initialize a cURL session
    $curl = curl_init();

    if($metal==="copper"){
        $url = $copper_url;
    } else{
        $url = $cobalt_url;
    }
    // set the website URL
    curl_setopt($curl, CURLOPT_URL, $url);

    // return the response as a string
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // follow redirects
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 

    // ignore SSL verification (not recommended in production)
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // execute the cURL session
    $htmlContent = curl_exec($curl);

    // check for errors
    if ($htmlContent === false) {
        // handle the error
        $error = curl_error($curl);
        echo "cURL error: " . $error;
        exit;
    }

    // close cURL session
    curl_close($curl);

    // create a new Simple HTML DOM instance and parse the HTML
    $html = str_get_html($htmlContent);

    if($metal==="copper"){
        $price = $html->find(".price-section__current-value", 0)." USD/Ton";
    } else{
        $price = $html->find(".key-stat-title", 0);
        $price = explode("/",$price)[0]."/Ton";
    }

    return strip_tags($price);
}
?>