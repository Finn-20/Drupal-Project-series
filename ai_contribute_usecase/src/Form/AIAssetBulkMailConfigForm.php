<?php

namespace Drupal\ai_contribute_usecase\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class AIAssetBulkMailConfigForm extends ConfigFormBase {
	/**
   	* {@inheritdoc}
   	*/
  public function getFormId() {
    return 'ai_asset_bulk_mail_configuration_form';
  }
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_contribute_usecase.bulk_mail.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    
    $config = $this->config('ai_contribute_usecase.bulk_mail.settings');

    $asset_bulk_email_body = $config->get('asset_bulk_email_body');
    $asset_bulk_email_body_content = isset($asset_bulk_email_body['value']) ? $asset_bulk_email_body['value'] : '';
    $asset_bulk_email_body_format = isset($asset_bulk_email_body['format']) ? $asset_bulk_email_body['format'] : 'basic_html';

    $form['ai_contribute_usecase'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('Bulk Email Settings'),
    ];

    $form['asset_bulk_emails'] = [
      '#type' => 'details',
      '#title' => t('Configuration for sending bulk mails for the assets'),
      '#description' => t('Assets bulk mail sending feature'),
      '#group' => 'ai_archive',
    ];

    $form['asset_bulk_emails']['bulk_mail_process_duration'] = [
      '#type' => 'textfield',
      '#title' => t('Bulk email process duration.'),
      '#description' => t('The duration for which the asset should be included e.g. -1 week, -1 month, -6 month etc.'),
      '#default_value' => $config->get('bulk_mail_process_duration'),
      '#maxlength' => 60,
    ];

    $form['asset_bulk_emails']['asset_bulk_mail_user_processed_limit'] = [
      '#type' => 'textfield',
      '#title' => t('User processed limit per batch'),
      '#default_value' => $config->get('asset_bulk_mail_user_processed_limit'),
      '#maxlength' => 60,
    ];

    $form['asset_bulk_emails']['bulk_mail_user_last_processed_user_id'] = [
      '#type' => 'textfield',
      '#title' => t('Bulk email last processed user Id.'),
      '#default_value' => $config->get('bulk_mail_user_last_processed_user_id'),
      '#maxlength' => 60,
    ];

    $form['asset_bulk_emails']['asset_bulk_email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Asset bulk email Subject'),
      '#default_value' => $config->get('asset_bulk_email_subject'),
      '#maxlength' => 180,
    ];

    $form['asset_bulk_emails']['asset_bulk_email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Asset bulk Email Template'),
      '#default_value' => $asset_bulk_email_body_content,
      '#format' => $asset_bulk_email_body_format,
    ];

    $form['asset_bulk_emails']['bulk_mail_user_last_processed_date'] = [
      '#type' => 'textfield',
      '#title' => t('Bulk email last process date.'),
      '#description' => t('Last date on which the bulk process was executed.'),
      '#default_value' => $config->get('bulk_mail_user_last_processed_date'),
      '#maxlength' => 60,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_contribute_usecase.bulk_mail.settings');

    $config->set('asset_bulk_email_subject', $form_state->getValue('asset_bulk_email_subject'));
    $config->set('asset_bulk_email_body', $form_state->getValue('asset_bulk_email_body'));
    $config->set('asset_bulk_mail_user_processed_limit', $form_state->getValue('asset_bulk_mail_user_processed_limit'));
    $config->set('bulk_mail_user_last_processed_user_id', $form_state->getValue('bulk_mail_user_last_processed_user_id'));
    $config->set('bulk_mail_process_duration', $form_state->getValue('bulk_mail_process_duration'));
    $config->set('bulk_mail_user_last_processed_date', $form_state->getValue('bulk_mail_user_last_processed_date'));

    $config->save();

    parent::submitForm($form, $form_state);
  }
}