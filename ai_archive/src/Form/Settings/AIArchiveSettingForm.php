<?php

namespace Drupal\ai_archive\Form\Settings;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


class AIArchiveSettingForm extends ConfigFormBase {
	/**
   	* {@inheritdoc}
   	*/
  public function getFormId() {
    return 'ai_archive_config_form';
  }
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_archive.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state){
    
    $config = $this->config('ai_archive.settings');

    $archived_email_body = $config->get('archived_email_body');
    $archived_email_body_content = isset($archived_email_body['value']) ? $archived_email_body['value'] : '';
    $archived_email_body_format = isset($archived_email_body['format']) ? $archived_email_body['format'] : 'basic_html';

    $unarchived_email_body = $config->get('unarchived_email_body');
    $unarchived_email_body_content = isset($unarchived_email_body['value']) ? $unarchived_email_body['value'] : '';
    $unarchived_email_body_format = isset($unarchived_email_body['format']) ? $unarchived_email_body['format'] : 'basic_html';

    $disclaimer_body = $config->get('asset_disclaimer');
    $disclaimer_body_content = isset($disclaimer_body['value']) ? $disclaimer_body['value'] : '';
    $disclaimer_body_format = isset($disclaimer_body['format']) ? $disclaimer_body['format'] : 'basic_html';

    $email_token_help = t('Available variables are: [asset:url].');

    $form['ai_archive'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Archive'),
    ];
    $form['email_testing'] = [
      '#type' => 'details',
      '#title' => t('Add User to test archive mails'),
      '#description' => t('Add user mail ids who can notify when asset moved to archive/un-archive state. Multiple users should be seprated by comma (,)'),
      '#group' => 'ai_archive',
    ];
    $form['email_testing']['mail_testing_user_list'] = [
      '#type' => 'textfield',
      '#title' => t('Users who needs to be notify'),
      '#description' => t('Users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('mail_testing_user_list')) ? $config->get('mail_testing_user_list') : NULL,
      '#size' => 1000,
    ];
    $form['email_testing']['mail_testing_bcc_user_list'] = [
      '#type' => 'textfield',
      '#title' => t('Users who needs to be notify in BCC'),
      '#description' => t('Users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('mail_testing_bcc_user_list')) ? $config->get('mail_testing_bcc_user_list') : NULL,
      '#size' => 1000,
    ];
    $form['email_testing']['check_to_enable_testing'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable testing archive mail'),
      '#default_value' => !empty($config->get('check_to_enable_testing')) ? $config->get('check_to_enable_testing') : NULL,
      '#size' => 90,
    ]; 
    $form['default_user'] = [
      '#type' => 'details',
      '#title' => t('Add User to notify archive assets'),
      '#description' => t('Add user mail ids who can notify when asset moved to archive/un-archive state. Multiple users should be seprated by comma (,)'),
      '#group' => 'ai_archive',
    ];

    $form['default_user']['mail_non_asset_author_user_list'] = [
      '#type' => 'textfield',
      '#title' => t('Admin users who needs to be notify'),
      '#description' => t('Admin users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('mail_non_asset_author_user_list')) ? $config->get('mail_non_asset_author_user_list') : NULL,
      '#size' => 90,
    ];
    $form['content_archived_email'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template - Asset Moved to Archive State'),
      '#description' => t('Add notification email messages sent to <em>Owner,Primary Owner, Secondary Owner and Admin</em> after asset moved to archive state.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_archive',
    ];
    $form['content_archived_email']['archived_email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Archive State Subject'),
      '#default_value' => $config->get('archived_email_subject'),
      '#maxlength' => 180,
    ];

    $form['content_archived_email']['archived_email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Archive State Email Template'),
      '#default_value' => $archived_email_body_content,
      '#format' => $archived_email_body_format,
    ];
    $form['content_archived_email']['asset_disclaimer'] = [
      '#type' => 'text_format',
      '#title' => t('Archive messages : Disclaimer in node detail page'),
      '#default_value' => $disclaimer_body_content,
      '#format' => $disclaimer_body_format,
    ];

    $form['content_unarchived_email'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template - Asset Moved to Un-Archive State'),
      '#description' => t('Add notification email messages sent to <em>Owner,Primary Owner, Secondary Owner and Admin</em> after asset moved to Un-archive state.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_archive',
    ];

    $form['content_unarchived_email']['unarchived_email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Un-Archive State Subject'),
      '#default_value' => $config->get('unarchived_email_subject'),
      '#maxlength' => 180,
    ];

    $form['content_unarchived_email']['unarchived_email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Un-Archive State Email Template'),
      '#default_value' => $unarchived_email_body_content,
      '#format' => $unarchived_email_body_format,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_archive.settings');
 
    $config->set('mail_testing_user_list', $form_state->getValue('mail_testing_user_list')); 
    $config->set('mail_testing_bcc_user_list', $form_state->getValue('mail_testing_bcc_user_list'));
    $config->set('check_to_enable_testing', $form_state->getValue('check_to_enable_testing'));
    $config->set('mail_non_asset_author_user_list', $form_state->getValue('mail_non_asset_author_user_list'));
    $config->set('archived_email_subject', $form_state->getValue('archived_email_subject'));
    $config->set('archived_email_body', $form_state->getValue('archived_email_body'));

    $config->set('unarchived_email_subject', $form_state->getValue('unarchived_email_subject'));
    $config->set('unarchived_email_body', $form_state->getValue('unarchived_email_body'));

    $config->set('asset_disclaimer', $form_state->getValue('asset_disclaimer'));

    $config->save();

    parent::submitForm($form, $form_state);
  }
}