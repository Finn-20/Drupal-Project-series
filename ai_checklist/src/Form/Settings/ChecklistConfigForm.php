<?php

namespace Drupal\ai_checklist\Form\Settings;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Content feedback settings form.
 */
class ChecklistConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ai_checklist.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user_roles = user_roles();
    $roles = [];
    foreach ($user_roles as $id => $role) {
      if (in_array($id, ['anonymous', 'authenticated', 'creator', 'cap_authenticated'])) {
        continue;
      }
      $roles[$id] = $role->label();
    }

    $email_token_help = t('Available variables are: [user:display-name], [user:account-name], [user:mail], [checklist:url], [checklist:usecase-url], [node:author], [node:title], [node:domain], [node:industry], [category:new-term-notification].');
    $config = $this->config('ai_checklist.settings');

    $selected_users = $config->get('selected_users');
    $selected_other_users = $config->get('selected_other_users');
    $default_users = [];
    $default_other_users = [];

    if (NULL != $selected_users) {
      foreach ($selected_users as $user) {
        if (isset($user['target_id']) && !empty($user['target_id'])) {
          $default_users[] = User::load($user['target_id']);
          $to[] = User::load($user['target_id'])->get('mail')->value;
        }
      }
    }

    if (NULL != $selected_other_users) {
      foreach ($selected_other_users as $other_user) {
        if (isset($other_user['target_id']) && !empty($other_user['target_id'])) {
          $default_other_users[] = User::load($other_user['target_id']);
          $other_to[] = User::load($other_user['target_id'])->get('mail')->value;
        }
      }
    }

    $add_contributor_access_users = $config->get('users_with_add_contributor_access');
    $contributor_access_users = [];
    if (NULL != $add_contributor_access_users) {
      foreach ($add_contributor_access_users as $contrib_user) {
        if (isset($contrib_user['target_id']) && !empty($contrib_user['target_id'])) {
          $contributor_access_users[] = User::load($contrib_user['target_id']);
        }
      }
    }

    $email_body_author = $config->get('email_body_by_author');
    // Set default email body by author value.
    $email_body_author_content = isset($email_body_author['value']) ? $email_body_author['value'] : ''; 

    $email_body_author_update = $config->get('email_body_by_author_update');
    // Set default email body by author value.
    $email_body_author_content_update = isset($email_body_author_update['value']) ? $email_body_author_update['value'] : '';

    $email_body_author_format_update = isset($email_body_author_update['format']) ? $email_body_author_update['format'] : 'basic_html';
    // Set default email body by author format.
    $email_body_author_format = isset($email_body_author['format']) ? $email_body_author['format'] : 'basic_html';

    $email_body_reviewer = $config->get('email_body_by_reviewer');
    // Set default email body by reviewer value.
    $email_body_reviewer_content = isset($email_body_reviewer['value']) ? $email_body_reviewer['value'] : '';
    // Set default email body by reviewer format.
    $email_body_reviewer_format = isset($email_body_reviewer['format']) ? $email_body_reviewer['format'] : 'basic_html';

    $published_email_body = $config->get('published_email_body');
    // Set default email body by reviewer value.
    $published_email_body_content = isset($published_email_body['value']) ? $published_email_body['value'] : '';
    // Set default email body by reviewer format.
    $published_email_body_format = isset($published_email_body['format']) ? $published_email_body['format'] : 'basic_html';

    // republish mail
    $republished_email_body = $config->get('republished_email_body');
    // Set default email body by reviewer value.
    $republished_email_body_content = isset($republished_email_body['value']) ? $republished_email_body['value'] : '';
    // Set default email body by reviewer format.
    $republished_email_body_format = isset($republished_email_body['format']) ? $republished_email_body['format'] : 'basic_html';

    $form['ai_checklist'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Checklist'),
    ];

    /** AI Checklist reviewer Configurations **/

    $form['checklist_reviewer_config'] = [
      '#type' => 'details',
      '#title' => t('Checklist Reviewer'),
      '#description' => t('Select who can review the checklist. You can either select role or individual users as reviewer of checklist'),
      '#group' => 'ai_checklist',
    ];

    $form['checklist_reviewer_config']['reviewers_type'] = [
      '#type' => 'radios',
      '#title' => t('Select Reviewer'),
      '#options' => ['roles' => 'By Role', 'users' => 'By Users', 'industry_domain_lead' => 'By Industry and Domain Lead'],
      '#default_value' => (NULL != $config->get('reviewers_type')) ? $config->get('reviewers_type') : 'roles',
    ];

    $form['checklist_reviewer_config']['selected_roles'] = [
      '#type' => 'checkboxes',
      '#title' => t('Roles'),
      '#default_value' => $config->get('selected_roles'),
      '#options' => $roles,
      '#states' => [
        'visible' => [
          [':input[name="reviewers_type"]' => ['value' => 'roles']],
        ],
      ],
    ];
    $form['checklist_reviewer_config']['selected_users'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Select Users'),
      '#target_type' => 'user',
      '#default_value' => $default_users,
      '#states' => [
        'visible' => [
          [':input[name="reviewers_type"]' => ['value' => 'users']],
        ],
      ],
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'role' => $roles,
        ],
      ],
      '#tags' => TRUE,
    ];

    $form['checklist_reviewer_config']['selected_other_users'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Additional users for notification'),
      '#target_type' => 'user',
      '#description' => 'Please add users who will get notified along with Industry and domain leaders about checklist submission. Use comma(,) to sepearte multiple users.',
      '#default_value' => $default_other_users,
      '#states' => [
        'visible' => [
          [':input[name="reviewers_type"]' => ['value' => 'industry_domain_lead']],
        ],
      ],
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'role' => $roles,
        ],
      ],
      '#tags' => TRUE,
    ];

    $form['checklist_reviewer_config']['selected_fallback_roles'] = [
      '#type' => 'checkboxes',
      '#title' => t('Fallback Roles'),
      '#description' => 'Please select roles whose users should get notify in case of Industry or Domain leaders doesn\'t exists',
      '#default_value' => $config->get('selected_fallback_roles'),
      '#options' => $roles,
      '#states' => [
        'visible' => [
          [':input[name="reviewers_type"]' => ['value' => 'industry_domain_lead']],
        ],
      ],
    ];

    /** Configuration to give access to users to add themself as a contributor while reviewing the checklist **/

    $form['contributor_access'] = [
      '#type' => 'details',
      '#title' => t('Add Reviewer as Contributor'),
      '#description' => t('Allow users who can add themself as a contributor from checklist form while review the checklist. Multiple users should be seprated by comma (,)'),
      '#group' => 'ai_checklist',
    ];

    $form['contributor_access']['users_with_add_contributor_access'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Select Users'),
      '#target_type' => 'user',
      '#default_value' => $contributor_access_users,

      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'filter' => [
          'role' => $roles,
        ],
      ],
      '#tags' => TRUE,
    ];

    /** Confirmation Email to User template **/
    $form['checklist_submitted_author'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template - Checklist submitted by author'),
      '#description' => t('Add notification email messages sent to <em>reviewer</em> after checklist submission by content author/contributor.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_checklist',
    ];

    $form['checklist_submitted_author']['email_subject_by_author'] = [
      '#type' => 'textfield',
      '#title' => t('Subject'),
      '#default_value' => $config->get('email_subject_by_author'),
      '#maxlength' => 180,
    ];

    $form['checklist_submitted_author']['email_body_by_author'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $email_body_author_content,
      '#format' => $email_body_author_format,
    ]; 

    $form['checklist_submitted_author']['email_subject_by_author_update'] = [
      '#type' => 'textfield',
      '#title' => t('Subject:Re-submit for review'),
      '#default_value' => $config->get('email_subject_by_author_update'),
      '#maxlength' => 180,
    ];

    $form['checklist_submitted_author']['email_body_by_author_update'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template:Re-submit for review'),
      '#default_value' => $email_body_author_content_update,
      '#format' => $email_body_author_format_update,
    ];

    /** Notification Email to Administrator template **/
    $form['checklist_submitted_reviewer'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template - Checklist submitted by reviewer'),
      '#description' => t('Add notification email messages sent to <em>author</em> after checklist reviewed by reviewer.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_checklist',
    ];

    $form['checklist_submitted_reviewer']['email_subject_by_reviewer'] = [
      '#type' => 'textfield',
      '#title' => t('Subject'),
      '#default_value' => $config->get('email_subject_by_reviewer'),
      '#maxlength' => 180,
    ];
    $form['checklist_submitted_reviewer']['email_body_by_reviewer'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $email_body_reviewer_content,
      '#format' => $email_body_reviewer_format,
    ];

    /** Published content notification email to Author template **/
    $form['content_published_email'] = [
      '#type' => 'details',
      '#title' => t('Notification Email Template - Content published by reviewer'),
      '#description' => t('Add notification email messages sent to <em>author</em> after content published by reviewer from checklist form.') . '<br/><br/>' . $email_token_help,
      '#group' => 'ai_checklist',
    ];

    $form['content_published_email']['published_email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Subject'),
      '#default_value' => $config->get('published_email_subject'),
      '#maxlength' => 180,
    ];

    $form['content_published_email']['published_email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $published_email_body_content,
      '#format' => $published_email_body_format,
    ];

    // republish mail
    $form['content_published_email']['republished_email_subject'] = [
      '#type' => 'textfield',
      '#title' => t('Subject'),
      '#default_value' => $config->get('republished_email_subject'),
      '#maxlength' => 180,
    ];

    $form['content_published_email']['republished_email_body'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $republished_email_body_content,
      '#format' => $republished_email_body_format,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ai_checklist.settings');

    $config->set('reviewers_type', $form_state->getValue('reviewers_type'));
    $config->set('selected_roles', $form_state->getValue('selected_roles'));
    $config->set('selected_users', $form_state->getValue('selected_users'));
    $config->set('selected_fallback_roles', $form_state->getValue('selected_fallback_roles'));
    $config->set('users_with_add_contributor_access', $form_state->getValue('users_with_add_contributor_access'));
    $config->set('selected_other_users', $form_state->getValue('selected_other_users'));

    $config->set('email_subject_by_author', $form_state->getValue('email_subject_by_author'));
    $config->set('email_body_by_author', $form_state->getValue('email_body_by_author'));

    $config->set('email_subject_by_reviewer', $form_state->getValue('email_subject_by_reviewer'));
    $config->set('email_body_by_reviewer', $form_state->getValue('email_body_by_reviewer'));

    $config->set('published_email_subject', $form_state->getValue('published_email_subject'));
    $config->set('published_email_body', $form_state->getValue('published_email_body'));

    $config->set('email_subject_by_author_update', $form_state->getValue('email_subject_by_author_update'));
    $config->set('email_body_by_author_update', $form_state->getValue('email_body_by_author_update'));
    $config->set('republished_email_subject', $form_state->getValue('republished_email_subject'));
    $config->set('republished_email_body', $form_state->getValue('republished_email_body'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
