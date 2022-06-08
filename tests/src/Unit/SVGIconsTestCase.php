<?php

namespace Drupal\Tests\svg_icons\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * SVG Icons unit test case.
 *
 * @group SVG Icons
 */
class SVGIconsTestCase extends UnitTestCase {

  /**
   * Load a fixture icon file return string content.
   *
   * @var string $name
   *  Name of the fixture icon file.
   */
  protected function loadFixtureIcon(string $name): string {
    $path = __DIR__ . './../../fixtures/' . $name . '.svg';
    return file_get_contents($path);
  }

}
