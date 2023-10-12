<?php

namespace Drupal\user_notification\Services;

use Drupal\node\Entity\Node;

/**
 * Class NotificationUtil.
 */
class NotificationUtil {

  protected $entity;
  protected $opt;

  /**
   * Helper function to create notification.
   */
  public function createNotification() {

    if ($this->entity->getEntity() instanceof Node) {
      $query = \Drupal::entityTypeManager()->getStorage('user_notification')->getQuery();
      $query->condition('entity_id', $this->entity->getEntity()->id(), '=');
      $entity_ids = $query->execute();

      if (empty($entity_ids)) {
        if ($this->entity->getEntity()->bundle() == 'my_idea') {
          $notification_myidea = [
            'title' => $this->entity->getEntity()->getTitle(),
            'entity_id' => $this->entity->getEntity()->id(),
            'entity_type' => $this->entity->getEntity()->bundle(),
            'status' => 1,
            'operation' => $this->opt,
          ];
          \Drupal::entityTypeManager()->getStorage('user_notification')->create($notification_myidea)->save();
        }
        if ($this->entity->getEntity()->bundle() == 'push_notification_description' && $this->entity->getEntity()->get('status')->getString() == 1) {
          $notification_push = [
            'title' => $this->entity->getEntity()->getTitle(),
            'entity_id' => $this->entity->getEntity()->id(),
            'entity_type' => $this->entity->getEntity()->bundle(),
            'status' => TRUE,
            'operation' => $this->opt,
          ];
          \Drupal::entityTypeManager()->getStorage('user_notification')->create($notification_push)->save();
        }
        if ($this->entity->getEntity()->bundle() == 'use_case_or_accelerator') {
          if ($this->entity->getEntity()->get('moderation_state')->getString() == 'published') {
            $notification = [
              'title' => $this->entity->getEntity()->getTitle(),
              'entity_id' => $this->entity->getEntity()->id(),
              'entity_type' => $this->entity->getEntity()->bundle(),
              'status' => TRUE,
              'operation' => $this->opt,
            ];
            \Drupal::entityTypeManager()->getStorage('user_notification')->create($notification)->save();
          }
        }
      }
      else {
        if ($this->entity->getEntity()->get('moderation_state')->getString() == 'needs_review') {

          // If the current userid is same as the asset user id then only process this request.
          if (\Drupal::currentUser()->id() == $this->entity->getEntity()->get('uid')->getString()) {

            foreach ($entity_ids as $entity_id) {
              // Operation types are tue asset author and the non-asset author i.e. admin, primary and secondary owner.
              $notification_properties = ['id' => $entity_id, 'entity_id' => $this->entity->getEntity()->id(),
                'operation' => ['draft-asset-opt', 'draft-asset-opt-non-aa', 'pending-review-asset-opt', 'pending-review-asset-opt-non-aa']];
              $remove_notifications = \Drupal::entityTypeManager()->getStorage('user_notification')
                ->loadMultiple($notification_properties);
              foreach ($remove_notifications as $remove_notification) {
                $remove_notification->delete();
                \Drupal::database()->delete('ai_content_notifications_logs')
                  ->condition('nid', $entity_id, '=')
                  ->execute();
              }
            }
          }
        }
      }
    }
  }

  /**
   * Helper function to update notification.
   */
  public function updateNotification() {

  }

  /**
   * Sets the entity instance for this mapper.
   *
   * @return $this
   */
  public function setEntity($event, $opt) {
    $this->opt = $opt;
    $this->entity = $event;
    return $this;
  }

  public static function load($uid, $entity_id) {
    $select = db_select('nodeviewcount', 'nv');
    $select->fields('nv');
    $select->condition('uid', $uid, '=');
    $select->condition('nid', $entity_id, '=');
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

}
