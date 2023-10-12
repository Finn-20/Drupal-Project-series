<?php

namespace Drupal\ai_account\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\simplenews\Entity\Newsletter;

/**
 * Provides route responses for the Briefcase module.
 */
class NewsletterListForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_newsletter_list_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    $form['#title'] = $this->t('News Letter');

    $header = ['Item', 'Description', 'News Letter'];
    // Add the headers.
    $form['asso_bulkimage_upload'] = [
      '#type' => 'table',
      '#title' => 'Sample Table',
      '#header' => $header,
    ];
    $query = db_select('simplenews_subscriber', 's');
    $query->fields('s', ['id', 'uid'])
      ->fields('u', ['subscriptions_target_id', 'subscriptions_status'])
      ->condition('s.uid', $uid, '=')
      ->condition('u.subscriptions_status', 1, '=');
    $query->join('simplenews_subscriber__subscriptions', 'u', 'u.entity_id = s.id');
    $results = $query->execute()->fetchAll();
    foreach ($results as $res) {
      $id = $res->id;
      $sub_id = $res->subscriptions_target_id;

      $query = db_select('node__simplenews_issue', 'sb');
      $query->fields('sb', ['entity_id'])
        ->condition('sb.simplenews_issue_target_id', $sub_id, '=');
      $node_del = $query->execute()->fetchAll();
      foreach ($node_del as $val) {
        $node = Node::load($val->entity_id);
        $title = $node->title->value;
        $desc = $node->get('body')->value;
        $form['asso_bulkimage_upload'][$val->entity_id]['assoc_title'] = [
          '#type' => 'markup',
          '#markup' => $title,
        ];
        $form['asso_bulkimage_upload'][$val->entity_id]['assoc_desc'] = [
          '#type' => 'markup',
          '#markup' => $desc,
        ];
        $newslet = $node->get('simplenews_issue')->getValue();
        foreach ($newslet as $tarval) {
          $newsletters = Newsletter::load($tarval['target_id']);
          $news_letter_name[] = $newsletters->name;
          $form['asso_bulkimage_upload'][$val->entity_id]['assoc_news'] = [
            '#type' => 'markup',
            '#markup' => $newsletters->name,
          ];
        }
      }
    }
    /* $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Apply Bulk Image Update'),
    );  */
    return $form;
  }

  /**
   * Submit function.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
