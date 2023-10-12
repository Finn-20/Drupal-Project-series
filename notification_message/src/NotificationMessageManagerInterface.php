<?php

namespace Drupal\notification_message;

/**
 * Define the notification message manager interface.
 */
interface NotificationMessageManagerInterface {

  /**
   * The static message collection queue.
   *
   * @return \Drupal\notification_message\NotificationMessageCollection
   *   All the messages attached to the collection queue.
   */
  public static function notificationMessageQueue();

  /**
   * Get the message collection queue.
   *
   * @return \Drupal\notification_message\NotificationMessageCollection
   *   All the messages attached to the collection queue.
   */
  public function getNotificationMessageQueue();

  /**
   * View the collection of notification messages.
   *
   * @param $mode
   *   The message view display mode.
   * @param array $types
   *   An array of allowed message type bundles.
   *
   * @return array
   *   An array of notification messages.
   */
  public function viewNotificationMessages($mode, array $types = []);

  /**
   * Add notification messages to the message queue.
   *
   * @param array $contexts
   *   An array of context definitions.
   *
   * @return \Drupal\notification_message\NotificationMessageManager
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function addNotificationMessages(array $contexts = []);

  /**
   * Get the notification message type options.
   *
   * @return array
   *   An array of notification message type options.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNotificationMessageTypeOptions();
}
