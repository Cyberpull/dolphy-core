<?php

namespace Dolphy\Core\Template;

use WP;

interface ControllerInterface {

  /**
   * Initialize the controller, fires the hook that allows addition of virtual pages
   */
  public static function init();

  /**
   * Register a page object in the controller
   *
   * @param Page $page
   * @return Page
   */
  public function addPage(string $url, $title = 'Untitled', $template = 'page.php');

  /**
   * Run on 'do_parse_request' and if the request is for one of the registered pages
   * setup global variables, fire core hooks, requires page template and exit.
   *
   * @param boolean $bool The boolean flag value passed by 'do_parse_request'
   * @param WP $wp The global wp object passed by 'do_parse_request'
   */
  public function dispatch($bool, WP $wp);

}
