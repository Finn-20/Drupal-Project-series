<?php

namespace Drupal\user_notification\Form;

use Drupal\node\Entity\NodeType;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AlertSettings.
 */
class AlertSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_notification_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'user_notification.config_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('user_notification.config_settings');

    $node_types = NodeType::loadMultiple();
	/* $entityManager = \Drupal::service('entity.manager');
$bundles = $entityManager->getBundleInfo('paragraph');
print_r($bundles); */
    $node_type_titles = [];
    foreach ($node_types as $machine_name => $val) {
      $node_type_titles[$machine_name] = $val->label();
    }
    
    $form['content_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Notification For content type'),
      '#description' => $this->t('The list of Content type for which user should see the Notifications on their screen.'),
      '#options' => $node_type_titles,
      '#default_value' => $config->get('user_notification.content_types'),
    ];

    $form['user_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('User Roles'),
      '#description' => $this->t('Which type of user should see the Content update notification.'),
      '#options' => user_role_names(),
      '#default_value' => $config->get('user_notification.user_roles'),
    ];

    $form['alert_settings'] = [
      '#type' => 'details',
      '#title' => t('User Notification Settings'),
      '#open' => TRUE,
    ];

    $form['alert_settings']['duration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Duration'),
      '#description' => t('The number of days that a node is considered new.'),
      '#default_value' => $config->get('user_notification.duration'),
    ];

    $form['email_alert_settings'] = [
      '#type' => 'details',
      '#title' => t('Email Alert Settings'),
      '#description' => t('Send out notification via Email to specific email address .'),
      '#open' => TRUE,
    ];

    $form['email_alert_settings']['email_receivers'] = [
      '#type' => 'textarea',
      '#rows' => 3,
      '#title' => $this->t('Email addresses'),
      '#default_value' => $config->get('user_notification.email_receivers'),
    ];

    $form['email_alert_settings']['email_alert_receivers_roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Email notification on basis of User roles.'),
      '#options' => user_role_names(),
      '#default_value' => $config->get('user_notification.email_user_roles'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) { 
    // Retrieve the configuration.
    $this->configFactory->getEditable('user_notification.config_settings')
      // Set the submitted configuration setting.
      ->set('user_notification.content_types', $form_state->getValue('content_types'))
      ->set('user_notification.user_roles', $form_state->getValue('user_roles'))
      ->set('user_notification.duration', $form_state->getValue('duration'))
      ->set('user_notification.email_receivers', $form_state->getValue('email_receivers'))
      ->set('user_notification.email_user_roles', $form_state->getValue('email_alert_receivers_roles'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
