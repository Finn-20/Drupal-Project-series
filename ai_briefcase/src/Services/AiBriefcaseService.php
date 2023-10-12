<?php

namespace Drupal\ai_briefcase\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AiBriefcaseService.
 *
 * @package Drupal\ai_briefcase\Services
 *
 * This service is to provide helper methods for BI tagging.
 */
class AiBriefcaseService {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Include the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  protected $entity_type_manager;

  protected $aliasManager;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Aibriefcase services.
   */
  public function __construct(Connection $database, MessengerInterface $messenger, EntityTypeManagerInterface $entity_type_manager, AliasManagerInterface $aliasManager, RequestStack $requestStack) {
    $this->database = $database;
    $this->messenger = $messenger;
    $this->entity_type_manager = $entity_type_manager;
    $this->aliasManager = $aliasManager;
    $this->requestStack = $requestStack;
  }

  /**
   * Adds a new message to the queue.
   *
   * @param string $message
   *   - Message to display.
   * @param string $type
   *   - Type of message. Ex. 'status', 'error', 'warning' etc.
   */
  public function addMessage($message, $type = 'status') {
    $this->messenger->addMessage($message, $type);
  }

  /**
   * Getnode details.
   */
  public function getNodeDetails($nid) {
    return $this->entity_type_manager->getStorage('node')->load($nid);
  }

  /**
   * Content flagged.
   */
  public function isContentFlagged($nid, $uid) {
    $query = $this->database->select('flagging', 'f');
    $query->fields('f', ['id']);
    $query->condition('f.uid', $uid);
    $query->condition('f.entity_id', $nid);
    $results = $query->execute()->fetchAll();
    return count($results);
  }

  /**
   * Get the base url of site.
   */
  public function getSiteBaseUrl() {
    return $this->requestStack->getCurrentRequest()->getScheme() . '://' . $this->requestStack->getCurrentRequest()->getHost();
  }

}
