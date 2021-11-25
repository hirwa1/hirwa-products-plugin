 <?php
/*
 Plugin Name: Hirwa Store
 Plugin URI: https://github.com/hirwa1/hirwa-products-plugin
 Description: Create a Hirwa Store to display product information
 Version: 1.0
Author: NIYIBIZI HIRWA
Author URI: http://tkd.co.rw
  */
  
/* Copyright 2021 NIYIBIZI HIRWA(email : hirwa@tkd.co.rw)
  
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.
  
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
  
 
 */
  

  
// Call function when plugin is activated
 register_activation_hook( __FILE__, 'Hirwa_store_install' );
  
 function Hirwa_store_install() {
  
 //setup default option values
 $hiirwa_options_arr = array(
 'currency_sign' => '$'
 );
  
 //save our default option values
 update_option( 'Hirwa_options', $hiirwa_options_arr );
  
 }
  
  
// Action hook to initialize the plugin
 add_action( 'init', 'Hirwa_store_init' );
  
//Initialize the Hirwa Store
 function Hirwa_store_init() {
  
 //register the products custom post type 
 	$labels = array(
                  'name' => __( 'Products',
                  'Hirwa-plugin' ),
                  'singular_name' => __( 'Product',
                  'Hirwa-plugin' ),
                  'add_new' => __( 'Add New',
                  'Hirwa-plugin' ),
                  'add_new_item' => __( 'Add New Product',
                  'Hirwa-plugin' ),
                  'edit_item' => __( 'Edit Product',
                  'Hirwa-plugin' ),
                  'new_item' => __( 'New Product',
                  'Hirwa-plugin' ),
                  'all_items' => __( 'All Products',
                  'Hirwa-plugin' ),
                  'view_item' => __( 'View Product',
                  'Hirwa-plugin' ),
                  'search_items' => __( 'Search Products',
                  'Hirwa-plugin' ),
                  'not_found' => __( 'No products found',
                  'Hirwa-plugin' ),
                  'not_found_in_trash' => __( 'No products found in Trash',
                  'Hirwa-plugin' ),
                  'menu_name' => __( 'Products', 'Hirwa-plugin' )
                   );


  
 $args = array(
         'labels' => $labels,
         'public' => true,
         'publicly_queryable' => true,
         'show_ui' => true,
         'show_in_menu' => true,
         'query_var' => true,
         'rewrite' => true,
         'capability_type' => 'post',
         'has_archive' => true,
         'hierarchical' => false,
         'menu_position' => null,
         'supports' => array( 'title', 'editor',
         'thumbnail', 'excerpt' )
               );
      register_post_type( 'Hirwa-products', $args );
  
}
  
// Action hook to add the post products menu item
 add_action( 'admin_menu', 'Hirwa_store_menu' );
         //create the Hirwa Masks sub-menu
       function Hirwa_store_menu() {
 	 add_options_page(
           __( 'Hirwa Store Settings Page', 'Hirwa-plugin' ),
           __( 'Hirwa Store Settings', 'Hirwa-plugin' ),
            'manage_options',
            'Hirwa-store-settings',
            'Hirwa_store_settings_page'
 );
}
  
//build the plugin settings page
 function Hirwa_store_settings_page() {
  
 //load the plugin options array
      $hiirwa_options_arr = get_option( 'Hirwa_options' );
  
 //set the option array values to variables
 $hs_inventory = (
          ! empty( $hiirwa_options_arr['show_inventory'] ) )
          ? $hiirwa_options_arr['show_inventory'] : '';
          $hs_currency_sign = $hiirwa_options_arr['currency_sign'];
 ?>
     <div class="wrap">
            <h2>
            	<?php _e( 'Hirwa Store Options',
                'Hirwa-plugin' ) ?>
                	
                </h2>
 <form method="post" action="options.php">

 <?php settings_fields( 'Hirwa-settings-group' ); ?>
       <table class="form-table">
        <tr valign="top">
         <th scope="row"><?php _e( 'Show Product Inventory',
 'Hirwa-plugin' ) ?></th>
                 <td><input type="checkbox"
           name="Hirwa_options[show_inventory]" <?php
           echo checked( $hs_inventory, 'on' ); ?> /></td>
 </tr>
          <tr valign="top">
    <th scope="row"><?php _e( 'Currency Sign',
 'Hirwa-plugin' ) ?></th>
       <td><input type="text"
 name="Hirwa_options[currency_sign]"
      value="<?php echo esc_attr( $hs_currency_sign ); ?>"
         size="1" maxlength="1" /></td>
      </tr>
 </table>
  
 <p class="submit">
 <input type="submit" class="button-primary"
 value="<?php _e( 'Save Changes',
 'Hirwa-plugin' ); ?>" />
 </p>
  
 </form>
 </div>
  <?php
 }
