<?php

namespace Drupal\Tests\svg_icons\Functional;

use Drupal\media\Entity\MediaType;
use Drupal\Tests\BrowserTestBase;

/**
 * Provides a base class for SVG icon functional tests.
 *
 * @group SVG Icons
 */
abstract class SVGIconFunctionalTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'media',
    'field_ui',
    'menu_ui',
    'svg_image_field',
    'svg_icons',
  ];

  /**
   * Media Type.
   *
   * @var \Drupal\media\MediaTypeInterface
   */
  protected $mediaType;

  /**
   * Media Source Type field.
   *
   * @var string
   */
  protected $mediaSourceFieldName;

  /**
   * An admin test user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * A non-admin test user account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $nonAdminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'access media overview',
      'administer media',
      'administer media fields',
      'administer media form display',
      'administer media display',
      'administer media types',
      'view media',
      'import svg icons to media library',
    ]);
    $this->nonAdminUser = $this->drupalCreateUser([]);
  }

  /**
   * Create the media type.
   */
  protected function createMediaType() {
    $this->mediaType = MediaType::create([
      'id' => 'svg_icon',
      'label' => 'svg_icon',
      'description' => 'SVG Icon test.',
      'source' => 'svg',
    ]);
    $this->mediaType->save();
    $source_field = $this->mediaType->getSource()->createSourceField($this->mediaType);
    $source_field->getFieldStorageDefinition()->save();
    $source_field->save();
    $this->mediaType->set('source_configuration', [
      'source_field' => $source_field->getName(),
    ])->save();
    $this->mediaSourceFieldName = $source_field->getName();
  }

}
