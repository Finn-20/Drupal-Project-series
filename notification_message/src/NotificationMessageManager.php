<?php

namespace Drupal\notification_message;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Define the notification message manager.
 */
class NotificationMessageManager implements NotificationMessageManagerInterface  {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManger;

  /**
   * Define the notification message manager constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->entityTypeManger = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function notificationMessageQueue() {
    return NotificationMessageCollection::queue();
  }

  /**
   * {@inheritDoc}
   */
  public function getNotificationMessageQueue() {
    return static::notificationMessageQueue();
  }

  /**
   * {@inheritDoc}
   */
  public function viewNotificationMessages($mode, array $types = []) {
    $messages = [];

    /** @var \Drupal\notification_message\Entity\NotificationMessage $message */
    foreach ($this->getNotificationMessageQueue()->getCollection() as $message) {
      if (!empty($types) && !in_array($message->bundle(), $types)) {
        continue;
      }
      $messages[] = $message->view($mode);
    }

    return $messages;
  }

  /**
   * {@inheritDoc}
   */
  public function addNotificationMessages(array $contexts = []) {
    $queue = $this->getNotificationMessageQueue();

    /** @var \Drupal\notification_message\Entity\NotificationMessage $message */
    foreach ($this->loadNotificationMessage() as $message) {
      if (!$message->isPublished()
        || !$message->evaluateConditions($contexts)) {
        continue;
      }
      $queue->addNotificationMessage($message);
    }

    return $this;
  }

  /**
   * {@inheritDoc
   */
  public function getNotificationMessageTypeOptions() {
    $options = [];

    /** @var \Drupal\notification_message\Entity\NotificationMessageType $entity */
    foreach ($this->notificationMessageTypeStorage()->loadMultiple() as $id => $entity) {
      $options[$id] = $entity->label();
    }

    return $options;
  }

  /**
   * Load all the notification message.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   An array of notification messages.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadNotificationMessage() {
    return $this->entityTypeManger
      ->getStorage('notification_message')
      ->loadMultiple();
  }

  /**
   * The notification message type storage instance.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   Get the notification message type storage instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function notificationMessageTypeStorage() {
    return $this->entityTypeManger->getStorage('notification_message_type');
  }
}
