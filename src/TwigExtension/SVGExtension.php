<?php

namespace Drupal\svg_icons\TwigExtension;

use Drupal\svg_icons\SVGIcon;
use Drupal\svg_icons\Utils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * TWIG extension for SVG icons.
 */
class SVGExtension extends AbstractExtension {

  /**
   * Shared SVG Utils.
   *
   * @var \Drupal\svg_icons\Utils
   */
  protected $utils;

  /**
   * Class Constructor injecting SVG Utils.
   *
   * @param Utils $utils
   *   SVG Utils.
   */
  public function __construct(Utils $utils) {
    $this->utils = $utils;
  }

  /**
   * {@inheritDoc}
   */
  public function getName() {
    return 'svg_icons.funcion';
  }

  /**
   * {@inheritDoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('svg', [$this, 'svg'], ['is_safe' => ['html']])
    ];
  }

  /**
   * Return the SVG markup.
   *
   * @param string $name
   *   SVG icon name.
   * @param string $class
   *   CSS string for classes to add.
   * @param array $attributes
   *   Array with other attributes.
   *
   * @return string
   *   SVG icon markup.
   */
  public function svg(string $name, string $class = '', array $attributes = []): string {
    $path = $this->utils->getPath();
    if ($path) {
      $filename = $path . '/' . $name . '.svg';
      if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $icon = new SVGIcon($content, $attributes);
        $icon->addClass($this->utils->defaultClass());
        $icon->addClass($class);

        return $icon;
      }
      else {
        return '';
      }
    }
    else {
      return '';
    }
  }

}
