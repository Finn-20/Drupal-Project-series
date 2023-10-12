<?php

namespace Drupal\notification_message\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Define the notification message access control handler.
 */
class NotificationMessageAccess extends EntityAccessControlHandler {

  /**
   * {@inheritDoc}
   */
  protected function checkAccess(
    EntityInterface $entity,
    $operation,
    AccountInterface $account
  ) {
    if ($operation === 'view') {
      if ($entity instanceof EntityPublishedInterface && $entity->isPublished()) {
        return AccessResult::allowed()->addCacheableDependency($entity);
      }
    } else {
      return AccessResult::allowedIfHasPermission(
        $account, $entity->getEntityType()->getAdminPermission()
      );
    }

    return AccessResult::neutral();
  }
}
