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
   * @var \Drupal\Core\File\FileSystem
   */
  protected $filesystem;

  /**
   * Twig loader.
   *
   * @var \Drupal\Core\Template\Loader\FilesystemLoader
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
   * @return string|false
   *   The full path or null if not found.
   */
  public function getPath($path = NULL) {
    $path = $path ? $path : $this->config->get('path');

    if (!empty($path)) {
      return $this->getRealPath($path);
    }
    else {
      throw new \Exception('Empty svg path configured');
    }
  }

  /**
   * Check if the path is valid.
   *
   * @param string $path
   *   The path to check.
   *
   * @return bool
   *   True if path is valid.
   */
  public function isValidPath(string $path): bool {
    if ($this->getRealPath($path)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get icons path from config.
   *
   * @param string $path
   *   Relative path.
   *
   * @return string|false
   *   The full path or false if not found.
   */
  protected function getRealPath(string $path): bool|string {
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

    return $this->filesystem->realpath($path);
  }

}
