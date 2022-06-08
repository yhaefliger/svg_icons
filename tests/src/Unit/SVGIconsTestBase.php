<?php

namespace Drupal\Tests\svg_icons\Unit;

use Drupal\Tests\UnitTestCase;

class SVGIconsTestBase extends UnitTestCase
{

  protected function loadFixtureIcon($name)
  {
    $path = __DIR__ . './../../fixtures/' . $name . '.svg';
    return file_get_contents($path);
  }
}
