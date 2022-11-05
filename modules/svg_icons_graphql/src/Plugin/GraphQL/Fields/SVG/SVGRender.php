<?php

namespace Drupal\svg_icons_graphql\Plugin\GraphQL\Fields\SVG;

use Drupal\file\FileInterface;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use Drupal\svg_icons\SVGIcon;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Global file svg render based on mimetype.
 *
 * @GraphQLField(
 *   id = "svg_render",
 *   secure = true,
 *   name = "svgRender",
 *   type = "String",
 *   parents = {"File"},
 *   arguments = {
 *     "class" = "String",
 *     "height" = "String",
 *     "width" = "String"
 *   }
 * )
 */
class SVGRender extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    /** @var FileInterface $value */
    $render = '';

    if ($value->getMimeType() == 'image/svg+xml') {
      // Defined width / height.
      $attributes = [];
      if (isset($args['width']) && !empty($args['width'])) {
        $attributes['width'] = $args['width'];
      }
      if (isset($args['height']) && !empty($args['height'])) {
        $attributes['height'] = $args['height'];
      }

      $svg = new SVGIcon(file_get_contents($value->getFileUri()), $attributes);

      // Add classes.
      if (isset($args['class']) && !empty($args['class'])) {
        $classes = explode(' ', $args['class']);
        foreach ($classes as $class) {
          $svg->addClass($class);
        }
      }

      $render = (string) $svg;
    }

    yield $render;
  }

}