// Action hook to register the plugin option settings
 add_action( 'admin_init', 'Hirwa_store_register_settings' );
 function Hirwa_store_register_settings() {
  
 //register the array of settings
 register_setting( 'Hirwa-settings-group',
 'Hirwa_options', 'Hirwa_sanitize_options' );
  
}
 function Hirwa_sanitize_options( $options ) {
  
 $options['show_inventory'] = (
 ! empty( $options['show_inventory'] ) )
 ? sanitize_text_field( $options['show_inventory'] ) : '';
 $options['currency_sign'] = (
 ! empty( $options['currency_sign'] ) )
 ? sanitize_text_field( $options['currency_sign'] ) : '';
  
 return $options;
  
}
  
//Action hook to register the Products meta box
 add_action( 'add_meta_boxes',
 'Hirwa_store_register_meta_box' );
 function Hirwa_store_register_meta_box() {
  
 // create our custom meta box
 add_meta_box( 'Hirwa-product-meta',
 __( 'Product Information','Hirwa-plugin' ),
 'Hirwa_meta_box', 'Hirwa-products',
 'side', 'default' );
}
  
//build product meta box
 function Hirwa_meta_box( $post ) {
  
 // retrieve our custom meta box values
 $hs_meta = get_post_meta( $post->ID,
 '_Hirwa_product_data', true );
  
 $hiirwa_sku = ( ! empty( $hs_meta['sku'] ) )
 ? $hs_meta['sku'] : '';
 $hiirwa_price = ( ! empty( $hs_meta['price'] ) )
 ? $hs_meta['price'] : '';
 $hiirwa_weight = ( ! empty( $hs_meta['weight'] ) )

 ? $hs_meta['weight'] : '';
 $hiirwa_color = ( ! empty( $hs_meta['color'] ) )
 ? $hs_meta['color'] : '';
 $hiirwa_inventory = ( ! empty( $hs_meta['inventory'] ) )
 ? $hs_meta['inventory'] : '';
  
 //nonce field for security
 wp_nonce_field( 'meta-box-save', 'Hirwa-plugin' );
  
 // display meta box form
 echo '<table>';
 echo '<tr>';
 echo '<td>' .__('Sku', 'Hirwa-plugin').':</td>
 <td><input type="text" name="Hirwa_product[sku]"
 value="'.esc_attr( $hiirwa_sku ).'" size="10"></td>';
 echo '</tr><tr>';
 echo '<td>' .__('Price', 'Hirwa-plugin').':</td>
 <td><input type="text" name="Hirwa_product[price]"
 value="'.esc_attr( $hiirwa_price ).'" size="5"></td>';
 echo '</tr><tr>';
 echo '<td>' .__('Weight', 'Hirwa-plugin').':</td>
 <td><input type="text" name="Hirwa_product[weight]"
 value="'.esc_attr( $hiirwa_weight ).'" size="5"></td>';
 echo '</tr><tr>';
 echo '<td>' .__('Color', 'Hirwa-plugin').':</td>
 <td><input type="text" name="Hirwa_product[color]"
 value="'.esc_attr( $hiirwa_color ).'" size="5"></td>';
 echo '</tr><tr>';
 echo '<td>Inventory:</td>
 <td>
 <select name="Hirwa_product[inventory]"
 id="Hirwa_product[inventory]">
 <option value="In Stock"'
 .selected( $hiirwa_inventory, 'In Stock', false )
 . '>' .__( 'In Stock', 'Hirwa-plugin' ). '</option>
 <option value="Backordered"'
 .selected( $hiirwa_inventory, 'Backordered', false )
 . '>' .__( 'Backordered', 'Hirwa-plugin' )
 . '</option>
 <option value="Out of Stock"'
 .selected( $hiirwa_inventory, 'Out of Stock', false )
 . '>' .__( 'Out of Stock', 'Hirwa-plugin' )
 . '</option>
 <option value="Discontinued"'
 .selected( $hiirwa_inventory, 'Discontinued', false )
 . '>' .__( 'Discontinued', 'Hirwa-plugin' )
 . '</option>
 </select></td>';
 echo '</tr>';
  
 //display the meta box shortcode legend section
 echo '<tr><td colspan="2"><hr></td></tr>';
 echo '<tr><td colspan="2"><strong>'
 .__( 'Shortcode Legend',
 'Hirwa-plugin' ).'</strong></td></tr>';

 echo '<tr><td>'
 .__( 'Sku', 'Hirwa-plugin' ) .':</td>
 <td>[hs show=sku]</td></tr>';
 echo '<tr><td>'
 .__( 'Price', 'Hirwa-plugin' ).':</td>
 <td>[hs show=price]</td></tr>';
 echo '<tr><td>'
 .__( 'Weight', 'Hirwa-plugin' ).':</td>
 <td>[hs show=weight]</td></tr>';
 echo '<tr><td>'
 .__( 'Color', 'Hirwa-plugin' ).':</td>
 <td>[hs show=color]</td></tr>';
 echo '<tr><td>'
 .__( 'Inventory', 'Hirwa-plugin' ).':</td>
 <td>[hs show=inventory]</td></tr>';
 echo '</table>';
}
// Action hook to save the meta box data when the post is saved
 add_action( 'save_post','Hirwa_store_save_meta_box' );
