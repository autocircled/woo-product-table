<?php

global $shortCodeText;
add_shortcode( $shortCodeText, 'wpt_shortcode_generator' );

/**
 * Shortcode Generator for WPT Plugin
 * 
 * @param array $atts
 * @return string
 * 
 * @since 1.0
 */
function wpt_shortcode_generator( $atts = false ) {
    //Getting WooProductTable Pro
    $config_value = get_option( 'wpt_configure_options' );
    $html = '';
    $GLOBALS['wpt_product_table'] = "Yes";
    /**
     * Set Variable $html to return
     * 
     * @since 1.1
     */
    
    $pairs = array( 'exclude' => false );
    extract( shortcode_atts( $pairs, $atts ) );
    
    if( isset( $atts['id'] ) && !empty( $atts['id'] ) && is_numeric( $atts['id'] ) && get_post_type( (int) $atts['id'] ) == 'wpt_product_table' ){
        $ID = $table_ID = (int) $atts['id']; //Table ID added at V5.0. And as this part is already encapsule with if and return is false, so no need previous declearation
        $GLOBALS['wpt_product_table'] = $ID;

        //Used meta_key column_array, enabled_column_array, basics, conditions, mobile, search_n_filter, 
        $column_array = get_post_meta( $ID, 'column_array', true );
        $enabled_column_array = get_post_meta( $ID, 'enabled_column_array', true );
        

        if( !isset( $enabled_column_array['product_title'] ) ){
            $temp_product_title['product_title'] = $column_array['product_title'];
            $enabled_column_array = array_merge($temp_product_title,$enabled_column_array);
        }
        unset($enabled_column_array['description']); //Description column has been removed V5.2
        
        $column_settings = get_post_meta( $ID, 'column_settings', true);
        var_dump($column_settings);
        $basics = get_post_meta( $ID, 'basics', true );
        $table_style = get_post_meta( $ID, 'table_style', true );
        $conditions = get_post_meta( $ID, 'conditions', true );
        $mobile = get_post_meta( $ID, 'mobile', true );
        $search_n_filter = get_post_meta( $ID, 'search_n_filter', true );
        $pagination = get_post_meta( $ID, 'pagination', true );
        $config_value = wpt_get_config_value( $table_ID ); //Added at V5.0
        array_unshift( $config_value, get_the_title( $ID ) ); //Added at V5.0
        
        /**
         * Product Type featue added for provide Variation Product table 
         * 
         * @since 5.7.7
         */
        $product_type = isset( $basics['product_type'] ) && !empty( $basics['product_type'] ) ? $basics['product_type'] : false;
        if( $product_type ){
            unset( $enabled_column_array['category'] );
            unset( $enabled_column_array['tags'] );
            unset( $enabled_column_array['weight'] );
            unset( $enabled_column_array['length'] );
            unset( $enabled_column_array['width'] );
            unset( $enabled_column_array['height'] );
            unset( $enabled_column_array['rating'] );
            unset( $enabled_column_array['attribute'] );
            unset( $enabled_column_array['variations'] );
        }

        
        //For Advance and normal Version
        $table_type = isset( $conditions['table_type'] ) ? $conditions['table_type'] : 'normal_table';//"advance_table"; //table_type
        if($table_type != 'normal_table'){
            //unset( $enabled_column_array['price'] );
            unset( $enabled_column_array['variations'] );
            unset( $enabled_column_array['total'] );
            unset( $enabled_column_array['quantity'] );
        }
        //Collumn Setting part
        $table_head = !isset( $column_settings['table_head'] ) ? true : false; //Table head availabe or not
        
        
        $table_column_keywords = array_keys( $enabled_column_array );
        var_dump($enabled_column_array,$table_column_keywords);
        
        $description_on = isset( $column_settings['description_off'] ) && $column_settings['description_off'] ? 'no' : 'yes';
        $title_variation = isset( $column_settings['title_variation'] ) && $column_settings['title_variation'] ? $column_settings['title_variation'] : 'link';
        $thumb_variation = isset( $column_settings['thumb_variation'] ) && $column_settings['thumb_variation'] ? $column_settings['thumb_variation'] : 'popup';
        
        
        //Basics Part
        $product_cat_id_single = ( isset($atts['product_cat_ids']) && !empty( $atts['product_cat_ids'] ) ? $atts['product_cat_ids'] : false );
        $product_cat_ids = isset( $basics['product_cat_ids'] ) ? $basics['product_cat_ids'] : $product_cat_id_single;
        $post_include = wpt_explode_string_to_array($basics['post_include']);
        $post_exclude = wpt_explode_string_to_array($basics['post_exclude']);
        $cat_explude = isset( $basics['cat_explude'] ) ? $basics['cat_explude'] : false;
        $product_tag_ids = isset( $basics['product_tag_ids'] ) ? $basics['product_tag_ids'] : false;
        $ajax_action = $basics['ajax_action'];//isset( $basics['ajax_action'] ) ? $basics['ajax_action'] : false;
        $pagination_ajax = isset( $basics['pagination_ajax'] ) ? $basics['pagination_ajax'] : 'pagination_ajax';
        $minicart_position = $basics['minicart_position'];//isset( $basics['ajax_action'] ) ? $basics['ajax_action'] : false;
        $table_class = $basics['table_class'];//isset( $basics['ajax_action'] ) ? $basics['ajax_action'] : false;
        $temp_number = $basics['temp_number'];// + $ID; //$ID has removed from temp_number
        $add_to_cart_text = $basics['add_to_cart_text'];
        

        
        $add_to_cart_selected_text = $basics['add_to_cart_selected_text'];
        $check_uncheck_text = $basics['check_uncheck_text'];
        $author = !empty( $basics['author'] ) ? $basics['author'] : false;
        $author_name = !empty( $basics['author_name'] ) ? $basics['author_name'] : false;
        
        //Auto checkbox checked for on load in basic tab - This will generate calss only
        $checkbox = isset( $basics['checkbox'] ) && !empty( $basics['checkbox'] ) ? $basics['checkbox'] : 'wpt_no_checked_table';
        //Design Tab part and generat CSS in html as <style> tag
        $template = isset( $table_style['template'] ) ? $table_style['template'] : 'custom'; //Default value for old version is 'default'
        $custom_css_code = false;
        $custom_table = 'no_custom_style';
        if( is_array($table_style) && $template != 'none' ){
            $custom_table = 'custom';
            $custom_style = $table_style;
            unset($custom_style['template']);
            $custom_css_code .= '<style>';
            foreach($custom_style as $selector=>$properties){
                $selector = str_replace('{', '[', $selector); //third bracket is not supported in array key
                $selector = str_replace('}', ']', $selector);  //third bracket is not supported in array key
                $selector = str_replace('%', '+', $selector);  //third bracket is not supported in array key
                $full_selector = '#table_id_'.$temp_number . ' ' . $selector .'{';
                $full_selector = str_replace( ',', ',#table_id_'.$temp_number . ' ', $full_selector );
                $custom_css_code .= $full_selector;
                foreach( $properties as $property=>$value ){
                    $custom_css_code .= !empty( $value ) ? $property . ': ' . $value . ' !important;' : '';
                }
                $custom_css_code .= '} ';
            }
            $custom_css_code .= '</style>';
        }
        
        //Conditions Tab Part
        $sort = $conditions['sort'];
        $sort_order_by = $conditions['sort_order_by'];
        $meta_value_sort = $conditions['meta_value_sort'];
        $min_price = $conditions['min_price'];
        $max_price = $conditions['max_price'];
        $description_type = $conditions['description_type'];
        $only_stock = $conditions['only_stock'] == 'yes' ? true : false;
        $only_sale = isset( $conditions['only_sale'] ) && $conditions['only_sale'] == 'yes' ? true : false;
        $posts_per_page = (int) $conditions['posts_per_page'];
        
        
        
        //Mobile tab part
        $mobile_responsive = $mobile['mobile_responsive'];
        $table_mobileHide_keywords = isset( $mobile['disable'] ) ? $mobile['disable'] : false;
        
        //Search and Filter
        $search_box = $search_n_filter['search_box'] == 'no' ? false : true;
        $texonomiy_keywords = wpt_explode_string_to_array( $search_n_filter['taxonomy_keywords'] ); 
        
        $filter_box = $search_n_filter['filter_box'] == 'no' ? false : true;
        $filter_keywords = wpt_explode_string_to_array( $search_n_filter['filter'] );
        
        //Pagination Start
        $pagination_start = isset( $pagination['start'] ) ? $pagination['start'] : '1'; //1 FOR ENABLE, AND 0 FOR DISABLE //Default value 1 - Enable
        
    }else{
        return false;
    }
    /***************This will be out of If condition of ID's************************/ 

    $taxonomy_column_keywords = array_filter( $table_column_keywords,'wpt_taxonomy_column_generator' );
    $customfileds_column_keywords = array_filter( $table_column_keywords,'wpt_customfileds_column_generator' );

    /**
     * Define permitted TD inside of Table, Not for Table head
     * Only for Table Body
     * Generate Array by wpt_define_permitted_td_array() which is available in functions.php file of Plugin
     * @since 1.0.4
     */
    $wpt_permitted_td = wpt_define_permitted_td_array( $table_column_keywords );

    
    /**
     * Args for wp_query()
     */
    $args = array(
        'posts_per_page' => $posts_per_page,
        'post_type' => array('product'), //, 'product_variation','product'
        'post_status'   =>  'publish',
        'meta_query' => array(),
        'wpt_query_type' => 'default',
    );
    
    /**
     * Issue of Query for Load More Button
     */
    if( isset( $_GET['s'] ) && !empty( $_GET['s'] ) ){
        $args['s'] = $_GET['s'];
    }else{
        unset( $args['s'] );
    }
    //Final Sku Start
    if($meta_value_sort && ( $sort_order_by == 'meta_value' || $sort_order_by == 'meta_value_num' ) ){
        $args['meta_query'][] = array(
                'key'     => $meta_value_sort, //Default value is _sku : '_sku'
                'compare' => 'EXISTS',
            );
    }
    //Final Sku end
    //Author or Vendor with Condition added 3.4
    if( $author ){
        $args['author'] = $author;
    }
    if( $author_name ){
        $args['author_name'] = $author_name;
    }
    //Author info with Condition added 3.4  - End Here

    if($only_stock){
        $args['meta_query'][] = array(//For Available product online
                'key' => '_stock_status',
                'value' => 'instock'
            );
    }
    /**
     * Modernize Shorting Option
     * Actually Default Value  will be RANDOM, So If not set ASC or DESC, Than Sorting 
     * will be Random by default. Although Just after WP_Query
     * 
     * @since 1.0.0 -9
     */
    if ($sort) {
        $args['orderby'] = $sort_order_by;//'post_title';
        $args['order'] = $sort;
    }


    /**
     * Set Minimum Price for
     */
    if ($min_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $min_price,
            'compare' => '>=',
            'type' => 'NUMERIC'
        );
    }

    /**
     * Set Maximum Price for
     */
    if ($max_price) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => $max_price,
            'compare' => '<=',
            'type' => 'NUMERIC'
        );
    }
    
    /**
     * Args Set for tax_query if available $product_cat_ids
     * 
     * @since 1.0
     */
    if ($product_cat_ids) {
        $args['tax_query']['product_cat_IN'] = array(  //product_cat_IN Added at 5.7 for javascript help work
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $product_cat_ids,
                'operator' => 'IN'
            );

    }
    
    /**
     * Args Set for tax_query if available $product_tag_ids
     * 
     * @since 1.9
     */
    if ($product_tag_ids) {
        $args['tax_query']['product_tag_IN'] = array( //product_tag_IN Added at 5.7 for javascript help work
                'taxonomy' => 'product_tag',
                'field' => 'id',
                'terms' => $product_tag_ids,
                'operator' => 'IN'
            );

    }
    $args['tax_query']['relation'] = 'AND';

    /**
     * Category Excluding System
     * 
     * @since 1.0.4
     * @date 27/04/2018
     */
    if($cat_explude){
        $args['tax_query'][] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $cat_explude,
                'operator' => 'NOT IN'
            );
    }
    
    /**
     * Post Include
     * 
     * @since 4.9
     * @date 22/06/2019
     */
    if($post_include){
        $args['post__in'] = $post_include;//
        $args['orderby'] = 'post__in';
    }
    
    //For Only Stock Product and Added at Version 6.0.6 at 15.6.2020
    if($only_sale){
        $sale_products = wc_get_product_ids_on_sale();
        $sale_products = $sale_products && is_array( $sale_products ) && $post_include && is_array( $post_include ) ? array_intersect( $post_include, $sale_products ) : $sale_products;
        $args['post__in'] = $sale_products;//var_dump(wc_get_product_ids_on_sale());
    }
    
    /**
     * Post Exlucde
     * 
     * @since 1.0.4
     * @date 28/04/2018
     */
    if($post_exclude){
        $args['post__not_in'] = $post_exclude;
    }
    
    //Table ID added to Args 
    $args['table_ID'] = $table_ID; //Added at V5.0
    
    /******************************************************************************/
    
    
    if( $product_type ){
        $product_loop = new WP_Query($args);
        $product_includes = array();
        if ($product_loop->have_posts()) : while ($product_loop->have_posts()): $product_loop->the_post();
            //$_ID = $product_loop->get_the_ID();

            global $product;

            $data = $product->get_data();
            (Int) $_ID = $data['id']; 

            if( wc_get_product($_ID)->get_type() == 'variable'){
                $this_post_variable = new WC_Product_Variable( $_ID );
                $available_post_includes = $this_post_variable->get_children();

                 if( isset( $available_post_includes ) && is_array($available_post_includes) && count( $available_post_includes ) > 0 ){
                    foreach ($available_post_includes as $pperItem){
                        $product_includes[$pperItem] = $pperItem;
                    }
                }


            }
        endwhile;
            //Moved reset query from here to end of table at version 4.3
        else:
            $product_includes = array();
        endif;

        wp_reset_query(); 
        //Unset some default here 
        unset($args['tax_query']);
        unset($args['tax_query']);
        unset($wpt_permitted_td['attribute']);
        unset($wpt_permitted_td['category']);
        unset($wpt_permitted_td['tags']);


        $args['post_type'] = array('product_variation','product');
        $args['post__in'] = $product_includes;
        $args['orderby'] = 'post__in';

        //Set few default value for product variation
        $search_box = false;

    }
    /******************************************************************************/
    
    
    
    /**
     * Initialize Page Number
     */
    $page_number = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
    $args['paged'] =( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : $page_number;
    $html .= '<br class="wpt_clear">';
    /**
     * Add to cart Check Select /check/un-check Section
     * 
     * @version V1.0.4 
     * @date 2/5/2018
     */
    $html_check = $html_check_footer = false; $filter_identy_class = 'fullter_full';
    if( isset( $wpt_permitted_td['check'] ) ){
        $filter_identy_class = 'fulter_half';
        //
        $add_to_cart_selected_text = $add_to_cart_selected_text;//'Add to Cart [Selected]';
        
        $html_check .= "<div class='all_check_header_footer all_check_header check_header_{$temp_number}'>";
        $html_check_footer .= "<div class='all_check_header_footer all_check_footer check_footer_{$temp_number}'>";
        
        $html_check .= "<span><input data-type='universal_checkbox' data-temp_number='{$temp_number}' class='wpt_check_universal wpt_check_universal_header' id='wpt_check_uncheck_button_{$temp_number}' type='checkbox'><label for='wpt_check_uncheck_button_{$temp_number}'>{$check_uncheck_text}</lable></span>";
        
        $html_check .= "<a data-add_to_cart='{$add_to_cart_text}' data-temp_number='{$temp_number}' class='button add_to_cart_all_selected add2c_selected'>$add_to_cart_selected_text</a>";
        $html_check_footer .= "<a data-add_to_cart='{$add_to_cart_text}' data-temp_number='{$temp_number}' class='button add_to_cart_all_selected add2c_selected'>$add_to_cart_selected_text</a>";
        
        $html_check .= "</div>";
        $html_check_footer .= "</div>";
    }
    
    /**
     * Maintenance Filter
     * Mainly Mini Filter
     */
    $filter_html = false;
    if( $filter_box ){
        $filter_html .= "<div class='wpt_filter {$filter_identy_class}'>";
        $filter_html .= "<div class='wpt_filter_wrapper'>";
        $filter_html .= wpt_filter_box($temp_number, $filter_keywords);
        $filter_html .= "</div>";
        $filter_html .= "</div>"; //End of ./wpt_filter
    }
    /**
     * Tables Minicart Message div tag
     * By this feature, we able to display minicart at top or bottom of Table
     * 
     * @since 1.9
     */
    $table_minicart_message_box = "<div class='tables_cart_message_box tables_cart_message_box_{$temp_number}' data-type='load'></div>";

    $html .= apply_filters('wpt_before_table_wrapper', ''); //Apply Filter Just Before Table Wrapper div tag
    
    $wrapper_class_arr = array(
            $table_type . "_wrapper",
            " wpt_temporary_wrapper_" . $temp_number,
            "wpt_product_table_wrapper",
            $template . "_wrapper woocommerce",
            $checkbox,
            "wpt_" . $pagination_ajax,
        );
    $wrapper_class_arr = implode( " ", $wrapper_class_arr );
    
    $html .= "<div "
            . "data-checkout_url='" . esc_attr( wc_get_checkout_url() ) . "' "
            . "data-temp_number='" . esc_attr( $temp_number ) . "' "
            . "data-add_to_cart='" . esc_attr( $add_to_cart_text ) . "' "
            . "data-add_to_cart='" . esc_attr( $add_to_cart_text ) . "' "
            . "data-site_url='" . site_url() . "' "
            . "id='table_id_" . esc_attr( $temp_number ) . "' "
            . "class='" . esc_attr( $wrapper_class_arr ) . "' "
            . ">"; //Table Wrapper Div start here with class. //Added woocommerce class at wrapper div in V1.0.4
    
    $html .= ($minicart_position == 'top' ? $table_minicart_message_box : false);//$minicart_position //"<div class='tables_cart_message_box_{$temp_number}'></div>";
    
    //Search Box Hander Here
    if( $search_box ){
        /**
         * Search Box Added here, Just before of Table 
         * 
         * @since 1.9
         * @date 9.6.2018 d.m.y
         */
        $html .= wpt_search_box( $temp_number, $texonomiy_keywords, $sort_order_by, $sort, $search_n_filter,$table_ID );
    }
    $html .= apply_filters('end_part_advance_search_box_abc','',$table_ID,$temp_number);
    /**
     * Instant Sarch Box
     */
    $instance_search = false;
    if( $config_value['instant_search_filter'] == 1 ){
        $instance_search .= "<div class='instance_search_wrapper'>";
        $instance_search .= "<input data-temp_number='" . esc_attr( $temp_number ) . "' placeholder='{$config_value['instant_search_text']}' class='instance_search_input'>";
        $instance_search .= "</div>";
    }
    
    $html .= $instance_search; //For Instance Search Result
    $html .= $filter_html; //Its actually for Mini Filter Box
    $html .= $html_check; //Added at @Version 1.0.4
    $html .= '<br class="wpt_clear">'; //Added @Version 2.0
    $html .= apply_filters('wpt_before_table', ''); //Apply Filter Jese Before Table Tag
    
    /**
     * Why this array here, Actually we will send this data as dataAttribute of table 's tag.
     * although function has called at bellow where this array need.
     */
    $table_row_generator_array = array(
        'args'                      => $args,
        'wpt_table_column_keywords' => $table_column_keywords,
        'wpt_product_short'         => $sort,
        'wpt_permitted_td'          => $wpt_permitted_td,
        'wpt_add_to_cart_text'      => $add_to_cart_text,
        'temp_number'               => $temp_number,
        'texonomy_key'              => $taxonomy_column_keywords,
        'customfield_key'           => $customfileds_column_keywords,
        'filter_key'                => $filter_keywords,
        'filter_box'                => $filter_box,
        'description_type'          => $description_type,
        'description_on'            => $description_on,
        'title_variation'           => $title_variation,
        'thumb_variation'           => $thumb_variation,
        'ajax_action'               => $ajax_action,
        'table_type'               => $table_type,
        'checkbox'               => $checkbox,
    );
    ####echo '<pre>';
    ########print_r($table_row_generator_array['args']);
    ########echo '</pre>';
    $page_number_1plugs = $args['paged'] + 1;
    $html .= "<table "
            . "data-page_number='" . esc_attr( $page_number_1plugs ) . "' "
            . "data-temp_number='" . esc_attr( $temp_number ) . "' "
            . "data-config_json='" . esc_attr( wp_json_encode( $config_value ) ) . "' "
            . "data-data_json='" . esc_attr( wp_json_encode( $table_row_generator_array ) ) . "' "
            . "data-data_json_backup='" . esc_attr( wp_json_encode( $table_row_generator_array ) ) . "' "
            . "id='" . apply_filters('wpt_change_table_id', 'wpt_table') . "' "
            . "class='{$mobile_responsive} {$table_type} wpt_temporary_table_" . $temp_number . " wpt_product_table " . $template . "_table {$custom_table}_table $table_class " . $config_value['custom_add_to_cart'] . "' "
            . ">"; //Table Tag start here.

    /**
     * this $responsive_table will use for responsive table css Selector.
     * I have used this table selector at the end of table
     * See at bellow inside of <<<EOF EOF;
     * 
     * @since 1.5
     */
    $responsive_table = "table#wpt_table.mobile_responsive.wpt_temporary_table_{$temp_number}.wpt_product_table";

    /**
     * Table Column Field Tilte Define here
     * 
     * @since 1.0.04
     */
    $column_title_html = $responsiveTableLabelData = false;
    if ( $table_head && $enabled_column_array && is_array( $enabled_column_array ) && count( $enabled_column_array ) >= 1) {
        $column_title_html .= '<thead><tr data-temp_number="' . $temp_number . '" class="wpt_table_header_row wpt_table_head">';
        foreach ( $enabled_column_array as $key => $colunt_title ) {

            /**
             * this $responsiveTableLabelData will use for Responsives 
             */
            //$responsiveTableLabelData .= $responsive_table . ' td:nth-of-type(' . ($key + 1) . '):before { content: "' . $colunt_title . '"; }';
            $column_class = $key;
            
            /**
             * Modified Table colum, Mainly for CheckBox Button's column.
             * From this 1.9 version, We will only show All check - checkbox here.
             * 
             * @since 1.9
             * @date: 10.6.2018 d.m.y
             */
            $colunt_title = ( $column_class != 'check' ? $colunt_title : "<input data-type='universal_checkbox' data-temp_number='{$temp_number}' class='wpt_check_universal' id='wpt_check_uncheck_column_{$temp_number}' type='checkbox'><label for=wpt_check_uncheck_column_{$temp_number}></label>" );
            
            $column_title_html .= "<th class='wpt_{$column_class}'>{$colunt_title}</th>";
            
        }
        $column_title_html .= '</tr></thead>';
    }
    $html .= $column_title_html;

    $html .= '<tbody>'; //Starting TBody here
    
    $html .= wpt_table_row_generator( $table_row_generator_array );
    
    $html .= '</tbody>'; //Tbody End here
    $html .= "</table>"; //Table tag end here.
    $Load_More_Text = $config_value['load_more_text'];

    //pagination
    if( $pagination_start && $pagination_start == '1' ){
        $html .= wpt_pagination_by_args( $args , $temp_number);
    }
    $Load_More = '<div id="wpt_load_more_wrapper_' . $temp_number . '" class="wpt_load_more_wrapper ' . $config_value['disable_loading_more'] . '"><button data-temp_number="' . $temp_number . '" data-load_type="current_page" data-type="load_more" class="button wpt_load_more">' . $Load_More_Text . '</button></div>';
    $html .= ( $posts_per_page != -1 ? $Load_More : '' );//$Load_More;
    
    $html .= $html_check_footer;
    $html .= apply_filters('wpt_after_table', '', $temp_number ); //Apply Filter Just Before Table Wrapper div tag
  
    /**
     * Table Minicart for Footer.
     * Only will show, if select bottom minicart
     * 
     * @since 1.9
     */
    $html .= ($minicart_position == 'bottom' ? $table_minicart_message_box : false);
    
    $html .= "</div>"; //End of Table wrapper.
    $html .= apply_filters('wpt_after_table_wrapper', ''); //Apply Filter Just After Table Wrapper div tag

    $html .= isset( $custom_css_code ) ? $custom_css_code : '';
    
    /**
     * Extra content for Mobile Hide content Issue
     */
    $mobile_hide_css_code = false;
    if( $table_mobileHide_keywords && count( $table_mobileHide_keywords ) > 0 ){
        foreach( $table_mobileHide_keywords as $selector ){
            $mobile_hide_css_code .= "table#wpt_table.wpt_temporary_table_{$temp_number}.wpt_product_table th.wpt_" . $selector . ',';
            $mobile_hide_css_code .= "table#wpt_table.wpt_temporary_table_{$temp_number}.wpt_product_table .wpt_" . $selector . ',';
        }
    }
    $mobile_hide_css_code .= '.hide_column_for_mobile_only_for_selected{ display: none!important;}';
    
    $padding_left = 8;
    $text_align = 'left';
    $table_css_n_js_array = array(
        'mobile_hide_css_code'      =>  $mobile_hide_css_code,
        'responsive_table'          =>  $responsive_table,
        //'responsiveTableLabelData'  =>  $responsiveTableLabelData,
        'temp_number'               =>  $temp_number,
        'padding_left'              =>  $padding_left,
        'text_align'                =>  $text_align,
    );
    $html .= wpt_table_css_n_js_generator( $table_css_n_js_array );
    
    return $html;
}

