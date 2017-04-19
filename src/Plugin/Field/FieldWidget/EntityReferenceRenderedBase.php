<?php

namespace Drupal\entityreference_rendered_widget\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Base class for widgets provided by this module.
 */
abstract class EntityReferenceRenderedBase extends OptionsButtonsWidget {

  /**
   * Display modes available for target entity type.
   *
   * @var array
   */
  protected $displayModes;

  /**
   * Label display options.
   *
   * @var array
   */
  protected $labelOptions;

  /**
   * Referenced entity type.
   *
   * @var string
   */
  protected $targetEntityType;

  /**
   * Referenced entity type.
   *
   * @var array
   */
  protected $fieldSettings;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings) {

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    /** @var \Drupal\Core\Entity\EntityDisplayRepository $edr */
    $edr = \Drupal::service('entity_display.repository');
    $this->fieldSettings = $this->getFieldSettings();
    $this->targetEntityType = $this->getFieldSetting('target_type');
    $this->displayModes = $edr->getViewModes($this->targetEntityType);
    $this->labelOptions = [
      'before' => $this->t('Before rendered element'),
      'after' => $this->t('After rendered element'),
      'hidden' => $this->t('Hidden'),
    ];

  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'display_mode' => 'default',
      'label_display' => 'before',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];
    $settings = $this->settings;

    $modes = ['default' => $this->t('Default')];
    foreach ($this->displayModes as $mode_name => $mode) {
      $modes[$mode_name] = $mode['label'];
    }

    $elements['display_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Display mode used'),
      '#options' => $modes,
      '#default_value' => isset($settings['display_mode']) ? $settings['display_mode'] : 'default',
    ];
    $elements['label_display'] = [
      '#type' => 'select',
      '#title' => $this->t('Label display'),
      '#options' => $this->labelOptions,
      '#default_value' => isset($settings['label_display']) ? $settings['label_display'] : 'before',
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $settings = $this->getSettings();
    $display_mode = $settings['display_mode'];
    $label_display = $settings['label_display'];

    $summary[] = $this->t('Display mode: @mode', ['@mode' => $this->displayModes[$display_mode]['label']]);
    $summary[] = $this->t('Label display: @label_display', ['@label_display' => $this->labelOptions[$label_display]]);

    return $summary;
  }

}
