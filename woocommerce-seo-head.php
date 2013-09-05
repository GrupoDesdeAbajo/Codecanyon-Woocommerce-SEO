<?php

/**
 * Prepare category page title/meta desc template and add relevant shortcodes for parsing.
 * http://www.96down.com/codecanyon-woocommerce-seo/
 * @global type $_attributes_array Woocommerce attributes array
 * @param type $template The cateeogry template
 * @return string The page title/meta desc with tags replaces
 */
function woocommerce_seo_parse_category_template( $template ){
    global $_attributes_array;
    
    //add only attribute shortcodes first (for detemining first/last tag in template)
    foreach($_attributes_array as $attribute){
        add_shortcode(str_replace('pa_', "", $attribute), 'woocommerce_seo_replace_category_tags');
    }
    
    $hide_before = get_option('woocommerce_seo_category_meta_template_hide_before');
    $hide_after = get_option('woocommerce_seo_category_meta_template_hide_after');
    
    if($hide_before == 'yes' || $hide_after = 'yes'){ //remove before/after from first/last tag
        //find the first and last tag
        preg_match_all("#".get_shortcode_regex()."#", $template, $matches); //get the matched shortcodes

        if(!empty($matches[0])){
            if($hide_before == 'yes'){
                $first_tag = $matches[0][0];
                $first_tag = str_replace(']', ' remove_before="true"]', $first_tag); //add remove_before attribute
                $template = str_replace($matches[0][0], $first_tag, $template); //replace first tag
            }
            
            if($hide_after == 'yes'){
                $last_tag = $matches[0][count($matches[0])-1];
                $last_tag = str_replace(']', ' remove_after="true"]', $last_tag); //add remove_after attribute
                $template = str_replace($matches[0][count($matches[0])-1], $last_tag, $template); //replace last tag
            }
        }
    } 
    //add rest of the shortcodes
    add_shortcode('category', 'woocommerce_seo_replace_category_tags');
    
    //run the shortcode replace
    $page_title = do_shortcode($template);
    
    //remove shortcodes so they dont conflict with anything else
    foreach($_attributes_array as $attribute){
        remove_shortcode(str_replace('pa_', "", $attribute));
    }
    remove_shortcode('category');   
    
    $page_title = preg_replace( "/ {2,}/", " ", $page_title ); //remove extra white spaces
    
    return $page_title;
    
}

/**
 * Shortcode function to replace shortcode with actual values
 * 1.1 Added show_parents
 * 
 * @global array $_chosen_attributes Woocommerce attributes array
 * @param type $atts shortcodes attributes
 * @param type $content Some content
 * @param type $tag The shortcode tag
 * @return string The value to replace the shortcode with
 */
function woocommerce_seo_replace_category_tags( $atts, $content = "", $tag ){
    
    extract( shortcode_atts( array( //defaults, incase an attribute isnt provided
        'before' => '',
        'after' => '',
        'separator' => " | ",
        'remove_before' => false,
        'remove_after' => false,
        'show_parents' => 'no'
    ), $atts ) );

    global $_chosen_attributes;

    $output = "";
    
    switch( $tag ){
        case 'category': //category shortcode, dont need separator
            $category = get_term_by( 'slug', get_query_var( 'product_cat' ), 'product_cat' );
            if($show_parents == 'yes'){
                $output = $category->name;
                while($category->parent != '0'){
                    $category = get_term_by( 'id', $category->parent, 'product_cat' );
                    $output = $category->name.$separator.$output;
                }
                $output = $before.$output.$after;
            } else $output = $before.$category->name.$after;
            break;

        default:
            if( array_key_exists( 'pa_'.$tag, $_chosen_attributes ) ){ //attribute is selected, create output

                //get the term names
                $terms = $_chosen_attributes['pa_'.$tag]['terms'];
                foreach( $terms as $k => $v ){
                    $term_data = get_term_by( 'id', $v, 'pa_'.$tag );
                    $terms[$k] = $term_data->name;
                }

                if( false != $remove_before ) $before = "";
                if( false != $remove_after ) $after = "";

                $output = $before.implode($separator, $terms).$after;
            }
    }

   return $output;
}

