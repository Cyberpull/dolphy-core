<?php

namespace Dolphy\Core\Template;

interface LoaderInterface {

  /**
   * Setup loader for a page objects
   *
   * @param PageInterface $page Matched virtual page
   */
  public function init(PageInterface $page);

  /**
   * Trigger core and custom hooks to filter templates,
   * then load the found template.
   */
  public function load();

}