function wpt_price_formatter(){
    $price_format = get_woocommerce_price_format();
    $curr_pos = '';
    switch($price_format){
        case '%1$s%2$s':
            $curr_pos = 'left';
            break;
        case '%2$s%1$s':
            $curr_pos = 'right';
            break;
        case '%1$s&nbsp;%2$s':
            $curr_pos = 'left-space';
            break;
        case '%2$s&nbsp;%1$s':
            $curr_pos = 'right-space';
            break;
    }
    return $curr_pos;
}

/**
 * CSS and JS code generator, Its under Table
 * 
 * @param type $table_css_n_js_array
 * @return string CSS and CSS code for bellow of Table
 */
function wpt_table_css_n_js_generator( $table_css_n_js_array  ){
    
    $mobile_hide_css_code = $table_css_n_js_array['mobile_hide_css_code'];
    $responsive_table = $table_css_n_js_array['responsive_table'];
    //$responsiveTableLabelData = $table_css_n_js_array['responsiveTableLabelData'];
    $temp_number = $table_css_n_js_array['temp_number'];
    $padding_left = $table_css_n_js_array['padding_left'];
    $text_align = $table_css_n_js_array['text_align'];
    $priceFormat = wpt_price_formatter();
    $html = <<<EOF
<style>
@media 
only screen and (max-width: 767px) {
    $mobile_hide_css_code        
    

    $responsive_table tr { border: 1px solid #ddd; margin-bottom: 5px;}

    $responsive_table td { 
        border-bottom: 1px solid;
        position: relative;
        text-align: $text_align;
        padding-left: {$padding_left}px !important;
        height: 100%;
        border: none;
        border-bottom: 1px solid #ddd;    
    }
    /*
    $responsive_table td,$responsive_table td.wpt_check,$responsive_table td.wpt_quantity{
     width: 100%;       
    }
    */
    $responsive_table td.wpt_quantity { 
       min-height: 57px;
    }
            
    $responsive_table td.wpt_thumbnails { 
       height: 100%;
       padding: 7px;
    }
            
    $responsive_table td.wpt_description { 
       min-height: 55px;
       height: 100%;
       padding: 7px;
    }
            
    $responsive_table td.wpt_action{ 
       min-height: 62px;
       height: auto;
    }        
    $responsive_table td.data_product_variations.woocommerce-variation-add-to-cart.variations_button.woocommerce-variation-add-to-cart-disabled.wpt_action{ 
            height: 100%;
            padding: 7px 0;
    }
            
    $responsive_table td:before { 
        width: 88px;
        white-space: normal;
        background: #b7b7b736;
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        text-align: right;
        padding-right: 10px;
    }
    /*VARresponsiveTableLabelData*/
} 
table tr.wpt_row td.wpt_quoterequest.addedd{
    display: block !important;
}
</style>
<script>
    (function($) {
        $(document).ready(function() {
            $('body').on('change', '.wpt_temporary_table_{$temp_number} .wpt_row input.input-text.qty.text', function() {
                var target_Qty_Val = $(this).val();
                
                var target_product_id = $(this).parents('tr').data('product_id');
                var targetTotalSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general');
                 
            
                var targetWeightSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_weight');
                var targetWeightAttr = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_weight').attr('data-weight');
                var totalWeight =  parseFloat(targetWeightAttr) * parseFloat(target_Qty_Val);
                totalWeight = totalWeight.toFixed(2);
                if(totalWeight === 'NaN'){
                totalWeight = '';
                }
                targetWeightSelector.html(totalWeight);
                
                var targetTotalStrongSelector = $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' td.wpt_total.total_general strong');
                var targetPrice = targetTotalSelector.attr('data-price');
                var targetCurrency = targetTotalSelector.data('currency');
                var targetPriceDecimalSeparator = targetTotalSelector.data('price_decimal_separator');
                var targetPriceThousandlSeparator = targetTotalSelector.data('thousand_separator');
                var targetNumbersPoint = targetTotalSelector.data('number_of_decimal');
                var totalPrice = parseFloat(targetPrice) * parseFloat(target_Qty_Val);
                totalPrice = totalPrice.toFixed(targetNumbersPoint);
                var priceFormat = '{$priceFormat}';
                var newPrice;
                switch(priceFormat){
                    case 'left': // left
                        newPrice = targetCurrency + totalPrice.replace(".",targetPriceDecimalSeparator);
                        break;
                    case 'right': // right
                        newPrice = totalPrice.replace(".",targetPriceDecimalSeparator) + targetCurrency;
                        break;
                    case 'left-space': // left with space
                        newPrice = targetCurrency + ' ' + totalPrice.replace(".",targetPriceDecimalSeparator);
                        break;
                    case 'right-space': // right with space
                        newPrice = totalPrice.replace(".",targetPriceDecimalSeparator) + ' ' + targetCurrency;
                        break;
                }
                $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' .wpt_action a.wpt_woo_add_cart_button').attr('data-quantity', target_Qty_Val);
                $('.yith_request_temp_{$temp_number}_id_' + target_product_id).attr('data-quantity', target_Qty_Val);
                $('.wpt_temporary_table_{$temp_number} .wpt_row_product_id_' + target_product_id + ' .wpt_total.total_general strong').html(newPrice);
                //$(target_row_id + ' a.add_to_cart_button').attr('data-quantity', target_Qty_Val); //wpt_total total_general
            });
            
        });
    })(jQuery);
