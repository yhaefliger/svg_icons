<?php

namespace Drupal\svg_icons_graphql\Plugin\GraphQL\Fields\SVG;

use Drupal\file\FileInterface;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use Drupal\svg_icons\SVGIcon;
use Drupal\svg_image_field\Plugin\Field\FieldType\SvgImageFieldItem;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Retrieve the image url.
 *
 * @GraphQLField(
 *   id = "svg_file_render",
 *   secure = true,
 *   name = "render",
 *   type = "String",
 *   provider = "file",
 *   arguments = {
 *     "class" = "String",
 *     "height" = "String",
 *     "width" = "String"
 *   },
 *   field_types = {"svg_image_field"},
 *   deriver = "Drupal\graphql_core\Plugin\Deriver\Fields\EntityFieldPropertyDeriver"
 * )
 */
class SVGFileRender extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($value instanceof SvgImageFieldItem && $value->entity && $value->entity->access('view')) {
      // Defined width / height.
      $attributes = [];
      if (isset($args['width']) && !empty($args['width'])) {
        $attributes['width'] = $args['width'];
      }
      if (isset($args['height']) && !empty($args['height'])) {
        $attributes['height'] = $args['height'];
      }

      $svg = new SVGIcon(file_get_contents($value->entity->getFileUri()), $attributes);

      // Add classes.
      if (isset($args['class']) && !empty($args['class'])) {
        $classes = explode(' ', $args['class']);
        foreach ($classes as $class) {
          $svg->addClass($class);
        }
      }
      yield (string) $svg;
    }
  }

}
