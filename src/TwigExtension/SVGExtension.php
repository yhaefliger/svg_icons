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
   * @param \Drupal\svg_icons\Utils $utils
   *   SVG Utils.
   */
  public function __construct(Utils $utils) {
    $this->utils = $utils;
  }

  /**
   * {@inheritDoc}
   */
  public function getName(): string {
    return 'svg_icons.funcion';
  }

  /**
   * {@inheritDoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction(
        'svg',
        [$this, 'svg'],
        ['is_safe' => ['html']]
      )
    ];
  }

  /**
   * Return the SVG markup.
   *
   * @param string $fullpath
   *   SVG icon name with optional path.
   * @param string $class
   *   CSS string for classes to add.
   * @param array $attributes
   *   Array with other attributes.
   *
   * @return string
   *   SVG icon markup.
   */
  public function svg(string $fullpath, string $class = '', array $attributes = []): string {
    try {
      $paths = explode('/', $fullpath);
      $name = array_pop($paths);

      // Path provided in the fullpath.
      if (count($paths) > 0) {
        $path = $this->utils->getRealPath(implode('/', $paths));
      }
      // Use the default path from config.
      else {
        $path = $this->utils->getPath();
      }

      if ($path) {
        if (substr($name, -4) !== '.svg') {
          $name .= ".svg";
        }
        $filename = $path . '/' . $name;
        if (file_exists($filename)) {
          $content = file_get_contents($filename);
          $icon = new SVGIcon($content, $attributes);
          $icon->addClass($this->utils->defaultClass());
          $icon->addClass($class);

          return $icon;
        } else {
          throw new \Exception("SVG File $filename not found.");
        }
      } else {
        throw new \Exception("SVG Path not found for icon $name.");
      }
    } catch (\Exception $e) {
      return $fullpath;
    }
  }
}
