<?php

namespace Drupal\notification_message\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\notification_message\NotificationMessageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define the notification message queue block.
 *
 * @Block(
 *   id = "notification_message",
 *   admin_label = @Translation("Notification messages")
 * )
 */
class NotificationMessageBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * @var \Drupal\notification_message\NotificationMessageManagerInterface
   */
  protected $notificationMessageManager;

  /**
   * NotificationMessageBlock constructor.
   *
   * @param array $configuration
   *   The block configurations.
   * @param $plugin_id
   *   The block plugin identifier.
   * @param $plugin_definition
   *   The block plugin definition.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entity_display_repository
   * @param \Drupal\notification_message\NotificationMessageManagerInterface $notification_message_manager
   *   The notification message service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityDisplayRepositoryInterface $entity_display_repository,
    NotificationMessageManagerInterface $notification_message_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityDisplayRepository = $entity_display_repository;
    $this->notificationMessageManager = $notification_message_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_display.repository'),
      $container->get('notification_message.manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return [
      'notification_message' => [
        'type' => [],
        'display_mode' => 'full',
      ]
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritDoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['notification_message'] = [
      '#type' => 'details',
      '#title' => $this->t('Notification Message'),
      '#open' => TRUE,
    ];
    $form['notification_message']['display_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Display Mode'),
      '#required' => TRUE,
      '#description' => $this->t('Select the notification message view mode.'),
      '#options' => $this->getDisplayModeOptions(),
      '#default_value' => $this->getNotificationMessageDisplayMode()
    ];
    $form['notification_message']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Message Type'),
      '#multiple' => TRUE,
      '#description' => $this->t('Select the notification message types. 
        <br/> <strong>Note:</strong> If no message types are selected then all 
        valid messages are rendered.'),
      '#options' => $this->getNotificationMessageTypeOptions(),
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $this->getNotificationMessageType()
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['notification_message'] = $form_state->getValue('notification_message');
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    return [
      '#block' => $this,
      '#theme' => 'notification_messages',
      '#messages' => $this->renderNotificationMessages(),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Get notification message type..
   *
   * @return string
   *   The notification message type.
   */
  public function getNotificationMessageType() {
    return $this->configuration['notification_message']['type'];
  }

  /**
   * Get notification message display mode.
   *
   * @return string
   *   The notification message display view mode.
   */
  public function getNotificationMessageDisplayMode() {
    return $this->configuration['notification_message']['display_mode'];
  }

  /**
   * Render the notification messages.
   *
   * @return array
   *   An array of render array for the messages.
   */
  protected function renderNotificationMessages() {
    return $this->notificationMessageManager->viewNotificationMessages(
      $this->getNotificationMessageDisplayMode(),
      $this->getNotificationMessageType()
    );
  }

  /**
   * Get notification message display mode options.
   *
   * @return array
   *   An array of display mode options.
   */
  protected function getDisplayModeOptions() {
    return $this->entityDisplayRepository
      ->getViewModeOptions('notification_message');
  }

  /**
   * Get the notification message type options.
   *
   * @return array
   *   An array of notification message types.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getNotificationMessageTypeOptions() {
    return $this->notificationMessageManager
      ->getNotificationMessageTypeOptions();
  }
}
