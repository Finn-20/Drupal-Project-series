<?php

namespace Drupal\ai_utility\Form\Settings;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class AIGeneralEmailSettingConfigForm extends ConfigFormBase {
	/**
   	* {@inheritdoc}
   	*/
  public function getFormId() {
    return 'ai_general_email_setting_configuration_form';
  }
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_utility.general_email_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    
    $config = $this->config('ai_utility.general_email_settings');

    $form['email_testing'] = [
      '#type' => 'details',
      '#title' => t('Configuration for Email settings'),
      '#description' => t('Email Settings'),
      '#group' => 'ai_utility',
    ];

    $form['email_testing']['check_to_enable_testing'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable email debug mode.'),
      '#default_value' => !empty($config->get('check_to_enable_testing')) ? $config->get('check_to_enable_testing') : NULL,
      '#size' => 90,
    ];
    $form['email_testing']['mail_testing_user_list'] = [
      '#type' => 'textarea',
      '#title' => t('Users email id for the debug mode.'),
      '#description' => t('Users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('mail_testing_user_list')) ? $config->get('mail_testing_user_list') : NULL,
      ];
    $form['email_testing']['mail_testing_bcc_user_list'] = [
      '#type' => 'textarea',
      '#title' => t('Users who needs to be notify in BCC'),
      '#description' => t('Users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('mail_testing_bcc_user_list')) ? $config->get('mail_testing_bcc_user_list') : NULL,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_utility.general_email_settings');

    $config->set('check_to_enable_testing', $form_state->getValue('check_to_enable_testing'));
    $config->set('mail_testing_user_list', $form_state->getValue('mail_testing_user_list'));
    $config->set('mail_testing_bcc_user_list', $form_state->getValue('mail_testing_bcc_user_list'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}