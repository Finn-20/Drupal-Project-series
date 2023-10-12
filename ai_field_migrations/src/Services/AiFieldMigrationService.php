<?php

namespace Drupal\ai_field_migrations\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\State\State;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Url;

/**
 * Class BiTaggingService
 *
 * @package Drupal\bi_tagging_framework\Services
 *
 * This service is to provide helper methods for BI tagging Framework module functionality.
 */
class AiFieldMigrationService {

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $config_factory;

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

  /**
   * State service for recording information received by event listeners.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;
  
  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * @param \Drupal\Core\Database\Connection $database
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   * @param \Drupal\Core\State\State $state
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Path\AliasManagerInterface $aliasManager
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   */
  public function __construct(ConfigFactoryInterface $config_factory, Connection $database, MessengerInterface $messenger, State $state, EntityTypeManagerInterface $entity_type_manager, AliasManagerInterface $aliasManager, RequestStack $requestStack) {
    $this->config_factory = $config_factory;
    $this->database = $database;
    $this->messenger = $messenger;
    $this->state = $state;
    $this->entity_type_manager = $entity_type_manager;
    $this->aliasManager = $aliasManager;
    $this->requestStack = $requestStack;
  }

  
  /**
   * Adds a new message to the queue.
   * The messages will be displayed in the order they got added later.
   *
   * @param string $message
   *   - Message to display
   * @param string $type
   *   - Type of message. Ex. 'status', 'error', 'warning' etc.
   */
  public function addMessage($message, $type = 'status') {
    $this->messenger->addMessage($message, $type);
  }

  /**
   * Get the Variable value.
   */
  public function getStateVariableValue($key) {
    return $this->state->get($key);
  }

  /**
   * Set the Variable value.
   */
  public function setStateVariableValue($key, $value) {
    return $this->state->set($key, $value);
  }
  
  /**
   * Delete the state Variable(s) based on $key Arguments.
   * You can provide array $key to delete multiple variable at once.
   */
  public function deleteStateVariableValue($key) {
    if (is_array($key)) {
      return $this->state->deleteMultiple($key);
    }
    else {
      return $this->state->delete($key);
    }
  }

  public function getCollateralsDetails() {
    $query = "SELECT c.entity_id AS nid, c.field_collaterals_files_upload_target_id AS fid, f.filemime AS type FROM node__field_collaterals_files_upload c INNER JOIN file_managed f ON f.fid=c.field_collaterals_files_upload_target_id";
    return $this->database->query($query)->fetchAll();
  }
  
  public function getNodeDetails($nid) {
    return $this->entity_type_manager->getStorage('node')->load($nid);
  }
  
  public function getMediaDetails($id) {
    return $this->entity_type_manager->getStorage('media')->load($id);
  }
  
  
  /**
   * Get the base url of site.
   */
  public function getSiteBaseUrl() {
    return $this->requestStack->getCurrentRequest()->getScheme() . '://' . $this->requestStack->getCurrentRequest()->getHost();
  }
  
  public function getLatestRevisionIds($node_ids) {
    $nodeRevisions = [];
    $select = db_select('node_field_data', 'n');
    $select->fields('n', ['nid', 'vid', 'type'])
    ->condition('n.nid', $node_ids, 'IN');
    
    // Return the result in object format.
    $results =  $select->execute()->fetchAll();
    foreach($results as $result) {
      $nodeRevisions[] = ['nid' => $result->nid, 'vid' => $result->vid, 'type' => $result->type];
    }
    return $nodeRevisions;
  }
  
  public function addTaxonomyTermsToNodes($data_set, $table_name) {
    $return_value = NULL;
    try {
      foreach ($data_set as $data) {
        $return_value = db_insert($table_name)
        ->fields($data)
        ->execute();
      }  
    }
    catch (\Exception $e) {
      drupal_set_message(t('db_insert failed. Message = %message, query= %query', [
      '%message' => $e->getMessage(),
      '%query' => $e->query_string,
      ]
      ), 'error');
    }
    return $return_value;
  }
}
