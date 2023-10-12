<?php

namespace Drupal\ai_site_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Content feedback settings form.
 */
class AISiteSettingConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_site_configuration_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_site_configuration.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('ai_site_configuration.settings');
    $form['ai_site_settings'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Site configuration settings'),
    ];

    /** AI Checklist reviewer Configurations **/
    $form['tribes_configuration'] = [
      '#type' => 'details',
      '#title' => t('Tribes configuration'),
      '#description' => t('Configuration related to collabore tribes.'),
      '#group' => 'ai_site_settings',
    ];
    $form['tribes_configuration']['display_tribes_carousel'] = [
      '#type' => 'checkbox',
      '#title' => t('Display asset carousel on the tribes page'),
      '#default_value' => !empty($config->get('display_tribes_carousel')) ? $config->get('display_tribes_carousel') : 0,
      '#size' => 90,
    ];
	$form['tribes_configuration']['display_tribes_cards'] = [
      '#type' => 'checkbox',
      '#title' => t('Display contact cards on the tribes page'),
      '#default_value' => !empty($config->get('display_tribes_cards')) ? $config->get('display_tribes_cards') : 0,
      '#size' => 90,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_site_configuration.settings');
    
    $config->set('display_tribes_carousel', $form_state->getValue('display_tribes_carousel'));
	$config->set('display_tribes_cards', $form_state->getValue('display_tribes_cards'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