</script>
EOF;
                return $html;
}

/**
 * Generate Table 's Root html based on Query args
 * 
 * @param type $args Query 's args
 * @param type $table_column_keywords table 's column
 * @param type $sort Its actually for Product Sorting
 * @param type $wpt_permitted_td Permission or each td
 * @param type $add_to_cart_text add_to_cart text
 * @return String 
 */
function wpt_table_row_generator( $table_row_generator_array ){
    ob_start();
    $html = false;
    //Getting WooProductTable Pro
    
    $table_ID = $table_row_generator_array['args']['table_ID'];
    $config_value = wpt_get_config_value( $table_ID );
    
    $args                   = $table_row_generator_array['args'];
    $table_column_keywords = $table_row_generator_array['wpt_table_column_keywords'];
    $sort      = $table_row_generator_array['wpt_product_short'];
    $wpt_permitted_td       = $table_row_generator_array['wpt_permitted_td'];
    $add_to_cart_text   = $table_row_generator_array['wpt_add_to_cart_text'];
    $temp_number            = $table_row_generator_array['temp_number'];
    $texonomy_key           = $table_row_generator_array['texonomy_key'];//texonomy_key
    $customfield_key        = $table_row_generator_array['customfield_key'];//texonomy_key
    $filter_key             = $table_row_generator_array['filter_key'];//texonomy_key
    $filter_box             = $table_row_generator_array['filter_box'];//Taxonomy Yes, or No
    $description_type = $table_row_generator_array['description_type'];
    $description_on = $table_row_generator_array['description_on'];
    $ajax_action            = $table_row_generator_array['ajax_action'];
    $title_variation        = $table_row_generator_array['title_variation'];
    $thumb_variation        = $table_row_generator_array['thumb_variation'];
    $checkbox        = $table_row_generator_array['checkbox'];
    
    $table_type           = $table_row_generator_array['table_type'];

    
    if( $args == false || $table_column_keywords == false ){
        return false;
    }
    //echo '<pre>';
    //print_r($args);echo "</pre>";
    $product_loop = new WP_Query($args);
    /**
     * If not set any Shorting (ASC/DESC) than Post loop will Random by Shuffle()
     * @since 1.0.0 -9
     */
    if ($sort == 'random') {
        shuffle($product_loop->posts);
    }
    $wpt_table_row_serial = (( $args['paged'] - 1) * $args['posts_per_page']) + 1; //For giving class id for each Row as well
    if ($product_loop->have_posts()) : while ($product_loop->have_posts()): $product_loop->the_post();
            global $product;
            
            $data = $product->get_data();
            $product_type = $product->get_type();
            (Int) $id = $data['id'];     
            
            $taxonomy_class = 'filter_row ';
            $data_tax = false;
            if( $filter_box && is_array( $filter_key ) && count( $filter_key ) > 0 ){
                foreach( $filter_key as $tax_keyword){
                    $terms = wp_get_post_terms( $data['id'], $tax_keyword  );

                    $attr = "data-{$tax_keyword}=";
                    
                    $attr_value = false;
                    if( is_array( $terms ) && count( $terms ) > 0 ){
                        foreach( $terms as $term ){
                            $taxonomy_class .= $tax_keyword . '_' . $temp_number . '_' . $term->term_id . ' ';
                            $attr_value .= $term->term_id . ':' . $term->name . ', ';
                        }
                    }
                    $data_tax .= $attr . '"' . $attr_value . '" ';
                }
            }else{
               $taxonomy_class = 'no_filter'; 
            }
            //visible_row wpt_row wpt_row_serial_$wpt_table_row_serial wpt_row_product_id_" . get_the_ID() . ' ' . $taxonomy_class . "
            $tr_class_arr = array(
                "visible_row",
                "wpt_row",
                "wpt_row_serial_$wpt_table_row_serial",
                "wpt_row_product_id_" . get_the_ID(),
                $taxonomy_class,
                $product_type,
                "product_type_" . $product_type,
                "stock_status_" . $data['stock_status'],
                "backorders_" . $data['backorders'],
                "sku_" . $data['sku'],
                "status_" . $data['status'],
                
            );
            $tr_class = implode( " ", $tr_class_arr );
            /**
             * Table Row and
             * And Table Data filed here will display
             * Based on Query
             */
            $wpt_each_row = false;
            echo "<tr role='row' data-title='" . esc_attr( $data['name'] ) . "' data-product_id='" . $data['id'] . "' id='product_id_" . $data['id'] . "' class='" . esc_attr( $tr_class ) . "' {$data_tax}>";
            
            /**
             * Texonomy Handaler
             * 
             * @since 1.9 
             * @date: 10.6.2016 d.m.y
             */
            if(is_array( $texonomy_key ) && count( $texonomy_key ) > 0 ){
                foreach( $texonomy_key as $keyword ){
                   $generated_keyword = substr( $keyword, 4 );
                    $texonomy_content = '';
                    if(is_string( get_the_term_list($data['id'],$generated_keyword) ) ){
                        $texonomy_content = get_the_term_list($data['id'],$generated_keyword,'',', ');
                    }
                   $wpt_each_row[$keyword] = "<td data-keyword='wpt_{$keyword}' class='wpt_custom_cf_tax wpt_custom_tax wpt_{$keyword}'>" . $texonomy_content . "</td>";  
                }
            }

            /**
             * Texonomy Handaler
             * 
             * @since 1.9 
             * @date: 10.6.2016 d.m.y
             */
            if(is_array( $customfield_key ) && count( $customfield_key ) > 0 ){
                foreach( $customfield_key as $keyword ){
                   $generated_keyword = substr( $keyword, 3 );
                    $customfield_content = false;
                    $custom_meta = get_post_meta( $data['id'],$generated_keyword );
                    if( function_exists( 'get_field' ) ){
                        $acf_content = get_field( $generated_keyword );
                        $customfield_content = !$acf_content ? false : $acf_content;
                    }

                    if( !$customfield_content && is_array( $custom_meta ) && isset( $custom_meta[0] ) ){
                        $customfield_content = $custom_meta[0];
                    }  
                    
                    if( is_string( $customfield_content ) ){
                        $customfield_content == do_shortcode( $customfield_content );
                    }else{
                        $customfield_content = "";
                    }
                   $wpt_each_row[$keyword] = "<td data-keyword='wpt_{$keyword}' class='wpt_custom_cf_tax wpt_custom_cf wpt_{$keyword}'>" . $customfield_content . "</td>";  
                }
            }
            
            
            
            
            $items_permanent_dir = __DIR__ . '/items/';
            $items_directory = apply_filters('wpt_item_directory_old', $items_permanent_dir, $table_ID, $product );
            foreach( $table_column_keywords as $td_keyword ){
                if( is_string( $td_keyword ) ){
                    $file = $items_directory . $td_keyword . '.php';
                    $file = apply_filters( 'wpto_template_loc_item_' . $td_keyword, $file, $table_ID, $product );
                    if( !file_exists( $file ) ){
                        $file = $items_permanent_dir . 'default.php';
                        $file = apply_filters( 'wpt_defult_file_loc', $file, $product);
                    }
                    ?>
<td class="wpt_<?php echo esc_attr( $td_keyword ); ?>" 
    data-keyword="<?php echo esc_attr( $td_keyword ); ?>" 
    data-sku="<?php echo esc_attr( $product->get_sku() ); ?>">    
                    <?php
                    //*****************************FILE INCLUDING HERE
                    include $file;
                                        ?>
</td>    
                    <?php
                }
            }
            

            
            
            
            
            
            
            
            
            
            //$html .= wpt_generate_each_row_data($table_column_keywords, $wpt_each_row);
            echo "</tr>"; //End of Table row

            $wpt_table_row_serial++; //Increasing Serial Number.
            
        endwhile;
        //Moved reset query from here to end of table at version 4.3
    else:
        $html .= "<div class='wpt_loader_text wpt_product_not_found'>" . $config_value['product_not_founded'] . "</div>";
    endif;
    
    wp_reset_query(); //Added reset query before end Table just at Version 4.3
    
    $html = ob_get_clean();
    return $html;
}

