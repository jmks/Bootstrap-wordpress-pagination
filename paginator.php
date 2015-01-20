<?php

class Paginator {
  private static $dup_preferences = array(
    'first_last',
    'previous_next',
  );

  private $page;
  private $total_pages;
  private $range;
  private $show_single;
  private $show_dups;
  private $dup_preference;

  public function __construct($args) {
    $params = array_merge( $this->defaults(), $args );

    $this->page           = $params['page'];
    $this->total_pages    = $params['count'];
    $this->range          = $params['range'];
    $this->show_single    = $params['show_single_button'];
    $this->show_dups      = $params['show_dup_buttons'];
    $this->dup_preference = $params['dup_preference'];

    if (!$this->show_dups && !in_array( $this->dup_preference, self::$dup_preferences ))
      throw new InvalidArgumentException( "Hiding duplicate buttons requires a preference." );
  }

  private function defaults() {
    return array(
      'page'  => 1,
      'count' => 1,
      'range' => 4,
      'show_single_button' => TRUE,
      'show_dup_buttons'   => TRUE,
      'dup_preference'     => 'previous_next',
    );
  }

  public function any() {
    return ( $this->total_pages <= 1 ) ? $this->show_single : TRUE;
  }

  private function dup_pref($preference) {
    return $this->dup_preference === $preference;
  }

  public function first_page() {
    if ( $this->page == 1 ) {
      return FALSE;
    } elseif ( !$this->show_dups && $this->page == 2 ) {
      return $this->dup_pref( 'first_last' );
    } else {
      return TRUE;
    }
  }

  public function last_page() {
    if ( $this->page == $this->total_pages ) {
      return FALSE;
    } elseif ( !$this->show_dups && $this->total_pages == $this->page + 1 ) {
      return $this->dup_pref( 'first_last' );
    } else {
      return TRUE;
    }
  }

  public function previous_page() {
    if ( $this->page <= 1 ) {
      return FALSE;
    } elseif ( !$this->show_dups && $this->page == 2 ) {
      return $this->dup_pref( 'previous_next' );
    } else {
      return TRUE;
    }
  }

  public function next_page() {
    if ($this->page == $this->total_pages) {
      return FALSE;
    } elseif ( $this->page + 1 == $this->total_pages ) {
      return $this->dup_pref( 'previous_next' );
    } else {
      return TRUE;
    }
  }

  private function numbered_range() {
    $start = max( $this->page - $this->range, 1 );
    $end   = min( $this->page + $this->range, $this->total_pages );

    return range( $start, $end );
  }

  public function numbered_pages() {
    return new ArrayIterator( $this->numbered_range() );
  }
}

?>
