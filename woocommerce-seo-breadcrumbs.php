<?php

function woocommerce_seo_enable_breadcrumbs(){
   if(is_product_category()){
        if(get_option('woocommerce_seo_breadcrumbs_enabled') == 'yes'){
            remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
            add_action( 'woocommerce_before_main_content', 'woocommerce_seo_breadcrumbs', 20, 0);
        }
    } 
}

function woocommerce_seo_save_seleceted_filters(){
    global $_chosen_attributes;
    
    if($_chosen_attributes){
        $filters = (array)$_SESSION['filters'];
        //add new chosen attributes to session variable
        foreach($_chosen_attributes as $k => $v){
            foreach($v['terms'] as $term_id){
                $term_info = array(
                    'term_id' => $term_id,
                    'taxonomy' => $k
                );
                if(!in_array($term_info, $filters)){
                    $filters[] = $term_info;
                }
            };
        }
        
        //remove no longer selected filters from session
        foreach($filters as $k => $v){
            if(array_key_exists($v['taxonomy'], $_chosen_attributes)){ //if this attribute is chosen
                if(!in_array($v['term_id'], $_chosen_attributes[$v['taxonomy']]['terms'])){ //check if this term is still chosen
                    unset($filters[$k]); //remove it if its not in the array
                }
            } else { //attribute is not chosen (no terms selected), 
                unset($filters[$k]); //remove this term from filters array
            }
            
        }
        
        $_SESSION['filters'] = $filters;
    }
    else{
        $_SESSION['filters'] = array();
    }
    //var_dump($filters);
}