/**
 * Texonomy select box for Texonomy.
 * 
 * @param type $texonomy_keyword
 * @param type $temp_number
 * @param type $current_select_texonomies
 * @return string|boolean
 */
function wpt_texonomy_search_generator( $texonomy_keyword, $temp_number , $search_n_filter = false){
    
    $selected_taxs = isset( $search_n_filter[$texonomy_keyword] ) ? $search_n_filter[$texonomy_keyword] : false;
    //Added at 3.1 date: 10.9.2018
    //$config_value = get_option('wpt_configure_options');
    $config_value = wpt_get_config_value( $temp_number ); //V5.0 temp number is post_ID , $table_ID
    $html = false;
    if( !$texonomy_keyword || is_array( $texonomy_keyword )){
        return false;
    }
    
    /**
     * Need for get_texonomy and get_terms
     */
    $texonomy_sarch_args = array('hide_empty' => true,'orderby' => 'count','order' => 'DESC');
    
    $taxonomy_details = get_taxonomy( $texonomy_keyword );

    if( !$taxonomy_details ){
        return false;
    }
    $label = $taxonomy_details->labels->menu_name;//label;
    $label_all_items = $taxonomy_details->labels->all_items;
    $html .= "<div class='search_single search_single_texonomy search_single_{$texonomy_keyword}'>";
    $html .= "<label class='search_keyword_label {$texonomy_keyword}' for='{$texonomy_keyword}_{$temp_number}'>{$label}</label>";
    

    $html .= "<select data-key='{$texonomy_keyword}' class='search_select query search_select_{$texonomy_keyword}' id='{$texonomy_keyword}_{$temp_number}' multiple>";
    //$html .= "<option value=''>{$label_all_items}</option>";
    $texonomy_boj = get_terms( $texonomy_keyword, $texonomy_sarch_args );
    if( count( $texonomy_boj ) > 0 ){
        //Search box's Filter Sorting Added at Version 3.1
        $customized_texonomy_boj = false;

        if( $selected_taxs && is_array( $selected_taxs ) && count( $selected_taxs ) > 0 ){
            foreach( $selected_taxs as $termID ){
                $singleTerm = get_term( $termID );
                $name = $singleTerm->name;
                $customized_texonomy_boj[$name] = $singleTerm;
            }
        }else{
            foreach( $texonomy_boj as $item ){
                $name = $item->name;
                $customized_texonomy_boj[$name] = $item;

            }
            $customized_texonomy_boj = wpt_sorting_array( $customized_texonomy_boj, $config_value['sort_searchbox_filter'] );
        }
        

        foreach( $customized_texonomy_boj as $item ){
            $html .= "<option value='{$item->term_id}'>{$item->name} ({$item->count}) </option>";
        }
    }
    $html .= "</select>";

        
        
        
        
        
    $html .= "</div>"; //End of .search_single
    
    
    return $html;
}

