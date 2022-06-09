<?php

namespace Drupal\Tests\svg_icons\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Test Twig icons render.
 *
 * @group SVG Icons
 */
class TwigRenderTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['system', 'svg_icons', 'svg_icons_test'];

  /**
   * Default config class.
   *
   * @var string
   */
  protected $defaultClass;

  /**
   * Empty test svg path (<svg></svg>).
   *
   * @var string
   */
  protected $emptyIcon = '@svg_icons_test/test_icons/empty';

  /**
   * Twig environment.
   *
   * @var \Drupal\Core\Template\TwigEnvironment
   * */
  protected $environment;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['svg_icons']);

    $settings = $this->container->get('config.factory')->get('svg_icons.settings');
    $this->defaultClass = $settings->get('default_class');

    $this->environment = \Drupal::service('twig');
  }

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    parent::register($container);

    $definition = new Definition('Twig\Loader\FilesystemLoader', [[sys_get_temp_dir()]]);
    $definition->setPublic(TRUE);
    $container->setDefinition('twig_loader__file_system', $definition)
      ->addTag('twig.loader');
  }

  /**
   * Tests twig render configured icons.
   */
  public function testRenderIcons() {
    $this->assertStringStartsWith(
      '<svg',
      $this->environment->renderInline('{{ svg(\'druplicon\') }}')
    );
  }

  /**
   * Test inexistant icon return fallback icon name.
   */
  public function testInexistantIcon() {
    $this->assertEquals(
      'inexistant',
      $this->environment->renderInline('{{ svg(\'inexistant\') }}')
    );
  }

  /**
   * Test if it can render from anoter module.
   */
  public function testRenderIconFormOtherModule() {
    $this->assertEquals(
      sprintf('<svg class="%s"></svg>', $this->defaultClass),
      $this->environment->renderInline(sprintf('{{ svg(\'%s\') }}', $this->emptyIcon))
    );
  }

  /**
   * Test it can add classes to the icon.
   */
  public function testRenderIconWithClasses() {
    $this->assertEquals(
      sprintf('<svg class="%s %s"></svg>', $this->defaultClass, 'test-class'),
      $this->environment->renderInline(sprintf('{{ svg(\'%s\', \'test-class\') }}', $this->emptyIcon))
    );
  }

  /**
   * Test it can render icon with attributes.
   */
  public function testRederIconAttributes() {
    $this->assertEquals(
      sprintf('<svg test="ok" class="%s"></svg>', $this->defaultClass),
      $this->environment->renderInline(
        sprintf('{{ svg(\'%s\', \'\', {\'test\': \'ok\'}) }}', $this->emptyIcon)
      )
    );
  }

}
