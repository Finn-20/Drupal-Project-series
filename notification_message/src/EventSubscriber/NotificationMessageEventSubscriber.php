<?php

namespace Drupal\notification_message\EventSubscriber;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\notification_message\NotificationMessageManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Define the notification message event subscriber.
 */
class NotificationMessageEventSubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\notification_message\NotificationMessageManagerInterface
   */
  protected $notificationMessageManager;

  /**
   * Notification message event subscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\notification_message\NotificationMessageManagerInterface $notification_message_manager
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    NotificationMessageManagerInterface $notification_message_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->notificationMessageManager = $notification_message_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onRequest']
    ];
  }

  /**
   * React on the kernel request events.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The kernel response event object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function onRequest(GetResponseEvent $event) {
    $request = $event->getRequest();

    if ($entity = $this->getRequestEntity($request)) {
      $route_name = $request->attributes->get('_route', NULL);

      if ($route_name === "entity.{$entity->getEntityTypeId()}.canonical") {
        $contexts = $this->getContextsFromRepository();
        $this->notificationMessageManager->addNotificationMessages(
          $contexts
        );
      }
    }
  }

  /**
   * Get the entity for the given request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request object.
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface
   *   Return the entity found in the request; otherwise FALSE.
   */
  protected function getRequestEntity(Request $request) {
    $definitions = $this->entityTypeManager->getDefinitions();

    foreach ($definitions as $entity_type_id => $definition) {
      if ($definition instanceof ContentEntityTypeInterface
        && $request->attributes->has($entity_type_id)) {
        return $request->attributes->get($entity_type_id);
      }
    }

    return FALSE;
  }

  /**
   * Get contexts from the context providers.
   *
   * @return array
   *   An array of contexts discovered on runtime.
   */
  protected function getContextsFromRepository() {
    /** @var \Drupal\Core\Plugin\Context\LazyContextRepository $context_repository */
    $context_repository = \Drupal::service('context.repository');

    $context_ids = array_keys(
      $context_repository->getAvailableContexts()
    );
    $contexts = [];

    foreach ($context_repository->getRuntimeContexts($context_ids) as $name => $context) {
      $unqualified_context_id = substr($name, strpos($name, ':') + 1);
      $contexts[$unqualified_context_id] = $context;
    }

    return $contexts;
  }
}