/**
 * Sorting Associative array based on ASC,DESC or None.
 * 
 * @param type $array Associative Array
 * @param type $sorting_type Available type ASC,DESC,None
 * @return Array
 */
function wpt_sorting_array( $array, $sorting_type ){
    if( $sorting_type == 'ASC' ){
        ksort( $array );
    }else if( $sorting_type == 'DESC' ){
        krsort( $array );
    }
    
    return $array;
}

/**
 * Texonomy select for Filter -- Texonomy.
 * 
 * @param type $texonomy_keyword
 * @param type $temp_number
 * @param type $current_select_texonomies
 * @return string|boolean
 */
function wpt_texonomy_filter_generator( $texonomy_keyword, $temp_number ){
    //Getting data from options
    //$config_value = get_option('wpt_configure_options');
    $config_value = wpt_get_config_value( $temp_number ); //V5.0 temp number is post_ID , $table_ID
    $html = false;
    if( !$texonomy_keyword || is_array( $texonomy_keyword )){
        return false;
    }
    
    /**
     * Need for get_texonomy and get_terms
     */
    $texonomy_sarch_args = array('hide_empty' => true,'orderby' => 'count','order' => 'DESC');
    
        $taxonomy_details = get_taxonomy( $texonomy_keyword );
        if( !$taxonomy_details ){
            return false;
        }
        
        $label = $taxonomy_details->labels->singular_name;
        $html .= "<select data-temp_number='{$temp_number}' data-key='{$texonomy_keyword}' data-label='{$label}' class='filter_select select2 filter filter_select_{$texonomy_keyword}' id='{$texonomy_keyword}_{$temp_number}'>";

            $texonomy_boj = get_terms( $texonomy_keyword, $texonomy_sarch_args );
            /*
            if( count( $texonomy_boj ) > 0 ){

                $customized_texonomy_boj = false;
                foreach( $texonomy_boj as $item ){
                    $name = $item->name;
                    $customized_texonomy_boj[$name] = $item;
                    
                }
                $customized_texonomy_boj = wpt_sorting_array( $customized_texonomy_boj, $config_value['sort_mini_filter'] );
                foreach( $customized_texonomy_boj as $item ){  
                    $html .= "<option value='{$texonomy_keyword}_{$temp_number}_{$item->term_id}'>{$item->name}</option>";
                    //$html .= "<option value='{$item->term_id}' " . ( is_array($current_select_texonomies) && in_array($item->term_id, $current_select_texonomies) ? 'selected' : false ) . ">{$item->name} ({$item->count}) </option>";
                }
            }
            */
        $html .= "</select>";
    return $html;
}

