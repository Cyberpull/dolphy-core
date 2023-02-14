<?php

namespace Dolphy\Core\Template;

class Loader implements LoaderInterface {

  private $templates = [];

  public function init(PageInterface $page) {
    $templates = array("dolphy/{$page->getTemplate()}");
    $this->templates = wp_parse_args(array('page.php', 'index.php'), $templates);
  }

  public function load() {
    do_action('template_redirect');

    $template = $this->locate(array_filter($this->templates));
    $filtered = apply_filters('template_include', apply_filters('virtual_page_template', $template));

    if (empty($filtered) || is_file($filtered)) {
      $template = $filtered;
    }

    if (!empty($template) && is_file($template)) {
      require_once $template;
    }
  }

  public function locate(array $templates) {
    foreach ($templates as $template) {
      if (substr($template, 0, 7) === 'dolphy/') {
        $template = substr($template, 7);
        $filename = DOLPHY_TEMPLATES."/{$template}";
        if (is_file($filename)) return $filename;
      }
    }

    return locate_template($templates);
  }

}
