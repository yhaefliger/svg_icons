<?php

namespace Drupal\Tests\svg_icons\Functional;

/**
 * Tests svg icons import.
 *
 * @group SVG Icons
 */
class SVGIconImportTest extends SVGIconFunctionalTestBase {

  /**
   * Test import of SVG Icons.
   */
  public function testImportOfSvgIcons() {
    $this->drupalGet('/admin/config/media/svg-icons/import');
    $this->assertSession()->statusCodeEquals(403);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/media/svg-icons/import');
    $assert_session = $this->assertSession();
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains('No media bundles can handle svg file format');

    $this->createMediaType();
    $this->drupalGet('/admin/config/media/svg-icons/import');
    $this->assertSession()->responseNotContains('No media bundles can handle svg file format');

  }

}
