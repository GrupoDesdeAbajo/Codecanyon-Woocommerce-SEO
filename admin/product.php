<?php

function woocommerce_seo_add_product_seo_data_tab(){
    ?>
    <li class="advanced_options"><a href="#seo_options" title="<?php _e('Woocommerce SEO options', 'woocommerce'); ?>"><?php _e('SEO', 'woocommerce'); ?></a></li>
    <?php
}
add_action( 'woocommerce_product_write_panel_tabs', 'woocommerce_seo_add_product_seo_data_tab', 99 );

function woocommerce_seo_add_product_seo_data_fields(){
    ?>
    <div id="seo_options" class="panel woocommerce_options_panel">
        <div class="options_group">  
            <?php
            //Enabled/Options
            woocommerce_wp_select( array( 'id' => 'seo_option', 'label' => __('Enabled/Option?', 'woocommerce'), 'options' => array(
                    'disabled' => __('Disabled', 'woocommerce'),
                    'global' => __('Global', 'woocommerce'),
                    'template' => __('Template', 'woocommerce'),
                    'custom' => __('Custom', 'woocommerce')
            ) ) );        
            //Page title
            woocommerce_wp_text_input( array( 'id' => 'seo_page_title', 'label' => __('Page Title', 'woocommerce'), 'description' => __('The page title for the product page', 'woocommerce'), 'class' => 'long' ) );     
            //Meta Description
            woocommerce_wp_textarea_input(  array( 'id' => 'seo_meta_desc', 'label' => __('Meta Description', 'woocommerce'), 'description' => __('The meta description for the product page', 'woocommerce') ) ); 
            //Noindex
            woocommerce_wp_checkbox( array( 'id' => 'seo_noindex', 'label' => __('Noindex this product', 'woocommerce') ) );     
            //Nofollow
            woocommerce_wp_checkbox( array( 'id' => 'seo_nofollow', 'label' => __('Nofollow this product', 'woocommerce') ) );
            ?>
        </div>
    </div>
    <?php
}
add_action( 'woocommerce_product_write_panels', 'woocommerce_seo_add_product_seo_data_fields' );

function woocommerce_seo_save_product_seo_data( $post_id ){
    if ( !$_POST ) return $post_id;
    if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
    if( is_int( wp_is_post_autosave( $post_id ) ) ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
    
    if( isset( $_POST['seo_option'] ) ) update_post_meta( $post_id, 'seo_option', $_POST['seo_option'] );
    if(isset($_POST['seo_page_title'])) update_post_meta($post_id, 'seo_page_title', esc_html(stripslashes($_POST['seo_page_title'])));
    if(isset($_POST['seo_meta_desc'])) update_post_meta($post_id, 'seo_meta_desc', esc_html(stripslashes($_POST['seo_meta_desc'])));
    
    if(isset($_POST['seo_noindex'])) update_post_meta($post_id, 'seo_noindex', $_POST['seo_noindex']);
    else update_post_meta($post_id, 'seo_noindex', "no");
    
    if(isset($_POST['seo_nofollow'])) update_post_meta($post_id, 'seo_nofollow', $_POST['seo_nofollow']);
    else update_post_meta($post_id, 'seo_nofollow', "no");
    
}
add_action( 'save_post', 'woocommerce_seo_save_product_seo_data' );
?>
