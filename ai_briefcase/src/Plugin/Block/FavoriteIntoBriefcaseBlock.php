<?php

namespace Drupal\ai_briefcase\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "favorite_into_briefcase_block",
 *   admin_label = @Translation("Favorite Into Briefcase Block"),
 * )
 */
class FavoriteIntoBriefcaseBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();

    $current_node = \Drupal::routeMatch()->getParameter('node');

    if ($current_node instanceof NodeInterface) {
      $current_nid = $current_node->id();
      $node_title = $current_node->title->value;
    }

    $query = \Drupal::entityQuery('node')
    // Published or not.
      ->condition('status', 1)
    // Content type.
      ->condition('type', 'briefcase')
      ->condition('uid', $uid);

    $nids = $query->execute();

    $briefcase_nodes = [];

    foreach ($nids as $nid) {
      $checked = 0;
      $node = Node::load($nid);
      $nid = $node->id();
      $title = $node->title->value;

      $favorites = $node->get('field_favorites')->getValue();

      foreach ($favorites as $favorite) {
        if (isset($favorite['target_id']) && $favorite['target_id'] == $current_nid) {
          $checked = 1;
          break;
        }
      }

      $briefcase_nodes[] = ['nid' => $nid, 'title' => $title, 'checked' => $checked];
    }

    return [
      'nid' => $nid,
      'current_nid' => $current_nid,
      'node_title' => $node_title,
      'briefcase_ids' => $briefcase_nodes,
      'module_path' => drupal_get_path('module', 'ai_briefcase'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * Block form.
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

  /**
   * Get cachemax age.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