/**
 * Total Search box Generator
 * 
 * @param type $temp_number It's a Temporay Number for each Table,
 * @param type $search_box_texonomiy_keyword Obviously should be a Array, for product_cat tag etc
 * @param int $search_n_filter getting search and fileter meta
 * @return string
 */
function wpt_search_box($temp_number, $search_box_texonomiy_keyword = array( 'product_cat', 'product_tag' ), $order_by = false, $order = false, $search_n_filter = false,$table_ID = false ){
    //$config_value = get_option('wpt_configure_options');
    $config_value = wpt_get_config_value( $temp_number ); //V5.0 temp number is post_ID , $table_ID
    $html = false;
    $html .= "<div id='search_box_{$temp_number}' class='wpt_search_box search_box_{$temp_number}'>";
    $html .= '<div class="search_box_fixer">'; //Search_box inside fixer
    $html .= '<h3 class="search_box_label">' . $config_value['search_box_title'] . '</h3>';
    $html .= "<div class='search_box_wrapper'>";
    
    /**
     * Search Input Box
     * At Version 3.3, we have changed few features
     */
    $html .= "<div class='search_single search_single_direct'>";
        
        $single_keyword = $config_value['search_box_searchkeyword'];//__( 'Search keyword', 'wpt_pro' );
        $html .= "<div class='search_single_column'>";
        $html .= '<label class="search_keyword_label single_keyword" for="single_keyword_' . $temp_number . '">' . $single_keyword . '</label>';
        $html .= '<input data-key="s" class="query_box_direct_value" id="single_keyword_' . $temp_number . '" value="" placeholder="' . $single_keyword . '"/>';
        $html .= "</div>";// End of .search_single_column
        
        $single_keyword = $config_value['search_box_orderby'];//__( 'Order By', 'wpt_pro' ); //search_box_orderby
        $html .= "<div class='search_single_column search_single_sort search_single_order_by'>";
        $html .= '<label class="search_keyword_label single_keyword" for="order_by' . $temp_number . '">' . $single_keyword . '</label>';
        
        $html .= '<select data-key="orderby" id="order_by_' . $temp_number . '" class="query_box_direct_value select2" >';
        $html .= '<option value="name" '. wpt_check_sortOrder( $order_by, 'name' ) .'>'.esc_html__( 'Name','wpt_pro' ).'</option>';
        $html .= '<option value="menu_order" '. wpt_check_sortOrder( $order_by, 'menu_order' ) .'>'.esc_html__( 'Menu Order','wpt_pro' ).'</option>';
        $html .= '<option value="type" '. wpt_check_sortOrder( $order_by, 'type' ) .'>'.esc_html__( 'Type','wpt_pro' ).'</option>';
        $html .= '<option value="comment_count" '. wpt_check_sortOrder( $order_by, 'comment_count' ) .'>'.esc_html__( 'Reviews','wpt_pro' ).'</option>';
        $html .= '</select>';

        $html .= "</div>";// End of .search_single_column

        $single_keyword = $config_value['search_box_order']; //__( 'Order', 'wpt_pro' );
        $html .= "<div class='search_single_column search_single_order'>";
        $html .= '<label class="search_keyword_label single_keyword" for="order_' . $temp_number . '">' . $single_keyword . '</label>';
        $html .= '<select data-key="order" id="order_' . $temp_number . '" class="query_box_direct_value select2" >  ';
        $html .= '<option value="ASC" '. wpt_check_sortOrder( $order, 'ASC' ) .'>'.esc_html__( 'ASCENDING','wpt_pro' ).'</option>';
        $html .= '<option value="DESC" '. wpt_check_sortOrder( $order, 'DESC' ) .'>'.esc_html__( 'DESCENDING','wpt_pro' ).'</option>';
        $html .= '<option value="random" '. wpt_check_sortOrder( $order, 'random' ) .'>'.esc_html__( 'Random','wpt_pro' ).'</option>';
        $html .= '</select>';

        $html .= "</div>";// End of .search_single_column
        
        
        
    $html .= "</div>"; //end of .search_single
    
    /**
     * Texonomies Handle based on $search_box_texonomiy_keyword
     * Default cat and tag for product
     * 
     * @since 1.9
     * @date 10.6.2018 d.m.y
     */
    if( is_array( $search_box_texonomiy_keyword ) && count( $search_box_texonomiy_keyword ) > 0 ){
        foreach( $search_box_texonomiy_keyword as $texonomy_name ){
           $html .= wpt_texonomy_search_generator( $texonomy_name,$temp_number, $search_n_filter ); 
        }
    }
    $html .=  apply_filters('end_part_advance_search_box','',$table_ID);
    $html .= '</div>'; //End of .search_box_singles
    
    $html .= '<button data-type="query" data-temp_number="' . $temp_number . '" id="wpt_query_search_button_' . $temp_number . '" class="button wpt_search_button query_button wpt_query_search_button wpt_query_search_button_' . $temp_number . '">' . $config_value['search_button_text'] . '</button>';
    $html .= '</div>';//End of .search_box_fixer
    $html .= '</div>';//End of .wpt_search_box
    return $html;
}

