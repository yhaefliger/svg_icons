<?php

namespace Drupal\svg_icons\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\svg_icons\Utils;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SVG Icons Library settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Shared SVG Icons utils.
   *
   * @var \Drupal\svg_icons\Utils
   */
  protected $utils;

  /**
   * Form constructor.
   *
   * @param \Drupal\svg_icons\Utils $utils
   *   Shared SVG Icons utils.
   */
  final public function __construct(Utils $utils) {
    $this->utils = $utils;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static($container->get('svg_icons.utils'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'svg_icons_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'svg_icons.settings',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('svg_icons.settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#open' => TRUE,
    ];

    $form['general']['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('SVG Path'),
      '#default_value' => $config->get('path') ? $config->get('path') : '@svg_icons/svg',
    ];

    $form['general']['remove_existing_class'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove existing class'),
      '#description' => $this->t('Remove existing class in the SVG file.'),
      '#default_value' => $config->get('remove_existing_class'),
    ];

    $form['general']['default_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default class to add'),
      '#default_value' => $config->get('default_class') ? $config->get('default_class') : '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Validate form submitted path.
   *
   * @{@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
    if (!$this->utils->isValidPath($form_state->getValue('path'))) {
      $form_state->setErrorByName('path', $this->t('Invalid path.'));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);
    $config = $this->config('svg_icons.settings');

    foreach (['path', 'default_class', 'remove_existing_class'] as $key) {
      if ($form_state->hasValue($key)) {
        $config->set($key, $form_state->getValue($key));
      }
    }

    $config->save();
  }

}
