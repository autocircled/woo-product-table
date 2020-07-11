<?php
$meta_search_n_filter =  get_post_meta( $post->ID, 'search_n_filter', true );
?>
<div class="section ultraaddons-panel">
    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="wpt_search_box"><?php esc_html_e( 'Advance Search Box', 'wpt_pro' ); ?></label>
                </th>
                <td>
                    <select name="search_n_filter[search_box]" data-name='search_box' id="wpt_search_box" class="wpt_fullwidth wpt_data_filed_atts ua_input" >
                        <option value="no" <?php echo isset( $meta_search_n_filter['search_box'] ) && $meta_search_n_filter['search_box'] == 'no' ? 'selected' : ''; ?>><?php esc_html_e( 'No Search Box', 'wpt_pro' ); ?></option>
                        <option value="yes" <?php echo isset( $meta_search_n_filter['search_box'] ) && $meta_search_n_filter['search_box'] == 'yes' ? 'selected' : ''; ?>><?php esc_html_e( 'Show Search Box', 'wpt_pro' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>
    </div>

    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="wpt_search_from"><?php esc_html_e( 'Search From', 'wpt_pro' ); ?></label>
                </th>
                <td>
                    <ul class="wpt-tag-list">
                    <?php
                    $search_form_arr = array(
                        'ID' => 'posts',
                        'post_title' => 'posts',
                        'post_content' => 'posts',
                        'menu_order' => 'posts',
                        'post_author' => 'posts',
                        'post_excerpt' => 'posts',
                        'post_parent' => 'posts',
                        'post_name' => 'posts',
                        'post_date' => 'posts',
                        '_sku' => 'postmeta',
                        '_price' => 'postmeta',
                        '_regular_price' => 'postmeta',
                        '_sale_price' => 'postmeta',
                        '_sku' => 'postmeta',
                    );

                    foreach($search_form_arr as $search_key => $search_type){
                        ?>  
                        <li class="wpt_checkbox wpt_search_form_checkbox wpt_<?php esc_attr_e( $search_key ); ?>_checkbox">
                            <input type="checkbox" name="search_n_filter[search_from][<?php esc_attr_e( $search_key ); ?>]" id="search_from_<?php esc_attr_e( $search_key ); ?>" value="<?php esc_attr_e( $search_type ); ?>" <?php echo isset( $meta_search_n_filter['search_from'][$search_key] ) && !empty( $meta_search_n_filter['search_from']) ? 'checked' : ''; ?>>
                            <label for="search_from_<?php esc_attr_e( $search_key ); ?>"><?php esc_html_e( $search_key ); ?></label>
                        </li>
                    <?php
                    }
                    ?>    
                    </ul>
                </td>
            </tr>
        </table>

    </div>

    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="wpt_taxonomy_keywords"><?php esc_html_e( 'Taxonomy Keywords for Advance Search Box (Separate with comma[,])', 'wpt_pro' ); ?></label>
                </th>
                <td>
                    <input name="search_n_filter[taxonomy_keywords]" data-name='taxonomy_keywords' id="wpt_taxonomy_keywords" value="<?php echo isset( $meta_search_n_filter['taxonomy_keywords'] ) ?$meta_search_n_filter['taxonomy_keywords'] : 'product_cat,product_tag'; ?>" class="wpt_fullwidth wpt_data_filed_atts ua_input" type="text" value="" placeholder="<?php esc_attr_e( 'eg: product_cat,product_tag,color,size', 'wpt_pro' ); ?>">
                    <p><?php echo sprintf( esc_html__( 'There are lot of %s for creating Taxonomy.', 'wpt_pro' ),
                            '<a href="https://wordpress.org/plugins/search/Taxonomy/" target="_blank">Taxonomy Creator Plugin available</a>'
                            );
                    ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <?php
        if( isset( $meta_search_n_filter['taxonomy_keywords'] ) && strlen( $meta_search_n_filter['taxonomy_keywords'] ) > 0 ){
            $snf_keywords = $meta_search_n_filter['taxonomy_keywords'];
            $snf_keywords = explode( ',', $snf_keywords );


            foreach( $snf_keywords as $per_keyword ){

                $args = array(
                    'hide_empty'    => false, 
                    'orderby'       => 'count',
                    'order'         => 'DESC',
                );

                //WooCommerce Product Category Object as Array
                $tax_object = get_terms( $per_keyword, $args );
                if( !isset( $tax_object->errors ) ){
    ?>
    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="filter_for_<?php echo $per_keyword; ?>"><?php echo sprintf( esc_html__('Choose [%s] %s (For Advanced Searchbox Filter) %s','wpt_pro'),$per_keyword,'<small>','</small>');?></label>
                </th>
                <td>
                    <select name="search_n_filter[<?php echo $per_keyword; ?>][]" data-name="<?php echo $per_keyword; ?>" id="filter_for_<?php echo $per_keyword; ?>" class="wpt_fullwidth wpt_data_filed_atts wpt_select2 ua_input" multiple>
                        <?php
                        foreach ( $tax_object as $tax_item ) {
                            $tax_array_key = $per_keyword;
                            echo "<option value='{$tax_item->term_id}' " . ( isset( $meta_search_n_filter[$tax_array_key] ) &&  is_array( $meta_search_n_filter[$tax_array_key] ) && in_array( $tax_item->term_id, $meta_search_n_filter[$tax_array_key] ) ? 'selected' : false ) . ">{$tax_item->name} - {$tax_item->slug} ({$tax_item->count})</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <?php               
                }
            }
        }else{
    ?>
    <p style="background-color: #dd9933;padding: 8px;"><?php esc_html_e( 'To get Taxonomy customization, Please save/update this shortcode.', 'wpt_pro' ); ?></p>
    <?php
        }
    ?>
    <?php
        /**
         * To Get Category List of WooCommerce
         * @since 1.0.0 -10
         */

    ?>




    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="wpt_filter_box"><?php esc_html_e( 'Mini Filter', 'wpt_pro' ); ?></label>
                </th>
                <td>
                    <select name="search_n_filter[filter_box]" data-name='filter_box' id="wpt_filter_box" class="wpt_fullwidth wpt_data_filed_atts ua_input" >
                        <option value="no" <?php echo isset( $meta_search_n_filter['filter_box'] ) && $meta_search_n_filter['filter_box'] == 'no' ? 'selected' : ''; ?>><?php esc_html_e( 'No Filter', 'wpt_pro' ); ?></option>
                        <option value="yes" <?php echo isset( $meta_search_n_filter['filter_box'] ) && $meta_search_n_filter['filter_box'] == 'yes' ? 'selected' : ''; ?>><?php esc_html_e( 'Filter Show', 'wpt_pro' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    <div class="wpt_column">
        <table class="ultraaddons-table">
            <tr>
                <th>
                    <label class="wpt_label" for="wpt_filter"><?php esc_html_e( 'Taxonomy Keywords for Filter (Separate with comma[,])', 'wpt_pro' ); ?></label>
                </th>
                <td>
                    <input name="search_n_filter[filter]" data-name='filter' id="wpt_filter" value="<?php echo isset( $meta_search_n_filter['filter'] ) ?$meta_search_n_filter['filter'] : 'product_cat,product_tag'; ?>" class="wpt_fullwidth wpt_data_filed_atts ua_input" type="text" value="" placeholder="<?php esc_attr_e( 'eg: product_cat,product_tag,color,size', 'wpt_pro' ); ?>">
                    <p><?php echo sprintf( esc_html__( 'There are lot of %s for creating Taxonomy.', 'wpt_pro' ),
                            '<a href="https://wordpress.org/plugins/search/Taxonomy/" target="_blank">Taxonomy Creator Plugin available</a>'
                            );
                    ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>