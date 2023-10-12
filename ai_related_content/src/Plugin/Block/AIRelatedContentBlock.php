<?php

namespace Drupal\ai_related_content\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Provides a block with a Related Content.
 *
 * @Block(
 *   id = "ai_related_content_block",
 *   admin_label = @Translation("AI Related Content Block"),
 * )
 */
class AIRelatedContentBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $related_usecases = [];
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    
    $current_node = \Drupal::routeMatch()->getParameter('node');
    
    $taxonomy_fields = ['field_usecase_industry','field_usecase_domain', 'field_offer', 'field_category', 'field_usecase_framework', 'field_usecase_aifeatures', 'field_usecase_technology'];
    $data = [];
    if ($current_node instanceof NodeInterface) {
      $current_nid = $current_node->id();
      $node  = Node::load($current_nid);
      
      if (null != $node) {
        $usecase_para_ref = $node->toArray();
        foreach ($taxonomy_fields as $field) {
          $associated_tids = [];
          if (isset($usecase_para_ref[$field]) && !empty($usecase_para_ref[$field])) {
            foreach ($usecase_para_ref[$field] as $term_ref) {
              if (isset($term_ref['target_id']) && !empty($term_ref['target_id'])) {
                if (!isset($associated_tids[$term_ref['target_id']]) || empty($associated_tids[$term_ref['target_id']])) {
                  $associated_tids[$term_ref['target_id']] = $term_ref['target_id'];
                }
              }
            }
          }
          if (isset($associated_tids) && !empty($associated_tids)) {
            $this->getRelatedNodesByTerms($field, $associated_tids, $current_nid, $data);
          }
        }
      }
    }
    shuffle($data); // Shuffle array to show different related content everytime.
    $count = 0;
    foreach ($data as $related_usecase) {
      if ($count > 2)
        break;
      $related_usecases[] = $related_usecase;
      $count++;
    }   
    
    return [
      'related_usecases' => $related_usecases,
    ];
  }

  public function getRelatedNodesByTerms($field, $associated_tids, $current_nid, &$data = []) {
    $base_table = 'node__' . $field;
    $base_field = $field . '_target_id';
    if (isset($associated_tids) && !empty($associated_tids)) {
      $query = db_select($base_table, 't');
      $query->fields('t', ['entity_id'])
      ->fields('n', ['nid', 'title'])
      ->fields('s', ['field_solution_value']);
      $query->innerJoin('node_field_data', 'n', 'n.nid = t.entity_id');
      $query->innerJoin('node__field_solution', 's', 's.entity_id = n.nid');
    
      $query->condition('t.' . $base_field, $associated_tids, 'IN');
      $query->condition('n.nid', $current_nid, '<>');
      $query->condition('n.moderation_state', 'published', '=');
      
      $query->orderBy('n.changed', 'DESC');
      
      $results = $query->execute()->fetchAll();
      foreach ($results as $result) {
        if (isset($result->nid) && !empty($result->nid)) {
          if (!isset($data[$result->nid]) || empty($data[$result->nid])) {
            $data[$result->nid]['url'] = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $result->nid);
            $data[$result->nid]['title'] = (strlen(trim(strip_tags($result->title))) > 50) ? substr(trim(strip_tags($result->title)), 0, 50) . '...' : trim(strip_tags($result->title));
            $data[$result->nid]['link_title'] = trim(strip_tags($result->title));
            $data[$result->nid]['solution'] = (strlen(trim(strip_tags($result->field_solution_value, '&nbsp;'))) > 100) ? substr(trim(strip_tags($result->field_solution_value, '&nbsp;')), 0, 100) . '...' : trim(strip_tags($result->field_solution_value, '&nbsp;'));   
          }
        }
      }
    }
    return $data;
  }
  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    
  }
  public function getCacheMaxAge() {
    return 0;
  }
}