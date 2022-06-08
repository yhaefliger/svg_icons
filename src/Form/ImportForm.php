<?php

namespace Drupal\svg_icons\Form;

use Drupal\Core\Url;
use Drupal\svg_icons\Utils;
use Drupal\file\Entity\File;
use Drupal\svg_icons\SVGIcon;
use Drupal\media\Entity\Media;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\media\Entity\MediaType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@inheritDoc}
 */
class ImportForm extends FormBase {

  /**
   * The FileSystem service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $filesystem;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The EntityTypeManager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Shared SVG Icons utils.
   *
   * @var \Drupal\svg_icons\Utils
   */
  protected $utils;

  /**
   * Form constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $filesystem
   *   The FileSystem service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The EntityTypeManager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The ModuleHandler service.
   * @param \Drupal\svg_icons\Utils $utils
   *   SVG Icons Utils.
   */
  public function __construct(
    FileSystemInterface $filesystem,
    AccountInterface $current_user,
    EntityTypeManagerInterface $entityTypeManager,
    ModuleHandlerInterface $moduleHandler,
    Utils $utils
  ) {
    $this->filesystem = $filesystem;
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entityTypeManager;
    $this->moduleHandler = $moduleHandler;
    $this->utils = $utils;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('svg_icons.utils')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'svg_icons_import_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (!$this->moduleHandler->moduleExists('media')) {
      return $this->setError($this->t('Media module is not enabled. <a href=":module_url">Install media module first</a>.', [
        ':module_url' => Url::fromRoute('system.modules_list')->toString(),
      ]), $form);
    }

    $bundles = [];
    $mediaTypes = $this->entityTypeManager->getStorage('media_type')->loadMultiple();
    /** @var \Drupal\media\Entity\MediaType $mediaType */
    foreach ($mediaTypes as $key => $mediaType) {
      $settings = $mediaType->getSource()->getSourceFieldDefinition($mediaType)->getSettings();
      if (isset($settings['file_extensions'])) {
        $extensions = explode(' ', $settings['file_extensions']);
        if (in_array('svg', $extensions)) {
          $bundles[$key] = $mediaType->label();
        }
      }
    }

    if (empty($bundles)) {
      // Probably missing svg extension configuration.
      if ($this->moduleHandler->moduleExists('svg_image')) {
        return $this->setError($this->t('svg_image module is installed but no source can accept svg files. Add svg file extension into the list of the allowed image extensions in the field settings.'), $form);
      }
      // Probably missing svg source support module.
      else {
        return $this->setError($this->t('No media bundles can handle svg file format. Consider installing either <a href=":svg_image_url">svg_image</a> or <a href=":svg_image_field_url">svg_image_field</a> module.', [
          ':svg_image_url' => 'https://www.drupal.org/project/svg_image',
          ':svg_image_field_url' => 'https://www.drupal.org/project/svg_image_field',
        ]), $form);
      }
    }

    $config = $this->config('svg_icons.settings');

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path to SVG icons'),
      '#default_value' => $config->get('path') ? $config->get('path') : '',
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class to add'),
      '#default_value' => $config->get('default_class') ? $config->get('default_class') : '',
    ];

    $form['media_bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Media bundle'),
      '#options' => $bundles,
      '#default_value' => $config->get('import_media_bundle') ? $config->get('import_media_bundle') : 'icon',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import icons into media'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    if (!$form_state->getValue('path') || !$this->utils->isValidPath($form_state->getValue('path'))) {
      $form_state->setErrorByName('path', $this->t('Invalid path.'));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $mediaType = MediaType::load($values['media_bundle']);
    $mediaBundle = $mediaType->getOriginalId();
    $mediaField = $mediaType->getSource()->getConfiguration()['source_field'];

    $path = $this->utils->getPath($values['path']);

    $icons = [];
    if ($path) {
      $icons = glob("$path/*.svg");
    } else {
      $this->messenger()->addError('Invalid path.');
    }
    $realpath = $this->filesystem->realpath($this->config('system.file')->get('default_scheme') . "://");
    $publicDir = $realpath . '/svg';

    if (!empty($icons) && $this->filesystem->prepareDirectory($publicDir, FileSystemInterface::CREATE_DIRECTORY)) {
      foreach ($icons as $icon_path) {
        $filename = basename($icon_path);
        $content = file_get_contents($icon_path);
        $svg = new SVGIcon($content);
        $svg->addClass($values['class']);

        $file = $this->filesystem->saveData($svg, $publicDir . '/' . $filename, FileSystemInterface::EXISTS_REPLACE);
        $fileUri = str_replace($realpath, 'public:/', $file);
        $files = $this->entityTypeManager->getStorage('file')->loadByProperties(['uri' => $fileUri]);
        if (empty($files)) {
          $file = File::create();
          $file->setFileUri($fileUri);
          $file->setOwnerId($this->currentUser()->id());
          $file->setMimeType('image/svg+xml');
          $file->setFilename($filename);
          $file->setPermanent();
          $file->save();
        } else {
          $file = reset($files);
        }

        // Create Drupal Media element.
        $media = $this->entityTypeManager->getStorage('media')->getQuery()
          ->condition('bundle', $mediaBundle)
          ->condition($mediaField . '.target_id', $file->id())
          ->execute();

        if (!$media || empty($media)) {
          $media = Media::create([
            'bundle' => $mediaBundle,
            'uid' => $this->currentUser()->id(),
            'name' => $filename,
            'status' => 1,
            $mediaField => $file,
          ]);

          $media->save();
        }
      }
    }

    $this->messenger()->addMessage($this->t('Import finished'));
  }

  /**
   * Set warning message and stop processing.
   *
   * @param string $message
   *   The message to display.
   * @param array $form
   *   The form array.
   */
  protected function setError(string $message, array $form) {
    $form['warning'] = [
      '#type' => 'container',
      '#markup' => $message,
      '#attributes' => [
        'class' => ['messages', 'messages--warning'],
      ],
      '#weight' => -10,
    ];
    return $form;
  }
}
