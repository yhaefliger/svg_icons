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
    $assert_session = $this->assertSession();
    $assert_session->responseNotContains('No media bundles can handle svg file format');
    $assert_session->fieldExists('path');
    $assert_session->fieldExists('media_bundle');
    $this->submitForm([], "Import icons into media", "svg-icons-import-form");

    $this->drupalGet('/admin/content/media');
    $assert_session = $this->assertSession();
    // Media type check.
    $assert_session->responseContains('>svg_icon</td>');
    // Media Status check.
    $assert_session->responseContains('<td>Published</td>');
    // SVG Icon check.
    $assert_session->responseContains('>druplicon.svg</a>');
  }

}
