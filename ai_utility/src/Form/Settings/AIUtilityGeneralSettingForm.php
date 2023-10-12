<?php

namespace Drupal\ai_utility\Form\Settings;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class AIUtilityGeneralSettingForm extends ConfigFormBase {
	/**
   	* {@inheritdoc}
   	*/
  public function getFormId() {
    return 'ai_utility_general_config_form';
  }
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_utility_general.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    
    $config = $this->config('ai_utility_general.settings');


    $form['ai_utility'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('General Settings'),
    ];
    $form['general_settings'] = [
      '#type' => 'details',
      '#title' => t('General Settings'),
      '#group' => 'ai_utility',
    ];
    $form['general_settings']['asset_owner_valid_domain'] = [
      '#type' => 'textfield',
      '#title' => t('Assets pri/sec owners valid domains'),
      '#description' => t('Domains which are allowed to be the part of pri/sec assets owners. Comma separated values.'),
      '#default_value' => !empty($config->get('asset_owner_valid_domain')) ? $config->get('asset_owner_valid_domain') : NULL,
    ];
    $form['general_settings']['asset_owner_valid_email_domain'] = [
      '#type' => 'textfield',
      '#title' => t('Asset owner/ contributor valid domain name.'),
      '#description' => t('Error message to be displayed, if primary owner/ contributor email domain is not valid.'),
      '#default_value' => !empty($config->get('asset_owner_valid_email_domain')) ? $config->get('asset_owner_valid_email_domain')
      : 'Currently we accept only capgemini.com and sogeti.com.',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_utility_general.settings');
 
    $config->set('asset_owner_valid_domain', $form_state->getValue('asset_owner_valid_domain'));
    $config->set('asset_owner_valid_email_domain', $form_state->getValue('asset_owner_valid_email_domain'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}