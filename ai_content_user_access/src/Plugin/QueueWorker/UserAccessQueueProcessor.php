<?php

namespace Drupal\ai_content_user_access\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes Node Tasks.
 *
 * @QueueWorker(
 *   id = "user_access_queue_processor",
 *   title = @Translation("Providing access to users"),
 *   cron = {"time" = 10}
 * )
 */
class UserAccessQueueProcessor extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($node) {
    if (!empty($node->user_access_case)) {
      $settings = \Drupal::configFactory()->get('nodeaccess.settings');
      $role_alias = $settings->get('role_alias');
      $role_map = $settings->get('role_map');
      $grants = [];
      switch ($node->user_access_case) {
        case 'archived': 
          $grants[] = [
            'gid' => $node->getOwnerId(),
            'realm' => 'nodeaccess_uid',
            'grant_view' => 1,
            'grant_update' => 0,
            'grant_delete' => 0,
          ];
            
          if (NULL != $node->get('field_other_contributors')->getValue()) {
              foreach ($node->get('field_other_contributors')->getValue() as $contributor) {
                if (isset($contributor['target_id']) && !empty($contributor['target_id'])) {
                  $grants[] = [
                    'gid' => $contributor['target_id'],
                    'realm' => 'nodeaccess_uid',
                    'grant_view' => 1,
                    'grant_update' => 0,
                    'grant_delete' => 0,
                  ];
                }
              }
            }

          foreach ($role_alias as $id => $role) {
            if ($role['allow']) {
              $allowed_roles[] = $id;
            }
          }

          foreach ($allowed_roles as $role) {
            $grants[] = [
              'gid' => $role_map[$role],
              'realm' => 'nodeaccess_rid',
              'grant_view' => 1,
              'grant_update' => 0,
              'grant_delete' => 0,
            ];
          }
          // Save role and user grants to our own table.
          \Drupal::database()->delete('nodeaccess')
            ->condition('nid', $node->id())
            ->execute();
          foreach ($grants as $grant) {
            $id = db_insert('nodeaccess')
              ->fields([
                'nid' => $node->id(),
                'gid' => $grant['gid'],
                'realm' => $grant['realm'],
                'grant_view' => $grant['grant_view'],
                'grant_update' => $grant['grant_update'],
                'grant_delete' => $grant['grant_delete'],
              ])
              ->execute();
          }
          \Drupal::entityTypeManager()->getAccessControlHandler('node')->writeGrants($node);
          // drupal_set_message(t('Grants saved.'));.
          $tags = ['node:' . $node->id()];
          Cache::invalidateTags($tags);
          break;
        
        default:
          # code...
          break;
      }
    }
  }
}