//save meta box data
 function Hirwa_store_save_meta_box( $post_id ) {
  
 //verify the post type is for Hirwa Products
 // and metadata has been posted
 if ( get_post_type( $post_id ) == 'Hirwa-products'
 && isset( $_POST['Hirwa_product'] ) ) {
  
 //if autosave skip saving data
 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
 return;
  
 //check nonce for security
 wp_verify_nonce( 'meta-box-save', 'Hirwa-plugin' );
 //store option values in a variable
 $Hirwa_product_data = $_POST['Hirwa_product'];
 //use array map function to sanitize option values
 $Hirwa_product_data =
 array_map( 'sanitize_text_field',
 $Hirwa_product_data );
 // save the meta box data as post metadata
 update_post_meta( $post_id, '_Hirwa_product_data',
 $Hirwa_product_data );
 }
  
}
 // Action hook to create the products shortcode
add_shortcode( 'hs', 'Hirwa_store_shortcode' );
  
//create shortcode
 function Hirwa_store_shortcode( $atts, $content = null ) {
 global $post;
  
 extract( shortcode_atts( array(
 "show" => ''
 ), $atts ) );
  
 //load options array
 $hiirwa_options_arr = get_option( 'Hirwa_options' );
  
 //load product data
 $hiirwa_product_data = get_post_meta( $post->ID,
 '_Hirwa_product_data', true );
  
 if ( $show == 'sku') {
 $hs_show = ( ! empty( $hiirwa_product_data['sku'] ) )
 ? $hiirwa_product_data['sku'] : '';
 }elseif ( $show == 'price' ) {
  
 $hs_show = $hiirwa_options_arr['currency_sign'];
 $hs_show = ( ! empty( $hiirwa_product_data['price'] ) )
 ? $hs_show . $hiirwa_product_data['price'] : '';
  
 }elseif ( $show == 'weight' ) {
 $hs_show = ( ! empty( $hiirwa_product_data['weight'] ) )
 ? $hiirwa_product_data['weight'] : '';
 }elseif ( $show == 'color' ) {
  
 $hs_show = ( ! empty( $hiirwa_product_data['color'] ) )
 ? $hiirwa_product_data['color'] : '';
  
 }elseif ( $show == 'inventory' ) {
 $hs_show = ( ! empty( $hiirwa_product_data['inventory'] ) )
 ? $hiirwa_product_data['inventory'] : '';
  
 }
  
 //return the shortcode value to display
 return $hs_show;
}
  