function woocommerce_seo_parse_product_template( $template ){
    //add shortcodes for prodict template
    add_shortcode( 'name', 'woocommerce_seo_replace_product_tags' );
    add_shortcode( 'sku', 'woocommerce_seo_replace_product_tags' );
    add_shortcode( 'price', 'woocommerce_seo_replace_product_tags' );
    add_shortcode( 'excerpt', 'woocommerce_seo_replace_product_tags' );
    add_shortcode( 'blogname', 'woocommerce_seo_replace_product_tags' );
    
    //run the shortcode replace
    $page_title = do_shortcode($template);
    
    //remove shortcodes so they dont conflict with anything else
    remove_shortcode('name');
    remove_shortcode('sku');
    remove_shortcode('price');
    remove_shortcode('excerpt');
    remove_shortcode('blogname');
    
    $page_title = preg_replace( "/ {2,}/", " ", $page_title ); //remove extra white spaces
    
    return $page_title;
}

function woocommerce_seo_replace_product_tags( $atts, $content = "", $tag ){
    extract( shortcode_atts( array( //defaults, incase an attribute isnt provided
        'before' => '',
        'after' => '',
    ), $atts ) );
    
    global $post;
    
    $output = "";
    
    switch( $tag ){
        case 'name': //product name
            $output = $before.$post->post_title.$after;
            break;
        case 'sku': //product sku
            $sku = get_post_meta($post->ID, '_sku', true);
            $output = $before.$sku.$after;
            break;
        case 'price':
            $price = get_post_meta($post->ID, '_price', true);
            $output = $before.$price.$after;
            break;
        case 'excerpt':
            $output = $before.$post->post_excerpt.$after;
            break;
        case 'blogname':
            $output = $before.get_option('blogname').$after;
            break;
        
        default:
            $output = "";
    }
    
    return $output;
}




/**
 * Returns the page title after being formatted with seperator
 *
 * @param string $title The page title to format
 * @param string $sep The seperator character
 * @param string $seplocation Location of separator
 * @return string The page title after formatting
 */
function woocommerce_seo_format_page_title( $title, $sep, $seplocation ){
    if( !is_null( $sep ) && !is_null( $seplocation ) ){
        $sep = " " . $sep . " ";
        if( $seplocation == "right" ){
            $title = $title.$sep;
        }
        else{
            $title = $sep.$title;
        }
    }
    return $title;
}
    
/**
 * Replaces the current page title
 *
 * @global $post The current post
 * @param $title the current page title
 */
function woocommerce_seo_filter_page_title( $page_title, $sep = null, $seplocation = null ){
    if( is_product_category() ){ 
        if( get_option( 'woocommerce_seo_category_meta_enabled' ) == 'yes' ){ //global category meta enabled?
            //get category id
            $category = get_term_by( 'slug', get_query_var( 'product_cat' ), 'product_cat' ); 

            $cat_option = get_woocommerce_term_meta( $category->term_id, 'woocommerce_seo_option', true ); //category meta enabled for this category? 
            $cat_option = ( $cat_option ) ? $cat_option : 'global'; //default to 'global' if no entry found.
            
            if($cat_option != 'disabled'){

                if( $cat_option == 'global' ) $cat_option = get_option( 'woocommerce_seo_category_meta_global' ); //get global option if cat option set to global

                if( $cat_option == 'template' ) $template = get_option( 'woocommerce_seo_category_page_title_global_template' ); //get the global template
                else $template = get_woocommerce_term_meta( $category->term_id, 'woocommerce_seo_page_title', true ); //custom, get the category page title/template


                //run the template through the parser to replace tags with their actual values
                $page_title = woocommerce_seo_parse_category_template(wp_kses_stripslashes($template));
                //format the page title
                $page_title = woocommerce_seo_format_page_title( $page_title, $sep, $seplocation );
            }
            
        } 
    } elseif( is_product() ){
        if( get_option('woocommerce_seo_product_meta_enabled') == "yes" ){
            
            global $post;

            $product_option = get_post_meta($post->ID, 'seo_option', true); //var_dump($product_option);
            $product_option = ( $product_option ) ? $product_option : 'global'; //default to 'global' if no entry found.
            //var_dump($product_option);
            
            if($product_option != 'disabled'){

                if($product_option == 'global') $product_option = get_option('woocommerce_seo_product_meta_global'); //get gloabl option if product option set to global
                //var_dump($product_option);

                if( $product_option == 'template' ) $template = get_option( 'woocommerce_seo_product_page_title_global_template' ); //get the global template
                else $template = get_post_meta( $post->ID, 'seo_page_title', true ); //custom, get the product page title/template
                //var_dump($template);

                $page_title = woocommerce_seo_parse_product_template( wp_kses_stripslashes($template) );

                $page_title = woocommerce_seo_format_page_title( $page_title, $sep, $seplocation );
            
            }
            
        }
    }

    return $page_title; 
}