/**
 * Total Search box Generator
 * 
 * @param type $temp_number It's a Temporay Number for each Table,
 * @param type $search_box_texonomiy_keyword Obviously should be a Array, for product_cat tag etc
 * @return string
 */
function wpt_filter_box($temp_number, $filter_keywords = false ){
    $html = $html_select = false;
    //$config_value = get_option('wpt_configure_options');
    $config_value = wpt_get_config_value( $temp_number ); //V5.0 temp number is post_ID , $table_ID
    /**
     * Texonomies Handle based on $search_box_texonomiy_keyword
     * Default cat and tag for product
     * 
     * @since 20
     * @date 11.6.2018 d.m.y
     */
    if( is_array( $filter_keywords ) && count( $filter_keywords ) > 0 ){
        foreach( $filter_keywords as $texonomy_name ){
           $html_select .= wpt_texonomy_filter_generator( $texonomy_name,$temp_number ); 
        }
    }
    if( $html_select ){
        $html .= "<label>" . __( $config_value['filter_text'], 'wpt_pro' ) . "</label>" . $html_select;
        $html .= '<a href="#" data-type="reset " data-temp_number="' . $temp_number . '" id="wpt_filter_reset_' . $temp_number . '" class="wpt_filter_reset wpt_filter_reset_' . $temp_number . '">' . __( $config_value['filter_reset_button'], 'wpt_pro' ) . '</a>';
    }
    return $html;
}