// Action hook to create plugin widget
 add_action( 'widgets_init', 'Hirwa_store_register_widgets' );
  
//register the widget
 function Hirwa_store_register_widgets() {
 	register_widget( 'hs_widget' );
  
}
//hs_widget class
 class hs_widget extends WP_Widget {
  
 //process our new widget
 function __construct() {
  
 $widget_ops = array(
 'classname' => 'hs-widget-class',
 'description' => __( 'Display Hirwa Products',
 'Hirwa-plugin' ) );
 parent::__construct( 'hs_widget', __( 'Products Widget',
 'Hirwa-plugin'), $widget_ops );
  
 }
 //build our widget settings form
 function form( $instance ) {
  
 $defaults = array(
 'title' =>
 __( 'Products', 'Hirwa-plugin' ),
 'number_products' => '3' );
  
 $instance = wp_parse_args( (array) $instance, $defaults );
 $title = $instance['title'];
 $number_products = $instance['number_products'];
 ?>
 <p><?php _e('Title', 'Hirwa-plugin') ?>:
 <input class="widefat" name="<?php
 echo $this->get_field_name( 'title' ); ?>"
 type="text" value="<?php
 echo esc_attr( $title ); ?>" /></p>
 <p><?php _e( 'Number of Products',
 'Hirwa-plugin' ) ?>:
 <input name="<?php
 echo $this->get_field_name( 'number_products' ); ?>"
 type="text" value="<?php
 echo absint( $number_products ); ?>"
 size="2" maxlength="2" />
 </p>
 <?php
 }
  
 //save our widget settings
 function update( $new_instance, $old_instance ) {
  
 $instance = $old_instance;
 $instance['title'] =
 sanitize_text_field( $new_instance['title'] );
 $instance['number_products'] =
 absint( $new_instance['number_products'] );
  
 return $instance;
  
 }
 //display our widget
 function widget( $args, $instance ) {
 global $post;
 extract( $args );
  
 echo $before_widget;
 $title = apply_filters( 'widget_title',
 $instance['title'] );
 $number_products = $instance['number_products'];
  
 if ( ! empty( $title ) ) {
 echo $before_title . esc_html( $title ) . $after_title;
 };
 //custom query to retrieve products
 $args = array(
 'post_type' => 'Hirwa-products',
 'posts_per_page' => absint( $number_products )
 );
  
 $dispProducts = new WP_Query();
 $dispProducts->query( $args );
  
 while ( $dispProducts->have_posts() ) :
 $dispProducts->the_post();
  
 //load options array
 $hiirwa_options_arr = get_option( 'Hirwa_options' );
 //load custom meta values
 $hiirwa_product_data =
 get_post_meta( $post->ID,
 '_Hirwa_product_data', true );
  
 $hs_price = ( ! empty( $hiirwa_product_data['price'] ) )
 ? $hiirwa_product_data['price'] : '';
 $hs_inventory = (
 ! empty( $hiirwa_product_data['inventory'] ) )
 ? $hiirwa_product_data['inventory'] : '';
 ?>
 <p>
 <a href="<?php the_permalink(); ?>"
 rel="bookmark"
 title="<?php the_title_attribute(); ?>
 Product Information">
 <?php the_title(); ?>
 </a>
 </p>
 <?php
 echo '<p>' .__( 'Price', 'Hirwa-plugin' )
 . ': '.$hiirwa_options_arr['currency_sign']
 .$hs_price .'</p>';
  
 //check if Show Inventory option is enabled
 if ( $hiirwa_options_arr['show_inventory'] ) {
  
 //display the inventory metadata for this product
 echo '<p>' .__( 'Stock', 'Hirwa-plugin' )
 . ': ' .$hs_inventory .'</p>';
  
 }
 echo '<hr>';
  
 endwhile;
 wp_reset_postdata();
  
 echo $after_widget;
 }
  
}

