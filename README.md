# SVG Icons module for Drupal

Let you import svg in your twig template with custom class and attributes inspired by "Blade Icons" from "Laravel Blade UI kit" [https://github.com/blade-ui-kit/blade-icons](https://github.com/blade-ui-kit/blade-icons)

Currently only support one folder/default class settings.

## Configuration

In the configuration page you can define the folder containing your svgs files. You can use the theme twig filesystem loader syntax (@yourtheme or @yourmodule) to get path of the theme or module. For example if your svgs are in a folder svg at the root of your theme you can simply put ```@yourtheme/svg```. You can also put an absolute path that will be resolved by Drupal filesystem realpath function.

## Usage

Once configured you can call add svg from your twig template using the syntax
```
svg(string $iconName, string $class, array $attributes)
```
Icon name is the filename of the desired svg without the extension, class is an optional string for added classes and attributes is an optional array of additional attributes merged to the final svg element.
```
{{ svg('druplicon', 'myicon-class', { 'height': '40', 'width': '40' }) }}
```

## Import

From the backend page the icons can also be imported to a media library (which require svg support with either module svg_image_field or svg_image).

## TODO:

- Twig filter or media widget to add class/attributes to icons rendered from media library
- Add support for multiple collections (set of folder/default class like blade ui kit)
