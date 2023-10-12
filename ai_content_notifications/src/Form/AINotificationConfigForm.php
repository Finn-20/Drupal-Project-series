<?php

namespace Drupal\ai_content_notifications\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Content feedback settings form.
 */
class AINotificationConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_content_notification_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_content_notifications.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $email_token_help = t('Available variables are: [usecase:author], [usecase:title], [usecase:url], [usecase:title-link], [usecase:changed].');
    $config = $this->config('ai_content_notifications.settings');
    $last_changed_notify_interval = $config->get('last_changed_notify_interval');
    
    $email_body = $config->get('email_body');
    // Get default email body.
    $email_body_content = isset($email_body['value']) ? $email_body['value'] : '';
    // Get default email body format.
    $email_body_format = isset($email_body['format']) ? $email_body['format'] : 'basic_html';
    
    $email_from = (null != $config->get('email_from')) ? $config->get('email_from') : \Drupal::config('system.site')->get('mail');
    
    $form['ai_content_notification'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Content Notifications'),
    ];

    /** AI Checklist reviewer Configurations **/
    $form['notify_timeline'] = [
      '#type' => 'details',
      '#title' => t('Timelines'),
      '#description' => t('Select timeline after which content author should be notified if content was not updated and after what interval they should be notified for same usecase if not updated.'),
      '#group' => 'ai_content_notification',
    ];

    $form['notify_timeline']['last_changed_notify_interval'] = [
      '#type' => 'select',
      '#title' => t('Select Timeline'),
      '#description' => t('Select timeline after which content author should be notified if content was not updated. For example, if you select 6 Months, all the usecases which was not updated in last 6 months, their author and contributors will recieve email.'),
      '#options' => ['1' => '1 Month', '3' => '3 Months', '6' => '6 Months', '12' => '12 months'],
      '#default_value' => (null != $config->get('last_changed_notify_interval')) ? $config->get('last_changed_notify_interval') : '6',
    ];
    
    $form['notify_timeline']['max_notified_usecases'] = [
      '#type' => 'select',
      '#title' => t('Limit of use cases to be notified'),
      '#description' => t('Select max limit of use case whose authors will be notified at one cron run'),
      '#options' => ['10' => '10', '20' => '20', '30' => '30', '50' => '50'],
      '#default_value' => (null != $config->get('max_notified_usecases')) ? $config->get('max_notified_usecases') : '20',
    ];
    
    $form['notify_timeline']['last_reminder_notify_interval'] = [
      '#type' => 'select',
      '#title' => t('Select Reminder Mail Frequency'),
      '#description' => t('Select the frequency on which author and contributor will get notification again for same usecase if not updated.'),
      '#options' => ['1' => 'Daily', '7' => 'Weekly', '15' => 'After 2 weeks', '30' => 'Monthly'],
      '#default_value' => (null != $config->get('last_reminder_notify_interval')) ? $config->get('last_reminder_notify_interval') : '30',
    ];
    
    /** Email to User template **/
    $form['content_notification_email'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template'),
      '#description' => t('Add notification email messages sent to <em>reviewer</em> after checklist submission by content author/contributor.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_content_notification',
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

    // AI Content notification configuration.
    $form['notify_settings'] = [
      '#type' => 'details',
      '#title' => t('Notification cron settings'),
      '#description' => t('Notification setting related to cron.'),
      '#group' => 'ai_content_notification',
    ];
    $form['notify_settings']['draft_mode_notify_interval'] = [
      '#type' => 'textfield',
      '#title' => t('Draft mode notify interval'),
      '#description' => t('Interval after which the draft mode notification needs to be sent.'),
      '#default_value' => !empty($config->get('draft_mode_notify_interval')) ? $config->get('draft_mode_notify_interval') : 2,
      '#size' => 15,
    ];
    $form['notify_settings']['draft_mode_notify_interval_duration'] = [
      '#type' => 'textfield',
      '#title' => t('Draft mode duration'),
      '#description' => t('Duration which the draft mode notification needs to be sent i.e. week/hour/month etc.'),
      '#default_value' => !empty($config->get('draft_mode_notify_interval_duration')) ? $config->get('draft_mode_notify_interval_duration') : 'week',
      '#size' => 15,
    ];
    $form['notify_settings']['check_to_notify_non_asset_author_user_list'] = [
      '#type' => 'checkbox',
      '#title' => t('Notify, admin users about notification'),
      '#default_value' => !empty($config->get('check_to_notify_non_asset_author_user_list')) ? $config->get('check_to_notify_non_asset_author_user_list') : NULL,
      '#size' => 90,
    ];
    $form['notify_settings']['notification_non_asset_author_user_list'] = [
      '#type' => 'textfield',
      '#title' => t('Admin users who needs to be notify'),
      '#description' => t('Admin users who needs to be notify, for multple users this needs to be comma separated values.'),
      '#default_value' => !empty($config->get('notification_non_asset_author_user_list')) ? $config->get('notification_non_asset_author_user_list') : NULL,
      '#size' => 90,
    ];
    // AI Content notification level configuration.
    $form['notify_level'] = [
      '#type' => 'details',
      '#title' => t('Notification level'),
      '#description' => t('Select the feature and notification class applicable to it.'),
      '#group' => 'ai_content_notification',
    ];

    $notification_feature = ['none' => 'Select feature', 'newly_added'=> 'Newly added', 'update_action_alert'=>
      'Update Action/Alert', 'communication' => 'Communication', 'interact_marketing' =>
      'Interact(Marketing)','subscriber' => 'Subscriber'];

    for ($fcnt = 1; $fcnt <= 5; $fcnt++) {
      $form['notify_level']['feature']['notification_fieldset'.$fcnt] = [
          '#type' => 'details',
          '#title' => t('Setting for feature ' . $fcnt),
          '#open' => FALSE,
        ];
      $form['notify_level']['feature']['notification_fieldset'. $fcnt]['notification_feature' . $fcnt] = [
        '#type' => 'select',
        '#options' => $notification_feature,
        '#default_value' => (null != $config->get('notification_feature' . $fcnt)) ? $config->get('notification_feature' . $fcnt) : 'none',
      ];
      $form['notify_level']['feature']['notification_fieldset'. $fcnt]['notify_css_class' . $fcnt] = [
        '#type' => 'select',
        '#options' => ['no_class' => 'No class', 'notify_user_blue' => 'Blue Notification',
          'notify_user_brown' => 'Brown Notification',
          'notify_user_green' => 'Green Notification', 
          'notify_user' => 'Red Notification',
          'notify_user_orange' => 'Orange Notification',],
        '#default_value' => (null != $config->get('notify_css_class' . $fcnt)) ?
          $config->get('notify_css_class' . $fcnt) : 'no_class',
      ];
    }

    $form['notify_level']['notify_messages'] = [
      '#type' => 'details',
      '#title' => t('Notification messages'),
      '#open' => FALSE,
    ];
    $form['notify_level']['notify_messages']['newly_added_asset'] = [
      '#type' => 'textfield',
      '#title' => t('Newly added asset'),
      '#default_value' => !empty($config->get('newly_added_asset')) ? $config->get('newly_added_asset') : 'Check out the new published Asset',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['newly_added_homepage_banner_video'] = [
      '#type' => 'textfield',
      '#title' => t('Homepage banner video'),
      '#default_value' => !empty($config->get('newly_added_homepage_banner_video')) ? $config->get('newly_added_homepage_banner_video') : 'Check out the new Video',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['asset_tobe_updated_notification'] = [
      '#type' => 'textfield',
      '#title' => t('Asset to be updated'),
      '#default_value' => !empty($config->get('asset_tobe_updated_notification')) ? $config->get('asset_tobe_updated_notification') : 'Update older assets',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['draft_asset_notification'] = [
      '#type' => 'textfield',
      '#title' => t('Draft asset notification'),
      '#default_value' => !empty($config->get('draft_asset_notification')) ? $config->get('draft_asset_notification') : 'Asset in draft mode',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['inreview_asset_notification'] = [
      '#type' => 'textfield',
      '#title' => t('In review asset notification'),
      '#default_value' => !empty($config->get('inreview_asset_notification')) ? $config->get('inreview_asset_notification') : 'Asset in review mode',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['comment_notification'] = [
      '#type' => 'textfield',
      '#title' => t('Comment notification'),
      '#default_value' => !empty($config->get('comment_notification')) ? $config->get('comment_notification') : 'There is a new comment',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['interact_notification'] = [
      '#type' => 'textfield',
      '#title' => t('Email notification'),
      '#default_value' => !empty($config->get('interact_notification')) ? $config->get('interact_notification') : 'There is a new interaction message',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['rating_notification'] = [
      '#type' => 'textfield',
      '#title' => t('Rating notification'),
      '#default_value' => !empty($config->get('rating_notification')) ? $config->get('rating_notification') : 'There is a new Rating',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['subscriber'] = [
      '#type' => 'textfield',
      '#title' => t('Subscriber notification'),
      '#default_value' => !empty($config->get('subscriber')) ? $config->get('subscriber') : 'There is a new asset of ur interest',
      '#maxlength' => 180,
    ];
    $form['notify_level']['notify_messages']['append_message_text_non_asset_user'] = [
      '#type' => 'textfield',
      '#title' => t('Text to be append to display message for non asset author'),
      '#default_value' => !empty($config->get('append_message_text_non_asset_user')) ? $config->get('append_message_text_non_asset_user') : NULL,
      '#maxlength' => 180,
    ];
    $form['notify_level']['my_idea_notification'] = [
      '#type' => 'details',
      '#title' => t('My Idea Notification'),
      '#open' => FALSE,
    ];
    $form['notify_level']['my_idea_notification']['newly_added_idea'] = [
      '#type' => 'textfield',
      '#title' => t('Newly added idea'),
      '#default_value' => !empty($config->get('newly_added_idea')) ? $config->get('newly_added_idea') : 'Check out the new published Idea',
      '#maxlength' => 180,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_content_notifications.settings');
    
    $config->set('last_changed_notify_interval', $form_state->getValue('last_changed_notify_interval'));
    $config->set('last_reminder_notify_interval', $form_state->getValue('last_reminder_notify_interval'));
    $config->set('max_notified_usecases', $form_state->getValue('max_notified_usecases'));
    
    $config->set('email_from', $form_state->getValue('email_from'));
    $config->set('email_subject', $form_state->getValue('email_subject'));
    $config->set('email_body', $form_state->getValue('email_body'));
    
    for ($fcnt = 1; $fcnt <= 5; $fcnt++) {
      $config->set('notification_feature' . $fcnt, $form_state->getValue('notification_feature' . $fcnt));
      //$config->set('notify_level' . $fcnt, $form_state->getValue('notify_level' . $fcnt));
      $config->set('notify_css_class' . $fcnt, $form_state->getValue('notify_css_class' . $fcnt));
      $notification_feature[$form_state->getValue('notification_feature' . $fcnt)] = $form_state->getValue('notify_css_class' . $fcnt);
      //$notify_class[$form_state->getValue('notify_level' . $fcnt)] = $form_state->getValue('notify_css_class' . $fcnt);
    }
    $config->set('notification_features', $notification_feature);
    //$config->set('notification_css_class', $notify_class);

    //Cron related settings.
    $config->set('draft_mode_notify_interval', $form_state->getValue('draft_mode_notify_interval'));
    $config->set('draft_mode_notify_interval_duration', $form_state->getValue('draft_mode_notify_interval_duration'));
    $config->set('check_to_notify_non_asset_author_user_list', $form_state->getValue('check_to_notify_non_asset_author_user_list'));
    $config->set('notification_non_asset_author_user_list', $form_state->getValue('notification_non_asset_author_user_list'));

    //Notification related messages.
    $config->set('newly_added_asset', $form_state->getValue('newly_added_asset'));
    $config->set('newly_added_homepage_banner_video', $form_state->getValue('newly_added_homepage_banner_video'));
    $config->set('asset_tobe_updated_notification', $form_state->getValue('asset_tobe_updated_notification'));
    $config->set('draft_asset_notification', $form_state->getValue('draft_asset_notification'));
    $config->set('inreview_asset_notification', $form_state->getValue('inreview_asset_notification')); 
    $config->set('comment_notification', $form_state->getValue('comment_notification'));
    $config->set('interact_notification', $form_state->getValue('interact_notification')); 
    $config->set('subscriber', $form_state->getValue('subscriber')); 
    $config->set('rating_notification', $form_state->getValue('rating_notification'));
    $config->set('append_message_text_non_asset_user', $form_state->getValue('append_message_text_non_asset_user'));

    // my idea notification
    $config->set('newly_added_idea', $form_state->getValue('newly_added_idea'));
    $config->save();

    parent::submitForm($form, $form_state);
  }
}
