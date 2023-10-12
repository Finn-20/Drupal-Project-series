<?php

namespace Drupal\notification_message;

use Drupal\notification_message\Entity\NotificationMessageInterface;

/**
 * Define the notification message collection service.
 */
class NotificationMessageCollection {

  /**
   * @var array
   */
  protected $collection = [];

  /**
   * @var \Drupal\notification_message\NotificationMessageCollection
   */
  private static $instance;

  private function __construct() {}

  /**
   * Get notification message collection queue.
   *
   * @return \Drupal\notification_message\NotificationMessageCollection
   *   The notification message collection instance.
   */
  public static function queue() {
    if (!self::$instance) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  /**
   * Get notification message count.
   *
   * @return int
   *   The number of message notifications that been added.
   */
  public function count() {
    return count($this->collection);
  }

  /**
   * Get notification message collection.
   *
   * @return \ArrayIterator
   */
  public function getCollection() {
    return new \ArrayIterator($this->collection);
  }

  /**
   * Add the notification message to the collection.
   *
   * @param \Drupal\notification_message\Entity\NotificationMessageInterface $message
   *   The notification message entity.
   *
   * @return \Drupal\notification_message\NotificationMessageCollection
   */
  public function addNotificationMessage(
    NotificationMessageInterface $message
  ) {
    $name = $message->id();

    if (!isset($this->collection[$name])) {
      $this->collection[$name] = $message;
    }

    return $this;
  }

  /**
   * Remove the notification message from the collection.
   *
   * @param \Drupal\notification_message\Entity\NotificationMessageInterface $message
   *   The notification message entity.
   *
   * @return \Drupal\notification_message\NotificationMessageCollection
   */
  public function removeNotificationMessage(
    NotificationMessageInterface $message
  ) {
    $name = $message->id();

    if (isset($this->collection[$name])) {
      unset($this->collection[$name]);
    }

    return $this;
  }
}
