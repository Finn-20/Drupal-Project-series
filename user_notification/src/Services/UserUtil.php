<?php

namespace Drupal\user_notification\Services;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class UserUtil.
 */
class UserUtil {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a new UserUtil.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory;
  }

  /**
   * Validate passed content type is configured or not for Notifications.
   *
   * @return bool
   *   TRUE if content type is configured and FALSE otherwise.
   */
  public function isValidContenttype() {
    $config = $this->config->getEditable('user_notification.config_settings')->get('user_notification.content_types');

    if (isset($config[$this->entity->bundle()]) && !empty($config[$this->entity->bundle()])) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Sets the form entity.
   *
   * @return $this
   */
  public function setEntity($entity) {
    $this->entity = $entity;
    return $this;
  }

  /**
   * Validate email address and checks it's exist in the system.
   */
  public function isValidEmail() {

  }

}
