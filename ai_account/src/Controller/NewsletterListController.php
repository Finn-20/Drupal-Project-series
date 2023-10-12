<?php

namespace Drupal\ai_account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\simplenews\Entity\Newsletter;

/**
 * Provides route responses for the Briefcase module.
 */
class NewsletterListController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function newslettersublist() {
    /* $block = \Drupal\block\Entity\Block::load('simplenewssubscription');
    $block_content = \Drupal::entityManager()
    ->getViewBuilder('block')
    ->view($block);
    return array('#markup' => \Drupal::service('renderer')->renderRoot($block_content)); */
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    $rows = [];
    $query = db_select('simplenews_subscriber', 's');
    $query->fields('s', ['id', 'uid'])
      ->fields('u', ['subscriptions_target_id', 'subscriptions_status'])
      ->condition('s.uid', $uid, '=')
      ->condition('u.subscriptions_status', 1, '=');
    $query->join('simplenews_subscriber__subscriptions', 'u', 'u.entity_id = s.id');
    $results = $query->execute()->fetchAll();
    if (!empty($results)) {
      $headers = ['News Letter', 'Issue Title', 'Published Date'];
      foreach ($results as $res) {
        $id = $res->id;
        $sub_id = $res->subscriptions_target_id;
        $query = db_select('node__simplenews_issue', 'sb');
        $query->fields('sb', ['entity_id'])
          ->condition('sb.simplenews_issue_target_id', $sub_id, '=');
        $node_del = $query->execute()->fetchAll();
        foreach ($node_del as $val) {
          $result_array = [];
          $nid          = $val->entity_id;
          $node         = Node::load($nid);
          $title        = $node->title->value;
          $desc         = substr($node->get('body')->value, 0, 100) . '...';

          $newslet = $node->get('simplenews_issue')->getValue();
          $newsletters = Newsletter::load($newslet[0]['target_id']);
          $newsletrows = [];
          $newsletrows = [
            'data' => $newsletters->name,
            'class' => 'news-letter-newshead',
          ];
          $rows[$nid]['news_letter_news'] = $newsletrows;

          $node_path = $node->toUrl('canonical', [
            'absolute' => TRUE,
            'language' => $node->language(),
          ])->toString();
          $rows[$nid]['title'] = [
            'data' => [
              '#markup' => '<a href="' . $node_path . '">' . $title . '</a>',
            ],
            'class' => 'news-letter-title',
          ];
          $rows[$nid]['news_letter_desc'] = [
            'data' => [
              '#markup' => \Drupal::service('date.formatter')->format($node->getChangedTime(), 'custom', 'd-m-Y'),
            ],
            'class' => 'news-letter-desc',
          ];
        }
      }
      // Print "<pre>";print_r($rows);exit;
      // Return render array for table.
      return [
        '#theme' => 'table',
        '#header' => $headers,
        '#rows' => $rows,

      ];
    }
    else {
      return [
        '#theme' => 'table',
        '#header' => $headers,
       // '#rows' => $rows,
      ];
    }
  }

}
