<?php
/**
 * WordPress Bootstrap Pagination
 * Modified from https://github.com/talentedaamer/Bootstrap-wordpress-pagination
 */

 require_once( 'paginator.php' );

 function wp_bootstrap_pagination( $args = array() ) {

    $defaults = array(
        'custom_query'      => FALSE,
        'previous_string'   => __( '<i class="glyphicon glyphicon-chevron-left"></i>', 'text-domain' ),
        'next_string'       => __( '<i class="glyphicon glyphicon-chevron-right"></i>', 'text-domain' ),
        'before_output'     => '<div class="post-nav"><ul class="pagination">',
        'after_output'      => '</ul></div>',
        'pad_page_numbers'   => TRUE,

        // Paginator params
        'range'             => 4,
        'show_single_button' => FALSE,
        'show_dup_buttons'   => FALSE,
        'dup_preference'     => 'previous_next',
    );

    $args = wp_parse_args(
        $args,
        apply_filters( 'wp_bootstrap_pagination_defaults', $defaults )
    );

    // update args with page and count
    if ( !$args['custom_query'] )
      $args['custom_query'] = @$GLOBALS['wp_query'];
    $args['count'] = (int) $args['custom_query']->max_num_pages;
    $args['page']  = max( 1, intval( get_query_var( 'paged', 1 ) ) );

    $pager = new Paginator($args);

    if ( ! $pager->any() ) return FALSE;

    $echo = '';

    $firstpage_link = esc_attr( get_pagenum_link( 1 ) );
    if ( $pager->first_page() && $firstpage_link )
      $echo .= '<li><a href="' . $firstpage_link . '">' . __( 'First', 'text-domain' ) . '</a></li>';

    $previous_link = esc_attr( get_pagenum_link( intval($args['page']) - 1 ) );
    if ( $pager->previous_page() && $previous_link )
        $echo .= '<li class="previous"><a href="' . $previous_link . '" title="' . __( 'previous', 'text-domain') . '">' . $args['previous_string'] . '</a></li>';

    $padding = strlen( (string)$args['count'] ) + 1;
    foreach ( $pager->numbered_pages() as $page_num ) {
      $button_text = (string)$page_num;

      if ( $args['pad_page_numbers'] )
        $button_text = str_pad( (int)$page_num, $padding, '0', STR_PAD_LEFT );

      if ( $page_num == $args['page'] ) {
        $echo .= '<li class="active"><span class="active">' . $button_text . '</span></li>';
      } else {
        $echo .= sprintf( '<li><a href="%s">%s</a></li>', esc_attr( get_pagenum_link( $page_num ) ), $button_text );
      }
    }

    $next_link = esc_attr( get_pagenum_link( intval($args['page']) + 1 ) );
    if ( $pager->next_page() && $next_link )
        $echo .= '<li><a href="' . $next_link . '" title="' . __( 'next', 'text-domain') . '">' . $args['next_string'] . '</a></li>';

    $lastpage_link = esc_attr( get_pagenum_link( $args['count'] ) );
    if ( $pager->last_page() && $lastpage_link )
        $echo .= '<li class="next"><a href="' . $lastpage_link . '">' . __( 'Last', 'text-domain' ) . '</a></li>';

    if ( isset($echo) )
        echo $args['before_output'] . $echo . $args['after_output'];
}
