<?php

namespace Dolphy\Core\Template;

use WP;
use SplObjectStorage;
use WP_Query;
use WP_Post;

class Controller implements ControllerInterface {

  private $pages;
  private $loader;
  private $matched;

  function __construct(LoaderInterface $loader) {
    $this->pages = new SplObjectStorage;
    $this->loader = $loader;
  }

  static function init() {
    $ctrl = new self(new Loader);

    add_action('init', function () use (&$ctrl) {
      do_action('dolphy_template', $ctrl);
    });

    add_filter('do_parse_request', array($ctrl, 'dispatch'), PHP_INT_MAX, 2);

    add_action('loop_end', function(WP_Query $query) {
      if (isset($query->virtual_page) && !empty($query->virtual_page)) {
        $query->virtual_page = NULL;
      }
    });

    add_filter('the_permalink', function($plink) {
      global $post, $wp_query;

      $isVirtualPage = (
        $wp_query->is_page && isset($wp_query->virtual_page) &&
        $wp_query->virtual_page instanceof Page &&
        isset($post->is_virtual) && $post->is_virtual
      );

      if ($isVirtualPage) {
        $plink = home_url($wp_query->virtual_page->getUrl());
      }

      return $plink;
    });
  }

  function addPage(string $url, $title = 'Untitled', $template = 'page.php') {
    $page = new Page($url, $title, $template);
    $this->pages->attach($page);
    return $page;
  }

  function dispatch($bool, WP $wp) {
    if ($this->checkRequest() && $this->matched instanceof Page) {
      $this->loader->init($this->matched);
      $wp->virtual_page = $this->matched;
      do_action('parse_request', $wp);

      $this->setupQuery();
      do_action('wp', $wp);

      $this->loader->load();
      $this->handleExit();
    }

    return $bool;
  }

  private function checkRequest() {
    $this->pages->rewind();

    list($path) = explode('?', $this->getPathInfo());
    $path = trim($path, '/');

    while($this->pages->valid()) {
      $dpath = trim($this->pages->current()->getUrl(), '/');

      if ($dpath === $path) {
        $this->matched = $this->pages->current();
        return true;
      }

      $this->pages->next();
    }

    return false;
  }

  private function getPathInfo() {
    $home_path = parse_url(home_url(), PHP_URL_PATH);
    return preg_replace("#^/?{$home_path}/#", '/', esc_url(add_query_arg(array())));
  }

  private function setupQuery() {
    global $wp_query;

    $wp_query->init();
    $wp_query->is_page = true;
    $wp_query->is_singular = true;
    $wp_query->is_home = false;
    $wp_query->found_posts = 1;
    $wp_query->post_count = 1;
    $wp_query->max_num_pages = 1;
    $posts = (array)apply_filters('the_posts', array($this->matched->asWpPost()), $wp_query);
    $post = $posts[0];
    $wp_query->posts = $posts;
    $wp_query->post = $post;
    $wp_query->queried_object = $post;
    $GLOBALS['post'] = $post;
    $wp_query->virtual_page = ($post instanceof WP_Post && isset($post->is_virtual)) ? $this->matched : NULL;
  }

  public function handleExit() {
    exit();
  }

}
