<?php

include 'settings.php';

class Woocommerce_Seo_Admin{
       
    function __construct() {
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_admin_seo_tab' ) );
        add_action( 'woocommerce_settings_tabs_seo', array( $this, 'add_settings' ) );
        add_action( 'woocommerce_update_options_seo', array( $this, 'save_settings' ) );
        
        if( get_option('woocommerce_seo_category_meta_enabled') == "yes" ) include 'category.php';
        if( get_option('woocommerce_seo_product_meta_enabled') == "yes" ) include 'product.php';
    }
    
    function add_admin_seo_tab($tabs) {
        $tabs['seo'] = 'SEO';
        return $tabs;
    }
    
    function add_settings() {
        woocommerce_admin_fields( woocommerce_seo_settings() );
    }
    
    function save_settings() {
        woocommerce_update_options( woocommerce_seo_settings() );
    }
}

$woocommerce_seo_admin = new Woocommerce_Seo_Admin();

?>