function woocommerce_seo_breadcrumbs( $args = array() ){
    $defaults = array(
        'delimiter'  => ' &rsaquo; ',
        'wrap_before'  => '<div id="breadcrumb" itemprop="breadcrumb">',
        'wrap_after' => '</div>',
        'before'   => '',
        'after'   => '',
        'home'    => null
    );

    $args = wp_parse_args( $args, $defaults  );
    extract($args);
    global $post, $wp_query;

    if( ! $home ) 
        $home = _x( 'Home', 'breadcrumb', 'woocommerce' );

    $home_link = home_url();

    if ( get_option('woocommerce_prepend_shop_page_to_urls') == "yes" && woocommerce_get_page_id( 'shop' ) && get_option( 'page_on_front' ) !== woocommerce_get_page_id( 'shop' ) )
        $prepend =  $before . '<a href="' . get_permalink( woocommerce_get_page_id('shop') ) . '">' . get_the_title( woocommerce_get_page_id('shop') ) . '</a> ' . $after . $delimiter;
    else
        $prepend = '';

    if ( ( ! is_home() && ! is_front_page() && ! ( is_post_type_archive() && get_option( 'page_on_front' ) == woocommerce_get_page_id( 'shop' ) ) ) || is_paged() ) {

        echo $wrap_before . $before  . '<a class="home" href="' . $home_link . '">' . $home . '</a> '  . $after . $delimiter ;

        if ( is_category() ) {

            $cat_obj = $wp_query->get_queried_object();
            $this_category = get_category( $cat_obj->term_id );

            if ( $this_category->parent != 0 ) {
                $parent_category = get_category( $this_category->parent );
                echo get_category_parents($parent_category, TRUE, $delimiter );
            }

            echo $before . single_cat_title( '', false ) . $after;

        } elseif ( is_tax('product_cat') ) {

            echo $prepend;
            $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

            $parents = array();
            $parent = $term->parent;
            while ( $parent ) {
                $parents[] = $parent;
                $new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
                $parent = $new_parent->parent;
            }

            if ( ! empty( $parents ) ) {
                $parents = array_reverse( $parents );
                foreach ( $parents as $parent ) {
                    $item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
                    echo $before .  '<a href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a>' . $after . $delimiter;
                }
            }

            $queried_object = $wp_query->get_queried_object(); 
            //output filters after category
            if(!empty($_SESSION['filters'])){
                $base_link = get_term_link( $queried_object->slug, 'product_cat' );
                //we have filters, output category breadcrumb as link
                echo $before . '<a href="' . $base_link . '">' .$queried_object->name . '</a>' . $after . $delimiter;
                //sort terms into separate array to create link (need to sort attributes and terms alphabetically)
                $attribute_terms = array();
                $filters = $_SESSION['filters'];
                $i = 1;
                foreach($filters as $k => $v){ 
                    $term_data = get_term_by('id', $v['term_id'], $v['taxonomy']);
                    if( $i == count($filters) ){ //last item, just output, no link needed
                        echo $before . $term_data->name . $after;
                    } else { //no last terms, output term with link
                        $attribute_terms[$term_data->taxonomy][] = $term_data->slug; //add term to link_terms array
                        //var_dump($attribute_terms);
                        ksort($attribute_terms); //sort attributes alphabetically
                        $link_terms = array();
                        foreach($attribute_terms as $terms){ //create link array
                            asort($terms);
                            $link_terms = array_merge($link_terms, $terms); //array with terms in correct order
                        }
                        //create link
                        $trailing_slash = ($base_link[strlen($base_link)-1] == '/') ? true : false ;
                        $link = $base_link;
                        foreach($link_terms as $term){ //create link 
                            $link .= ($trailing_slash) ? $term.'/' : '/'.$term ;
                        }
                        //output term
                        
                        echo $before . '<a href="' . $link . '">' .$term_data->name . '</a>' . $after . $delimiter;
                    }
                    $i++;
                }
  
            } else { //no filters, output category as normal
                echo $before . $queried_object->name . $after;
            }
            
          
        } elseif ( is_tax('product_tag') ) {

            $queried_object = $wp_query->get_queried_object();
            echo $prepend . $before . __('Products tagged &ldquo;', 'woocommerce') . $queried_object->name . '&rdquo;' . $after;

        } elseif ( is_day() ) {

            echo $before . '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $after . $delimiter;
            echo $before . '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $after . $delimiter;
            echo $before . get_the_time('d') . $after;

        } elseif ( is_month() ) {

            echo $before . '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $after . $delimiter;
            echo $before . get_the_time('F') . $after;

        } elseif ( is_year() ) {

            echo $before . get_the_time('Y') . $after;

        } elseif ( is_post_type_archive('product') && get_option('page_on_front') !== woocommerce_get_page_id('shop') ) {

            $_name = woocommerce_get_page_id( 'shop' ) ? get_the_title( woocommerce_get_page_id( 'shop' ) ) : ucwords( get_option( 'woocommerce_shop_slug' ) );

            if ( is_search() ) {

                echo $before . '<a href="' . get_post_type_archive_link('product') . '">' . $_name . '</a>' . $delimiter . __('Search results for &ldquo;', 'woocommerce') . get_search_query() . '&rdquo;' . $after;

            } elseif ( is_paged() ) {

                echo $before . '<a href="' . get_post_type_archive_link('product') . '">' . $_name . '</a>' . $after;

            } else {

                echo $before . $_name . $after;

            }

        } elseif ( is_single() && !is_attachment() ) {

            if ( get_post_type() == 'product' ) {

                echo $prepend;

                if ( $terms = wp_get_object_terms( $post->ID, 'product_cat' ) ) {
                    $term = current( $terms );
                    $parents = array();
                    $parent = $term->parent;

                    while ( $parent ) {
                        $parents[] = $parent;
                        $new_parent = get_term_by( 'id', $parent, 'product_cat' );
                        $parent = $new_parent->parent;
                    }

                    if ( ! empty( $parents ) ) {
                        $parents = array_reverse($parents);
                        foreach ( $parents as $parent ) {
                            $item = get_term_by( 'id', $parent, 'product_cat');
                            echo $before . '<a href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a>' . $after . $delimiter;
                        }
                    }

                    echo $before . '<a href="' . get_term_link( $term->slug, 'product_cat' ) . '">' . $term->name . '</a>' . $after . $delimiter;

                }

                echo $before . get_the_title() . $after;

            } elseif ( get_post_type() != 'post' ) {

                $post_type = get_post_type_object( get_post_type() );
                $slug = $post_type->rewrite;
                echo $before . '<a href="' . get_post_type_archive_link( get_post_type() ) . '">' . $post_type->labels->singular_name . '</a>' . $after . $delimiter;
                echo $before . get_the_title() . $after;

            } else {

                $cat = current( get_the_category() );
                echo get_category_parents( $cat, true, $delimiter );
                echo $before . get_the_title() . $after;

            }

        } elseif ( is_404() ) {

            echo $before . __( 'Error 404', 'woocommerce' ) . $after;

        } elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' ) {

            $post_type = get_post_type_object( get_post_type() );

            if ( $post_type )
                echo $before . $post_type->labels->singular_name . $after;

            } elseif ( is_attachment() ) {

                $parent = get_post( $post->post_parent );
                $cat = get_the_category( $parent->ID ); 
                $cat = $cat[0];
                echo get_category_parents( $cat, true, '' . $delimiter );
                echo $before . '<a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a>' . $after . $delimiter;
                echo $before . get_the_title() . $after;

            } elseif ( is_page() && !$post->post_parent ) {

                echo $before . get_the_title() . $after;

            } elseif ( is_page() && $post->post_parent ) {

                $parent_id  = $post->post_parent;
                $breadcrumbs = array();

                while ( $parent_id ) {
                    $page = get_page( $parent_id );
                    $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title( $page->ID ) . '</a>';
                    $parent_id  = $page->post_parent;
                }

                $breadcrumbs = array_reverse( $breadcrumbs );

                foreach ( $breadcrumbs as $crumb )
                    echo $crumb . '' . $delimiter;

                echo $before . get_the_title() . $after;

            } elseif ( is_search() ) {

                echo $before . __( 'Search results for &ldquo;', 'woocommerce' ) . get_search_query() . '&rdquo;' . $after;

            } elseif ( is_tag() ) {

                echo $before . __( 'Posts tagged &ldquo;', 'woocommerce' ) . single_tag_title('', false) . '&rdquo;' . $after;

            } elseif ( is_author() ) {

                $userdata = get_userdata($author);
                echo $before . __( 'Author:', 'woocommerce' ) . ' ' . $userdata->display_name . $after;

            }

            if ( get_query_var( 'paged' ) )
                echo ' (' . __( 'Page', 'woocommerce' ) . ' ' . get_query_var( 'paged' ) . ')';

        echo $wrap_after;

    }
                
}



?>
