<?php

namespace Drupal\ai_change_author\Form;

use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class ReplacedAuthorForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_changed_author_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $replace_auth_by = NULL) {

    $header = [
      'nodeid' => '',
      'node_title' => 'Title',
      'content_type' => 'Content type',
      'author_name' => 'Author Name',
      'updated_date' => 'Updated',
    ];

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#tableselect' => FALSE,
    ];

    $query = db_select('node_field_data', 'AR');
    $query->fields('AR');
    $query->condition('AR.uid', $replace_auth_by, '=');
    $query->condition('AR.type', ['use_case_or_accelerator', 'asset'], 'IN');
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $nid = $result->nid;
      // Pass your uid.
      $account = User::load($result->uid);
      $user_name = $account->getDisplayName();
      $form['table'][$nid]['nodeid'] = [
        '#type' => 'hidden',
        '#value' => $nid,
      ];
      $form['table'][$nid]['node_title'] = [
        '#plain_text' => $result->title,
      ];
      $form['table'][$nid]['content_type'] = [
        '#plain_text' => $result->type,
      ];
      $form['table'][$nid]['author_name'] = [
        '#plain_text' => $user_name,
      ];
      $form['table'][$nid]['updated_date'] = [
        '#plain_text' => format_date($result->changed, 'custom', 'm/d/Y-H:i'),
      ];
    }

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

  }

}
