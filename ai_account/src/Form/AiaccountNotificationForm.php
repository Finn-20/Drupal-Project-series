<?php

namespace Drupal\ai_account\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class AiaccountNotificationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_acccount_notification_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $industry_vid = 'industries';
    $indus_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($industry_vid);
    foreach ($indus_terms as $indus_term) {
      $industries[$indus_term->tid] = $indus_term->name;
    }

    $domain_vid = 'domain';
    $domain_tid = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($domain_vid);

    foreach ($domain_tid as $domain) {
      $domains[$domain->tid] = $domain->name;
    }
    // Fetch default value of industry and domain.
    $current_userid = \Drupal::currentUser()->id();
    $account_info = User::load($current_userid);

    $default_industries = [];
    $default_domains = [];

    $user_selected_industries = $account_info->get('field_favorite_industries_notifi')->getValue();
    $user_selected_domains = $account_info->get('field_favorite_terms')->getValue();

    if (isset($user_selected_industries) && !empty($user_selected_industries)) {
      foreach ($user_selected_industries as $selected_industry) {
        if (isset($selected_industry['target_id']) && !empty($selected_industry['target_id'])) {
          $default_industries[$selected_industry['target_id']] = $selected_industry['target_id'];
        }
      }
    }

    if (isset($user_selected_domains) && !empty($user_selected_domains)) {
      foreach ($user_selected_domains as $selected_domain) {
        if (isset($selected_domain['target_id']) && !empty($selected_domain['target_id'])) {
          $default_domains[$selected_domain['target_id']] = $selected_domain['target_id'];
        }
      }
    }

    $form['current_user'] = [
      '#type' => 'value',
      '#value' => $current_userid,
    ];

    $form['selected_industries'] = [
      '#type' => 'checkboxes',
      '#title' => t('Notify me about Industries'),
      '#default_value' => $default_industries,
      '#options' => $industries,
      '#prefix' => '<div class="acc_notification_wrapper"><div class="acc_noti_industries">',
      '#suffix' => '</div>',
    ];
    $form['selected_domain'] = [
      '#type' => 'checkboxes',
      '#title' => t('Notify me about Domains'),
      '#default_value' => $default_domains,
      '#options' => $domains,
      '#prefix' => '<div class="acc_noti_domains">',
      '#suffix' => '</div><div class="clearfix"></div></div>',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save settings'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $current_userid = $values['current_user'];
    // Pass your uid.
    $account = User::load($current_userid);

    $industry_values = [];
    $domain_values = [];

    foreach ($values['selected_industries'] as $industry_tid => $industry_value) {
      if (isset($industry_value) && !empty($industry_value)) {
        $industry_values[$industry_tid] = $industry_tid;
      }
    }

    foreach ($values['selected_domain'] as $domain_tid => $domain_value) {
      if (isset($domain_value) && !empty($domain_value)) {
        $domain_values[$domain_tid] = $domain_tid;
      }
    }

    $account->set('field_favorite_industries_notifi', $industry_values);
    $account->set('field_favorite_terms', $domain_values);

    $account->save();
    drupal_set_message($this->t('Industries & Domains Notifications Updated Successfully.'));
  }

}
