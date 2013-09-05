<?php

function woocommerce_seo_settings(){
    global $woocommerce;
    $attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
    $tags = "";
    foreach( $attribute_taxonomies as $att ){
        $tags .= '[' . sanitize_title($att->attribute_name) . '] , '; 
    }
    
    return
    array(
        array( 'name' => __( 'Woocommerce SEO Settings', 'woocommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'woocommerce_seo_settings' ),

        array(
            'name'      => __( 'Product Category Base', 'woocommerce' ),
            'desc'      => __( 'Remove product category base', 'woocommerce' ),
            'desc_tip'	=>  __('By default this shows product-category/category-name.  Check this to remove the base completely from your URL.', 'woocommerce'),
            'id'        => 'woocommerce_seo_remove_product_category_base',
            'std'       => 'no',
            'type'      => 'checkbox'
        ),
        
        array(
            'name'      => __( 'Woocommerce SEO breadcrumbs', 'woocommerce' ),
            'desc'      => __( 'Enable Woocommerce SEO breadcrumbs', 'woocommerce' ),
            'desc_tip'	=>  __('The Woocommerce SEO breadcrumbs will only show up on product category pages when enabled. All other pages will show the default Woocommerce breadcrumbs.', 'woocommerce'),
            'id'        => 'woocommerce_seo_breadcrumbs_enabled',
            'std'       => 'no',
            'type'      => 'checkbox'
        ),

        array( 'type' => 'sectionend', 'id' => 'woocommerce_seo_settings' ),
        
        array( 'name' => __( 'Global Category Meta Settings', 'woocommerce' ), 'type' => 'title', 'desc' => 'Note: For the following settings to work correctly, you must use the \'Woocommerce SEO Layered Navigation Widget\' instead of the Layered Navigation widget provided with Woocommerce. For more information please refer to the documentation.', 'id' => 'woocommerce_seo_category_meta_settings' ),
        
        array(
            'name'      => __( 'Enabled', 'woocommerce' ),     
            'desc_tip'	=>  __('Other Wordpress SEO plugins may also add this functionality.  Its best to only let a single plugin handle this. Either Disable it here, or disable it within the Wordpress SEO plugin you are using.', 'woocommerce'),
            'id'        => 'woocommerce_seo_category_meta_enabled',
            'std'       => 'no',
            'type'      => 'checkbox'
        ),
        
        array(
            'name'      => __( 'Category meta output', 'woocommerce' ),
            'desc'      => __( '<br />Set whether the Woocommerce SEO Plugin will use the global template below or your custom titles.', 'woocommerce' ),
            'id'        => 'woocommerce_seo_category_meta_global',
            'css'       => 'min-width:100px;',
            'std'       => 'template',
            'type'      => 'select',
            'options'   => array(
                'template'  => __( 'Template', 'woocommerce' ),
                'custom'    => __( 'Custom', 'woocommerce' ),
            ),
            'desc_tip'	=>  false,
	),
        
        array(
            'desc'          => __( 'Don\'t show before text if attribute is first in the template', 'woocommerce' ),
            'id'            => 'woocommerce_seo_category_meta_template_hide_before',
            'std'           => 'yes',
            'checkboxgroup' => 'start',
            'type'          => 'checkbox',
	),
        
        array(
            'desc'          => __( 'Don\'t show after text if attribute is last in the template', 'woocommerce' ),
            'desc_tip'      =>  __('Only works if the category meta output is set to template. See the documentation for a full explanation of what this does and why.', 'woocommerce'),
            'id'            => 'woocommerce_seo_category_meta_template_hide_after',
            'std'           => 'yes',
            'checkboxgroup' => 'end',
            'type'          => 'checkbox',
	),
        
        array(
            'name'      => __( 'Category page title template', 'woocommerce' ),
            'desc'      => __( '<br />The template can be used to show a different page title as the user filters their product search using the Woocommerce SEO Layered Navigation widget.  Use any combination of the tags and any text to make up your page title.<br />
                                <strong>Available tags and their attributes are shown below.</strong>', 'woocommerce' ),
            'id'        => 'woocommerce_seo_category_page_title_global_template',
            'type'      => 'text',
            'css'       => 'min-width:99%;',
            'std'       => '',
            'desc_tip'  =>  false,
	),
        
        array(
		'name'  => __( 'Category meta description template', 'woocommerce' ),
		'desc'  => __( 'The template can be used to show a different meta description as the user filters their product search using the Woocommerce SEO Layered Navigation widget.  
                               Particularly useful when trying to rank the filtered pages in the search engines. <br />Use any combination of the tags and any text to make up your description.', 'woocommerce' ),
		'id'    => 'woocommerce_seo_category_meta_desc_global_template',
		'css'	=> 'min-width:99%; height: 75px;',
		'type'	=> 'textarea',
		'std' 	=> ''
	),
        
        array(
            'name' => '',
            'desc' => '<strong>Available tags:</strong> [category] , ' . $tags .'<br />
                       <strong>Tag attributes: </strong><br /> before=" " - Shown before the attribute is output<br />after=" " - Shown after the attribute is output<br />separator=" " - If multiple attributes are selected. this will be used to as the seperator when outputting. Defaults to | if nothing is provided<br />show_parents="yes" - Will show the parents of the current category too using the \'separator\' attribute to separate the category names.<br />
                       <strong>Example:</strong> [category before="text before" after="text after" show_parents="yes"]',
            'type' => 'output_text'
        ),
        
        array( 'type' => 'sectionend', 'id' => 'woocommerce_seo_category_meta_settings' ),
        
        array( 'name' => __( 'Global Product Meta Settings', 'woocommerce' ), 'type' => 'title', 'desc' => '', 'id' => 'woocommerce_seo_product_meta_settings' ),
        
        array(
            'name'      => __( 'Enabled', 'woocommerce' ),     
            'desc_tip'	=>  __('Other Wordpress SEO plugins may also add this functionality.  Its best to only let a single plugin handle this. Either Disable it here, or disable it within the Wordpress SEO plugin you are using.', 'woocommerce'),
            'id'        => 'woocommerce_seo_product_meta_enabled',
            'std'       => 'no',
            'type'      => 'checkbox'
        ),
        
        array(
            'name'      => __( 'Product meta output', 'woocommerce' ),
            'desc'      => __( '<br />Set whether the Woocommerce SEO Plugin will use the global template below or your custom titles.', 'woocommerce' ),
            'id'        => 'woocommerce_seo_product_meta_global',
            'css'       => 'min-width:100px;',
            'std'       => 'template',
            'type'      => 'select',
            'options'   => array(
                'template'  => __( 'Template', 'woocommerce' ),
                'custom'    => __( 'Custom', 'woocommerce' ),
            ),
            'desc_tip'	=>  false,
	),
        
        array(
            'name'      => __( 'Product page title template', 'woocommerce' ),
            'desc'      => __( '<br />The template can be used to show a custom page title for each product based on the tags without the need to write a custom title for each product. Use any combination of the tags and any text to make up your page title.<br />
                                <strong>Available tags and their attributes are shown below.</strong>', 'woocommerce' ),
            'id'        => 'woocommerce_seo_product_page_title_global_template',
            'type'      => 'text',
            'css'       => 'min-width:99%;',
            'std'       => '',
            'desc_tip'  =>  false,
	),
        
        array(
		'name'  => __( 'Product meta description template', 'woocommerce' ),
		'desc'  => __( 'The template can be used to show a different meta description for each product. Use any combination of the tags and any text to make up your description.', 'woocommerce' ),
		'id'    => 'woocommerce_seo_product_meta_desc_global_template',
		'css'	=> 'min-width:99%; height: 75px;',
		'type'	=> 'textarea',
		'std' 	=> ''
	),
        
        array(
            'desc' => '<strong>Available tags:</strong> [name] , [sku] , [price] , [excerpt] , [blogname]<br />
                       <strong>Tag attributes:</strong><br/ >before=" " - Shown before the value is output<br />after=" " - Shown after the value is output<br />
                       <strong>Example:</strong> [name before="Product:" after=" - "] [sku before="SKU:"]',
            'type' => 'output_text'
        ),
        
        array( 'type' => 'sectionend', 'id' => 'woocommerce_seo_product_meta_settings' ),
    );
}

function woocommerce_seo_output_text($value){
    ?>
    <tr valign="top">
        <th scope="row" class="titledesc"></th>
        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <span class="description"><?php echo $value['desc'] ?></span>
        </td>
    </tr>
    <?php
    
}
add_action( 'woocommerce_admin_field_output_text', 'woocommerce_seo_output_text' );
?>
