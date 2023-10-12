<?php

namespace Drupal\user_notification\EventSubscriber;

use Drupal\alert_events\Event\EntityEvent;
use Drupal\alert_events\EventSubscriber\EntityEventSubscriber;
use Drupal\user_notification\services\NotificationUtil;

/**
 * Class UserAlertSubscriber.
 */
class UserAlertSubscriber extends EntityEventSubscriber {

  public $notification;

  const UPDATE = 'Update';
  const INSERT = 'Create';
  const DELETE = 'Delete';

  /**
   * {@inheritdoc}
   */
  public function onEntityUpdate(EntityEvent $event) {
    $this->notification->setEntity($event, self::UPDATE)->createNotification();
  }

  /**
   * {@inheritdoc}
   */
  public function onEntityInsert(EntityEvent $event) { 
    $this->notification->setEntity($event, self::INSERT)->createNotification();
  }

  /**
   * {@inheritdoc}
   */
  public function onEntityDelete(EntityEvent $event) {
    $this->notification->setEntity($event, self::DELETE)->createNotification();
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(NotificationUtil $notification) {
    $this->notification = $notification;

  }

}
