<?php

$wpt_single_action = false;
if( $table_type == 'advance_table'){

    woocommerce_template_single_add_to_cart();

}else{
    $variation_in_action = false;
    if( 'variable' == $product_type && is_array( $table_column_keywords ) && count( $table_column_keywords ) > 1 ){
        foreach( $table_column_keywords as $wpt_key => $wpt_val ){
            $variation_in_action = isset( $column_settings[$wpt_key]['items'] ) && in_array( 'variations', $column_settings[$wpt_key]['items'] ) ? true : false;
            if($variation_in_action){
                break;
            }
            
        }
    }
    //var_dump($variation_in_action);
    //var_dump($keyword,$table_column_keywords);
    if( 'variable' == $product_type && !$variation_in_action && !in_array( 'variations', $table_column_keywords) ){
        echo $variation_html;
        do_action('wpt_action_variation',$product); //Sepcially for Min Max Plugin
        
    }

    $ajax_action_final = ( $product_type == 'grouped' || $product_type == 'external' ? 'no_ajax_action ' : $ajax_action . ' ' );//$ajax_action;
    if( $product_type == 'grouped' || $product_type == 'external' ){
        $add_to_cart_url = $product->add_to_cart_url();
    }else{
        $add_to_cart_url = '?add-to-cart=' .  $data['id'];//( $ajax_action == 'no_ajax_action' ? '?add-to-cart=' .  $data['id'] : '?add-to-cart=' .  $data['id'] );// '?add-to-cart=' .  $data['id'];
    }

    $add_to_cart_text_final = ( $product_type == 'grouped' || $product_type == 'external' || $add_to_cart_text == ' ' ? $product->add_to_cart_text() : $add_to_cart_text );//'?add-to-cart=' .  $data['id']; //home_url() .  
    echo apply_filters('woocommerce_loop_add_to_cart_link', 
            sprintf('<a rel="nofollow" data-add_to_cart_url="%s" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s">%s</a>', 
                    esc_attr( $add_to_cart_url ),
                    esc_url( $add_to_cart_url ), 
                    esc_attr( $default_quantity ), //1 here was 1 before 2.8
                    esc_attr($product->get_id()), 
                    esc_attr($product->get_sku()), 
                    esc_attr( $ajax_action_final . ( $row_class ? 'wpt_variation_product single_add_to_cart_button button alt disabled wc-variation-selection-needed wpt_woo_add_cart_button' : 'button wpt_woo_add_cart_button ' . $stock_status_class ) ), //ajax_add_to_cart  //|| !$data['price']
                    esc_html( $add_to_cart_text_final )
            ), $product,false,false);
}