<?php

add_action( 'product_cat_edit_form_fields', 'wp_taxonomy_edit_meta_field', 10, 2 );
function wp_taxonomy_edit_meta_field($term) {
  $t_id = $term->term_id;
  $term_meta = get_option( "taxonomy_$t_id" );
  $content = $term_meta['custom_term_meta'] ? wp_kses_post( $term_meta['custom_term_meta'] ) : '';
  $settings = array( 'textarea_name' => 'term_meta[custom_term_meta]' );
  ?>
  <tr class="form-field">
  <th scope="row" valign="top"><label for="term_meta[custom_term_meta]">Additional description</label></th>
  <td>
  <?php wp_editor( $content, 'product_cat_details', $settings ); ?>
  </td>
  </tr>
  <?php
}

add_action( 'edited_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
add_action( 'create_product_cat', 'save_taxonomy_custom_meta', 10, 2 );
function save_taxonomy_custom_meta( $term_id ) {
  if ( isset( $_POST['term_meta'] ) ) {
    $t_id = $term_id;
    $term_meta = get_option( "taxonomy_$t_id" );
    $cat_keys = array_keys( $_POST['term_meta'] );
    foreach ( $cat_keys as $key ) {
      if ( isset ( $_POST['term_meta'][$key] ) ) {
        $term_meta[$key] = wp_kses_post( stripslashes($_POST['term_meta'][$key]) );
      }
    }
    update_option( "taxonomy_$t_id", $term_meta );
  }
}
add_action( 'woocommerce_after_shop_loop', 'wp_product_cat_archive_add_meta' );
function wp_product_cat_archive_add_meta() {
  $t_id = get_queried_object()->term_id;
  $term_meta = get_option( "taxonomy_$t_id" );
  $term_meta_content = $term_meta['custom_term_meta'];
  if ( $term_meta_content != '' ) {
    if ( is_tax( array( 'product_cat', 'product_tag' ) ) && 0 === absint( get_query_var( 'paged' ) ) ) {
      echo '<div class="woo-sc-box normal rounded full">';
      echo apply_filters( 'the_content', $term_meta_content );
      echo '</div>';
    }
  }
}
