<?php

namespace Drupal\Tests\svg_icons\Unit;

use Drupal\svg_icons\SVGIcon;

class SVGIconTest extends SVGIconsTestBase
{

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void
  {
    parent::setUp();
  }

  public function testSVGCanOutput()
  {
    $svg = new SVGIcon('<svg></svg>');
    $this->assertEquals('<svg></svg>', (string) $svg);
  }

  public function testSVGIsCleaned()
  {
    $svg = new SVGIcon('<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <svg></svg>');
    $this->assertEquals('<svg></svg>', (string) $svg);

    $inkscape_basic = new SVGIcon($this->loadFixtureIcon('inkscape_basic'));
    $this->assertStringNotContainsString('<?xml', (string) $inkscape_basic);
    $this->assertStringStartsWith('<svg', (string) $inkscape_basic);
  }

  public function testSVGCanManageClasses()
  {
    $svg = new SVGIcon('<svg class="firstclass"></svg>');
    //initial class is keeped
    $this->assertStringStartsWith('<svg class="firstclass"', (string) $svg);
    //add second class
    $svg->addClass('secondclass');
    $this->assertStringStartsWith('<svg class="firstclass secondclass"', (string) $svg);
    //check no duplicates classes
    $svg->addClass('firstclass');
    $svg->addClass('secondclass');
    $this->assertStringStartsWith('<svg class="firstclass secondclass"', (string) $svg);

    $inkscape_basic = new SVGIcon($this->loadFixtureIcon('inkscape_basic'));
    $inkscape_basic->addClass('firstclass');
    $this->assertStringStartsWith('<svg class="firstclass"', (string) $inkscape_basic);
  }
}
