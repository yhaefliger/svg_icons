<?php

namespace Drupal\Tests\svg_icons\Unit;

use Drupal\svg_icons\SVGIcon;

/**
 * @coversDefaultClass \Drupal\svg_icons\SVGIcon
 *
 * @group SVG Icons
 */
class SVGIconTest extends SVGIconsTestCase {

  /**
   * Testing output of the SVG icon.
   *
   * @covers ::__toString
   */
  public function testSvgCanOutput() {
    $svg = new SVGIcon('<svg></svg>');
    $this->assertEquals('<svg></svg>', (string) $svg);
  }

  /**
   * Testing svg cleanup of eventual <?xml header.
   *
   * @covers ::cleanSvgContents
   */
  public function testSvgIsCleaned() {
    $svg = new SVGIcon('<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <svg></svg>');
    $this->assertEquals('<svg></svg>', (string) $svg);

    $inkscape_basic = new SVGIcon($this->loadFixtureIcon('inkscape_basic'));
    $this->assertStringNotContainsString('<?xml', (string) $inkscape_basic);
    $this->assertStringStartsWith('<svg', (string) $inkscape_basic);
  }

  /**
   * Testing svg attributes addclas method.
   *
   * @covers ::addClass
   */
  public function testSvgCanManageClasses() {
    $svg = new SVGIcon('<svg class="firstclass"></svg>');

    // Initial class is keeped.
    $this->assertStringStartsWith('<svg class="firstclass"', (string) $svg);
    // Add second class.
    $svg->addClass('secondclass');
    $this->assertStringStartsWith('<svg class="firstclass secondclass"', (string) $svg);
    // Check no duplicates classes.
    $svg->addClass('firstclass');
    $svg->addClass('secondclass');
    $this->assertStringStartsWith('<svg class="firstclass secondclass"', (string) $svg);

    $inkscape_basic = new SVGIcon($this->loadFixtureIcon('inkscape_basic'));
    $inkscape_basic->addClass('firstclass');
    $this->assertStringStartsWith('<svg class="firstclass"', (string) $inkscape_basic);
  }

}
