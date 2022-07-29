<?php

namespace Drupal\svg_icons;

use Drupal\Core\File\FileSystem;
use Drupal\Core\Template\Loader\FilesystemLoader;
use Drupal\Core\Config\ConfigFactoryInterface;

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
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Configuration factory.
   */
  public function __construct(FileSystem $filesystem, FilesystemLoader $loader, ConfigFactoryInterface $configFactory) {
    $this->filesystem = $filesystem;
    $this->loader = $loader;
    $this->config = $configFactory->get('svg_icons.settings');
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
   * Get realpath from config default.
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
      throw new \Exception('No svg path configured.');
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
  public function isValidPath(string $path) {
    if ($this->getRealPath($path)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get realpath from twig loader or relative.
   *
   * @param string $path
   *   Relative path.
   *
   * @return string|false
   *   The full path or false if not found.
   */
  public function getRealPath(string $path) {
    // Twig loader replace @theme/@module with template dir.
    if (substr($path, 0, 1) == '@') {
      $folders = explode('/', $path);
      $base = array_shift($folders);
      $base_path = str_replace('/templates', '', $this->loader->getPaths(str_replace('@', '', $base)));
      if (!empty($base_path)) {
        $path = current($base_path) . '/' . implode('/', $folders);
      }
      else {
        return FALSE;
      }
    }

    return $this->filesystem->realpath($path);
  }

}
