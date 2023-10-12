<?php

namespace Drupal\ai_account\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Content feedback settings form.
 */
class AIFavNotifyConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_account_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_account_notifications.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $email_token_help = t('Available variables are: [usecase:author], [usecase:title], [usecase:url], [usecase:title-link], [usecase:changed].');
    $config = $this->config('ai_account_notifications.settings');
    $last_changed_notify_interval = $config->get('last_changed_notify_interval');

    $email_body = $config->get('email_body');
    // Get default email body.
    $email_body_content = isset($email_body['value']) ? $email_body['value'] : '';
    // Get default email body format.
    $email_body_format = isset($email_body['format']) ? $email_body['format'] : 'basic_html';

    $email_from = (NULL != $config->get('email_from')) ? $config->get('email_from') : \Drupal::config('system.site')->get('mail');

    $form['ai_account_notifications'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Acount Notifications'),
    ];

    // Email to User template.
    $form['content_notification_email'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template'),
      '#description' => t('Add Subscribtions Term Notification Email message to Users .') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_account_notifications',
    ];

    $form['content_notification_email']['email_from'] = [
      '#type' => 'textfield',
      '#title' => t('From'),
      '#default_value' => $email_from,
      '#maxlength' => 180,
    ];

    $form['content_notification_email']['email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Subject'),
      '#default_value' => $config->get('email_subject'),
      '#maxlength' => 180,
    ];

    $form['content_notification_email']['email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $email_body_content,
      '#format' => $email_body_format,
    ];

    // Asset notification settings.
    $form['asset_notification_settings'] = [
      '#type' => 'details',
      '#title' => t('Asset Notification settings'),
      '#description' => t('Asset notification settings.'),
      '#group' => 'ai_account_notifications',
    ];

    $duration_options = [
      '+5 minute' => '+5 minute',
      '+15 minute' => '+15 minute',
      '+30 minute' => '+30 minute',
      '+1 day' => '+1 day',
      '+1 month' => '+1 month',
      '+2 month' => '+2 month',
      '+3 month' => '+3 month',
      '+6 month' => '+6 month',
      '+1 year' => '+1 year',
      ];
    $notification_duration = $config->get('notification_duration');

    $form['asset_notification_settings']['notification_duration'] = [
      '#type' => 'select',
      '#title' => t('Notification Duration'),
      '#default_value' => $notification_duration,
      '#options' => $duration_options,
    ];

    $notification_display_text = $config->get('notification_display_text');
    $form['asset_notification_settings']['notification_display_text'] = [
      '#type' => 'textfield',
      '#title' => t('Asset notification message'),
      '#default_value' => $notification_display_text,
    ];

    $popup_notification_display_text = $config->get('popup_notification_display_text');
    $form['asset_notification_settings']['popup_notification_display_text'] = [
      '#type' => 'textfield',
      '#title' => t('Popup Asset notification message'),
      '#default_value' => $popup_notification_display_text,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit function.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_account_notifications.settings');

    $config->set('last_changed_notify_interval', $form_state->getValue('last_changed_notify_interval'));
    $config->set('last_reminder_notify_interval', $form_state->getValue('last_reminder_notify_interval'));
    $config->set('max_notified_usecases', $form_state->getValue('max_notified_usecases'));

    $config->set('email_from', $form_state->getValue('email_from'));
    $config->set('email_subject', $form_state->getValue('email_subject'));
    $config->set('email_body', $form_state->getValue('email_body'));

    $config->set('notification_duration', $form_state->getValue('notification_duration'));
    $config->set('notification_display_text', $form_state->getValue('notification_display_text'));
    $config->set('popup_notification_display_text', $form_state->getValue('popup_notification_display_text'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
