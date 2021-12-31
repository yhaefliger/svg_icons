<?php

namespace Drupal\svg_icons;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Template\Loader\FilesystemLoader;

/**
 * Utility class for SVG icons.
 */
class Utils {


  /**
   * Drupal FileSystem service.
   *
   * @var Drupal\Core\File\FileSystem
   */
  protected $filesystem;

  /**
   * Twig loader.
   *
   * @var Drupal\Core\Template\Loader\FilesystemLoader
   */
  protected $loader;

  /**
   * Immutable config object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Class Constructor injecting FileSystem and Twig Loader.
   *
   * @param \Drupal\Core\File\FileSystem $filesystem
   *   FileSystem service.
   * @param \Drupal\Core\Template\Loader\FilesystemLoader $loader
   *   Twig loader.
   */
  public function __construct(FileSystem $filesystem, FilesystemLoader $loader) {
    $this->filesystem = $filesystem;
    $this->loader = $loader;
    $this->config = \Drupal::config('svg_icons.settings');
  }

  /**
   * Get SVG icon default class from config.
   *
   * @return string
   *   SVG icon.
   */
  public function defaultClass() {
    return $this->config->get('default_class');
  }

  /**
   * Get icons path from config.
   *
   * @param null|string $path
   *   Relative path to icons.
   *
   * @return null|string
   *   The full path or null if not found.
   */
  public function getPath($path = NULL) {
    $path = $path ? $path : $this->config->get('path');
    $realpath = NULL;

    if (!empty($path)) {
      // Twig loader replace @theme/@module with template dir.
      if (substr($path, 0, 1) == '@') {
        $folders = explode('/', $path);
        $base = array_shift($folders);
        $base_path = str_replace('/templates', '', $this->loader->getPaths(str_replace('@', '', $base)));
        if (!empty($base_path)) {
          $path = current($base_path) . '/' . implode('/', $folders);
        }
        else {
          throw new \Exception('Template path not found' . $base);
        }
      }

      $realpath = $this->filesystem->realpath($path);
      if (!$realpath) {
        throw new \Exception('Path not found ' . $path);
      }
    }
    else {
      throw new \Exception('Empty svg path configured');
    }

    return $realpath;
  }

}
