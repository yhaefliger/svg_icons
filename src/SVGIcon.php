<?php

namespace Drupal\svg_icons;

use Drupal\Core\Template\Attribute;

/**
 * SVG icon class.
 */
class SVGIcon {

  /**
   * Raw string content.
   *
   * @var string
   */
  protected $content;

  /**
   * SVG dom document attriubtes.
   *
   * @var \Drupal\Core\Template\Attribute
   */
  protected $attributes;

  /**
   * Create a new SVG icon.
   *
   * @param string $content
   *   Raw string content.
   * @param array $attributes
   *   SVG dom document attriubtes.
   */
  public function __construct(string $content, array $attributes = []) {
    $this->content = $this->cleanSvgContents($content);
    $this->attributes = new Attribute($attributes);

    // Remove actual svg class and add it to object attributes.
    preg_match('/<svg.*?(class=["\'](.*?)["\']).*?>.*?<\/svg>/', $this->content, $hasClass);
    if (!empty($hasClass) && isset($hasClass[2])) {
      $this->content = preg_replace('/' . $hasClass[1] . '/', '', $this->content);
      $this->attributes->addClass($hasClass[2]);
    }
  }

  /**
   * Add a css class to the Attributes.
   *
   * @param string $class
   *   CSS class to add.
   */
  public function addClass($class) {
    $this->attributes->addClass($class);
  }

  /**
   * Return rendered SVG icon HTML element.
   *
   * @return string
   *   SVG icon HTML element.
   */
  public function __toString() {
    return str_replace(
      '<svg',
      sprintf('<svg%s', $this->attributes),
      $this->content
    );
  }

  /**
   * Cleanup svg raw content.
   *
   * @param string $content
   *   Raw svg content.
   *
   * @return string
   *   Cleaned svg content.
   */
  private function cleanSvgContents(string $content): string {
    return trim(preg_replace('/^(<\?xml.+?\?>)/', '', $content));
  }

}
