<?php

add_filter('plugin_action_links_' . WPT_Product_Table::getPath('PLUGIN_BASE_FILE'), 'wpt_add_action_links');

if( !function_exists( 'wpt_add_action_links' ) ){
    /**
     * For showing configure or add new link on plugin page
     * It was actually an individual file, now combine at 4.1.1
     * @param type $links
     * @return type
     */
    function wpt_add_action_links($links) {
        $wpt_links[] = '<a href="https://codecanyon.net/item/woo-product-table-pro/20676867" title="' . esc_attr__( 'Many awesome features is waiting for you', 'wpt_pro' ) . '" target="_blank">'.esc_html__( 'GET PRO VERSION','wpt_pro' ).'</a>';
        $wpt_links[] = '<a href="' . admin_url( 'post-new.php?post_type=wpt_product_table' ) . '" title="' . esc_attr__( 'Add new Shortcode', 'wpt_pro' ) . '">' . esc_html__( 'Create Table', 'wpt_pro' ).'</a>';
        $wpt_links[] = '<a href="' . admin_url( 'edit.php?post_type=wpt_product_table&page=woo-product-table-config' ) . '" title="' . esc_attr__( 'Configure for Universal', 'wpt_pro' ) . '">' . esc_html__( 'Configure', 'wpt_pro' ) . '</a>';
        $wpt_links[] = '<a href="https://codeastrology.com/support/" title="' . esc_attr__( 'CodeAstrology Support', 'wpt_pro' ) . '" target="_blank">'.esc_html__( 'Support','wpt_pro' ).'</a>';
        return array_merge( $wpt_links, $links );
    }                                       
}


if( !function_exists( 'wpt_admin_menu' ) ){
    /**
     * Set Menu for WPT (Woo Product Table) Plugin
     * It was actually an individual file, now combine  at 4.1.1
     * 
     * @since 1.0
     * 
     * @package Woo Product Table
     */
    function wpt_admin_menu() {
        
        add_submenu_page( 'edit.php?post_type=wpt_product_table', esc_html__( 'Configuration WPTpro', 'wpt_pro' ),  esc_html__( 'Configure', 'wpt_pro' ), WPT_CAPABILITY, 'woo-product-table-config', 'wpt_configuration_page' );
        add_submenu_page( 'edit.php?post_type=wpt_product_table', esc_html__( 'FAQ & Support page - Contact With US', 'wpt_pro' ), sprintf( esc_html__( 'FAQ %s& Contact%s', 'wpt_pro' ), '<span style="color:#ff8921;">', '</span>'), WPT_CAPABILITY, 'wpt_fac_contact_page', 'wpt_fac_support_page' );
        add_submenu_page( 'edit.php?post_type=wpt_product_table', esc_html__( 'GET PRO VERSION', 'wpt_pro' ),  __( '<i>Get <strong>Pro</strong></i>', 'wpt_pro' ), WPT_CAPABILITY, 'https://codecanyon.net/item/woo-product-table-pro/20676867' );
        add_submenu_page( 'edit.php?post_type=wpt_product_table', esc_html__( 'Pro Features', 'wpt_pro' ),  __( 'Pro Features', 'wpt_pro' ), WPT_CAPABILITY, 'wpt-pro-features', 'wpt_pro_features_content' );
    }
}
add_action( 'admin_menu', 'wpt_admin_menu' );

if( !function_exists( 'wpt_pro_features_content' ) ){
    
    /**
     * Displaying/Present Pro Features
     * in HTML file
     */
    function wpt_pro_features_content(){
        include __DIR__ . '/pro-features-html.php';
    }
}