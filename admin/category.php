<?php

/**
 * Adds the relevant fields in the category edit page
 * 
 * @param object $tag The category object
 */
function woocommerce_seo_add_category_seo_data_fields( $tag ){
    
    $category_option = get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_option', true );
    if( !$category_option ) $category_option = 'global';
    $category_noindex = (get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_no_index', true ) == "true") ? "checked" : '';
    $category_nofollow = (get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_no_follow', true ) == "true") ? "checked" : '';
    
    global $woocommerce;
    $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
    $tags = "";
    foreach( $attribute_taxonomies as $att ){
        $tags .= '[' . sanitize_title($att->attribute_name) . '] , '; 
    }
    ?>

    <tr><td colspan="2"><h3>Woocommerce SEO Settings</h3></td></tr>
    <tr>
        <th scope="row" valign="top"><label for="woocommerce_seo_option">Enabled/Option?</label></th>
        <td>
            <?php $options = array( 
                                'disabled' => 'Disabled', 
                                'global' => 'Global Setting',
                                'template' => 'Global Template',
                                'custom' => 'Custom' ); ?>
            <select id="woocommerce_seo_option" name="woocommerce_seo_option">
                <?php
                foreach( $options as $k => $v){
                    $selected = '';
                    if( $k == $category_option ) $selected='selected="selected" ';
                    echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="woocommerce_seo_page_title">Page title</label></th>
        <td>
            <!--<input type="text" name="woocommerce_seo_page_title" id="woocommerce_seo_page_title" value="<?php echo get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_page_title', true ); ?>" /><br />-->
            <textarea name="woocommerce_seo_page_title" id="woocommerce_seo_page_title" cols="50" rows="1"><?php echo get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_page_title', true ); ?></textarea>
            <p class="description">The page title to show when viewing this category. Use any combination of the tags and any text to make up your page title. <br /><strong>Available tags and their attributes are shown below.</strong></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="woocommerce_seo_meta_desc">Meta Description</label></th>
        <td>
            <textarea name="woocommerce_seo_meta_desc" id="woocommerce_seo_meta_desc" cols="50" rows="5"><?php echo get_woocommerce_term_meta( $tag->term_id, 'woocommerce_seo_meta_desc', true ); ?></textarea>
            <p class="description">
                The meta descripiton to show when viewing this category. Use any combination of the tags and any text to make up your description.<br />
                <strong>Available tags: </strong><?php echo $tags; ?><br />
                <strong>Tag attributes: </strong><br />before=" " - Shown before the attribute is output<br />after=" " - Shown after the attribute is output<br />
                separator=" " - If multiple attributes are selected, this will be used as the seperator when outputting. Defaults to " | " if nothing is provided<br />
                <strong>Example: </strong> [category before="text before" after="text after" separator=" | "]
            </p>
        </td>
    </tr>
    <tr class="form-field">
        <th abbr="" scope="row" valign="top"><label for="woocommerce_seo_no_index">Noindex this cateogry</label></th>
        <td>
            <input name="woocommerce_seo_no_index" id="woocommerce_seo_no_index" type="checkbox" <?php echo $category_noindex; ?> style="width:10px;" />
        </td>
    </tr>
    <tr class="form-field">
        <th abbr="" scope="row" valign="top"><label for="woocommerce_seo_no_follow">Nofollow this cateogry</label></th>
        <td>
            <input name="woocommerce_seo_no_follow" id="woocommerce_seo_no_follow" type="checkbox" <?php echo $category_nofollow; ?> style="width:10px;" />
        </td>
    </tr>

    <?php
    
}
add_action( 'product_cat_edit_form_fields', 'woocommerce_seo_add_category_seo_data_fields', 99 );

/**
 * Save the data held in the SEO fields added from the add_meta_data_fields function.
 * 
 * @param int $term_id Id of the term(category) to save against
 */
function woocommerce_seo_save_category_seo_data( $term_id ){
    if( !$term_id ) return;
       
    //enabled/option
    update_woocommerce_term_meta( $term_id, 'woocommerce_seo_option', $_POST['woocommerce_seo_option'] );
    //page title
    update_woocommerce_term_meta( $term_id, 'woocommerce_seo_page_title', $_POST['woocommerce_seo_page_title']);
    //meta desc
    update_woocommerce_term_meta( $term_id, 'woocommerce_seo_meta_desc', $_POST['woocommerce_seo_meta_desc']);
    //no index
    if(isset($_POST['woocommerce_seo_no_index'])) update_woocommerce_term_meta( $term_id, 'woocommerce_seo_no_index', "true");
    else update_woocommerce_term_meta( $term_id, 'woocommerce_seo_no_index', "false");
    //no follow
    if(isset($_POST['woocommerce_seo_no_follow'])) update_woocommerce_term_meta( $term_id, 'woocommerce_seo_no_follow', "true");
    else update_woocommerce_term_meta( $term_id, 'woocommerce_seo_no_follow', "false");
    
    
}
add_action( 'edited_product_cat', 'woocommerce_seo_save_category_seo_data' );

?>
