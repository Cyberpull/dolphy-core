<?php

namespace Dolphy\Core\Template;

interface PageInterface {

  public function getUrl();

  public function getTemplate();

  public function setTitle($title);

  public function setContent($content);

  public function setTemplate($template);

  /**
   * Get a WP_Post build using virtual Page object
   *
   * @return WP_Post
   */
  public function asWpPost();

}
