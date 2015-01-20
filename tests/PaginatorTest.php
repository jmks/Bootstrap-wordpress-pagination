<?php

require_once 'paginator.php';

class PaginatorTest extends PHPUnit_Framework_TestCase {

  /**
   * @expectedException     InvalidArgumentException
   */
  public function testConstructorThrowsExcecptionWhenHidingDupButtonsWithUnknownPreference() {
    $pager = new Paginator( array(
      'show_dup_buttons' => FALSE,
      'dup_preference'   => 'unknown_preference'
    ));
  }

  public function testSinglePage() {
    $shown_args = array(
      'page'  => 1,
      'count' => 1,
      'show_single_button' => TRUE,
    );
    $hidden_args = array_merge( $shown_args, array(
      'show_single_button' => FALSE
    ));

    $shown  = new Paginator( $shown_args  );
    $hidden = new Paginator( $hidden_args );

    $this->assertTrue( $shown->any() );
    $this->assertFalse( $shown->first_page() );
    $this->assertFalse( $shown->previous_page() );
    $this->assertFalse( $shown->next_page() );
    $this->assertFalse( $shown->last_page() );

    $this->assertFalse( $hidden->any() );
    $this->assertFalse( $hidden->first_page() );
    $this->assertFalse( $hidden->previous_page() );
    $this->assertFalse( $hidden->next_page() );
    $this->assertFalse( $hidden->last_page() );
  }

  public function testOnFirstPage() {
    $first_page = new Paginator( array(
      'page'  => 1,
      'count' => 10
    ));

    $this->assertTrue( $first_page->any() );
    $this->assertFalse( $first_page->first_page() );
    $this->assertFalse( $first_page->previous_page() );
    $this->assertTrue( $first_page->next_page() );
    $this->assertTrue( $first_page->last_page() );
  }

  public function testOnSecondPage() {
    $args = array(
      'page'  => 2,
      'count' => 10,
    );

    $dups = new Paginator( array_merge($args, array(
      'show_dup_buttons' => TRUE
    )));
    $dups_first = new Paginator( array_merge($args, array(
      'show_dup_buttons' => FALSE,
      'dup_preference'   => 'first_last'
    )));
    $dups_prev = new Paginator( array_merge($args, array(
      'show_dup_buttons' => FALSE,
      'dup_preference'   => 'previous_next'
    )));

    // all buttons shown
    $this->assertTrue( $dups->any() );
    $this->assertTrue( $dups->first_page(), 'first shown on page 2 by default' );
    $this->assertTrue( $dups->previous_page() );
    $this->assertTrue( $dups->next_page() );
    $this->assertTrue( $dups->last_page() );

    // first/last preferred
    $this->assertTrue( $dups_first->any() );
    $this->assertTrue( $dups_first->first_page() );
    $this->assertFalse( $dups_first->previous_page() );
    $this->assertTrue( $dups_first->next_page() );
    $this->assertTrue( $dups_first->last_page() );

    // previous/next preferred
    $this->assertTrue( $dups_prev->any() );
    $this->assertFalse( $dups_prev->first_page() );
    $this->assertTrue( $dups_prev->previous_page() );
    $this->assertTrue( $dups_prev->next_page() );
    $this->assertTrue( $dups_prev->last_page() );
  }

  public function testOnMidpage() {
    $pager = new Paginator( array(
      'page'  => 5,
      'count' => 10,
    ));

    $this->assertTrue( $pager->any() );
    $this->assertTrue( $pager->first_page() );
    $this->assertTrue( $pager->previous_page() );
    $this->assertTrue( $pager->next_page() );
    $this->assertTrue( $pager->last_page() );
  }

  public function testOnPenultimatePage() {
    $args = array(
      'page'  => 9,
      'count' => 10,
    );

    $dups = new Paginator( array_merge($args, array(
      'show_dup_buttons' => TRUE
    )));
    $dups_first = new Paginator( array_merge($args, array(
      'show_dup_buttons' => FALSE,
      'dup_preference'   => 'first_last'
    )));
    $dups_prev = new Paginator( array_merge($args, array(
      'show_dup_buttons' => FALSE,
      'dup_preference'   => 'previous_next'
    )));

    // all buttons shown
    $this->assertTrue( $dups->any() );
    $this->assertTrue( $dups->first_page() );
    $this->assertTrue( $dups->previous_page() );
    $this->assertTrue( $dups->next_page() );
    $this->assertTrue( $dups->last_page() );

    // first/last preferred
    $this->assertTrue( $dups_first->any() );
    $this->assertTrue( $dups_first->first_page() );
    $this->assertTrue( $dups_first->previous_page() );
    $this->assertFalse( $dups_first->next_page() );
    $this->assertTrue( $dups_first->last_page() );

    // previous/next preferred
    $this->assertTrue( $dups_prev->any() );
    $this->assertTrue( $dups_prev->first_page() );
    $this->assertTrue( $dups_prev->previous_page() );
    $this->assertTrue( $dups_prev->next_page() );
    $this->assertFalse( $dups_prev->last_page() );
  }

  public function testOnLastPage() {
    $pager = new Paginator( array(
      'page'  => 10,
      'count' => 10,
    ));

    $this->assertTrue( $pager->any() );
    $this->assertTrue( $pager->first_page() );
    $this->assertTrue( $pager->previous_page() );
    $this->assertFalse( $pager->next_page() );
    $this->assertFalse( $pager->last_page() );
  }

  private function assertNumberedRange($expected, $page, $pages, $range) {
    $p = new Paginator( array(
      'page' => $page,
      'count' => $pages,
      'range' => $range
    ));

    $numbered_pages = iterator_to_array( $p->numbered_pages() );

    $this->assertEquals( $expected, $numbered_pages );
  }

  public function testNumberedPages() {
    $this->assertNumberedRange( array(1), 1, 1, 3 );
    $this->assertNumberedRange( array(1, 2, 3, 4, 5), 1, 10, 4 );
    $this->assertNumberedRange( array(1, 2, 3, 4, 5), 2, 10, 3 );
    $this->assertNumberedRange( array(3, 4, 5, 6, 7), 5, 10, 2 );
    $this->assertNumberedRange( array(6, 7, 8, 9, 10), 8, 10, 2 );
    $this->assertNumberedRange( array(9, 10), 10, 10, 1 );
  }
}

?>