/**
 * Output meta description stuff into HTML head 
 */
function woocommerce_seo_output_head(){
    $output = '';
    
    if( is_product_category() ){ 
        //get category id
        $category = get_term_by( 'slug', get_query_var( 'product_cat' ), 'product_cat' ); 

        $cat_option = get_woocommerce_term_meta( $category->term_id, 'woocommerce_seo_option', true ); //category meta enabled for this category? 
        $cat_option = ( $cat_option ) ? $cat_option : 'global'; //default to 'global' if no entry found.
        
        if($cat_option != 'disabled'){

            if( $cat_option == 'global' ) $cat_option = get_option( 'woocommerce_seo_category_meta_global' ); //get global option if cat option set to global

            if( $cat_option == 'template' ) $template = get_option( 'woocommerce_seo_category_meta_desc_global_template' ); //get the global template
            else $template = get_woocommerce_term_meta( $category->term_id, 'woocommerce_seo_meta_desc', true ); //custom, get the category page title/template

            //run the template through the parser to replace tags with their actual values
            $meta_desc = woocommerce_seo_parse_category_template( wp_kses_stripslashes($template) );
            if($meta_desc) $output .= "<meta name=\"description\" content=\"$meta_desc\" />\n";

            //Noindex/Nofollow
            $noindex = get_woocommerce_term_meta( $category->term_id, 'woocommerce_seo_no_index', true);
            $nofollow = get_woocommerce_term_meta(  $category->term_id, 'woocommerce_seo_no_follow', true);
            if($noindex == "true" || $nofollow == "true"){
                $noindex_value = ($noindex == "true") ? "noindex" : "index";
                $nofollow_value = ($nofollow == "true") ? "nofollow" : "follow" ;
                $output .= '<meta name="robots" content="'.$noindex_value.','.$nofollow_value.'" />';
                $output .= "\n";
            }
            
        }
    } elseif( is_product() ){
        global $post;
        
        $product_option = get_post_meta($post->ID, 'seo_option', true);
        $product_option = ( $product_option ) ? $product_option : 'global'; //default to 'global' if no entry found.
        
        if($product_option != 'disabled'){
        
            if($product_option == 'global') $product_option = get_option('woocommerce_seo_product_meta_global'); //get gloabl option if product option set to global
            //var_dump($product_option);

            if( $product_option == 'template' ) $template = get_option( 'woocommerce_seo_product_meta_desc_global_template' ); //get the global template
            else $template = get_post_meta( $post->ID, 'seo_meta_desc', true ); //custom, get the product page title/template

            $meta_desc = woocommerce_seo_parse_product_template( wp_kses_stripslashes($template) );

            if($meta_desc)  $output .= "<meta name=\"description\" content=\"$meta_desc\" />\n";

            //Noindex/Nofollow
            $noindex = get_post_meta( $post->ID, 'seo_noindex', true);
            $nofollow = get_post_meta(  $post->ID, 'seo_nofollow', true);
            if($noindex == "yes" || $nofollow == "yes"){
                $noindex_value = ($noindex == "yes") ? "noindex" : "index";
                $nofollow_value = ($nofollow == "yes") ? "nofollow" : "follow" ;
                $output .= '<meta name="robots" content="'.$noindex_value.','.$nofollow_value.'" />';
                $output .= "\n";
            }
        }
    }
    
    if($output){
        echo "\n\n<!-- Woocommerce SEO Plugin -->\n".$output."<!-- Woocommerce SEO Plugin -->\n\n";
    }
}
    

?>
