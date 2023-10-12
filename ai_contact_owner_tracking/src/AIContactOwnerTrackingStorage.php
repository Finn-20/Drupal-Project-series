<?php

namespace Drupal\ai_contact_owner_tracking;

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use \Drupal\Core\Session\AccountInterface;
/**
 * Class SimplePopupBlocksStorage.
 */
class AIContactOwnerTrackingStorage {

  /**
   * Save an entry in the ai_contact_owner_tracking table.
   *
   * @param array $entry
   *   An array containing all the fields of the database record.
   *
   * @return int
   *   The number of updated rows.
   *
   * @throws \Exception
   *   When the database insert fails.
   *
   * @see db_insert()
   */
  public static function insert(array $entry) {
    $return_value = NULL;
    try {
      $return_value = db_insert('ai_contact_owner_tracking')
        ->fields($entry)
        ->execute();
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

  /**
   * Update an entry in the database.
   *
   * @param array $entry
   *   An array containing all the fields of the item to be updated.
   *
   * @return int
   *   The number of updated rows.
   *
   * @see db_update()
   */
  public static function update(array $entry, $key = 'tracking_id') {
    try {
      // db_update()...->execute() returns the number of rows updated.
      $count = db_update('ai_contact_owner_tracking')
        ->fields($entry)
        ->condition($key, $entry[$key])
        ->execute();
    }
    catch (\Exception $e) {
      drupal_set_message(t('db_update failed. Message = %message', [
        '%message' => $e->getMessage(),
      ]), 'error');
    }
    return $count;
  }

  /**
   * Load single popup from table with key.
   */
  public static function load($value) {
    $select = db_select('ai_contact_owner_tracking', 'tb');
    $select->fields('tb');
    $select->condition('tracking_id', $value, '=');
    
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  /**
   * Load all popup from table.
   */
  public static function loadAll($order_by='tracking_id', $where = []) {
    $select = db_select('ai_contact_owner_tracking', 'tb');
    $select->fields('tb');
    
    if (isset($where) && !empty($where)) {
      foreach ($where as $field => $condition) {
        if (in_array($field, ['date_from', 'date_to'])) {
          $field = 'timestamp';
        }
        $select->condition($field, $condition['value'], $condition['operator']);
      }
    }
    $select->orderBy($order_by, 'ASC');
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }
  
  public function getWebformSubmissionId() {
    $select = db_select('webform_submission', 'ws');
    $select->fields('ws', ['sid']);
    $select->condition('webform_id', 'contact', '=');
    $select->orderBy('sid', 'DESC');
    $select->range(0,1);
    
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }
  
  public static function getNodeTitleLinkByNodeId($nid) {
    $node_link = '';
    $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid);
    
    $select = db_select('node_field_data', 'n');
    $select->fields('n', ['title']);
    $select->condition('nid', $nid, '=');
    
    // Return the result in object format.
    $result = $select->execute()->fetchCol(0);
    
    if (isset($result[0]) && !empty($result[0])) {
      $url = Url::fromUserInput($path_alias);
      $title = strlen($result[0]) > 80 ? substr($result[0], 0, 80) . '...' : $result[0];
      $node_link = Link::fromTextAndUrl($title, $url);
      $node_link = $node_link->toRenderable();
      // If you need some attributes.
      $node_link['#attributes'] = ['target' => '_blank'];
    }
    
    return $node_link;
  }
  
  public static function getNodeTitleByNodeId($nid) {
    $title = '';
    $select = db_select('node_field_data', 'n');
    $select->fields('n', ['title']);
    $select->condition('nid', $nid, '=');
  
    // Return the result in object format.
    $result = $select->execute()->fetchCol(0);
  
    if (isset($result[0]) && !empty($result[0])) {
      $title = $result[0];
    }
  
    return $title;
  }
  
  public static function getUserNameLinkByUserId($uid) {
    $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath('/user/' . $uid);
    $user_account = User::load($uid);

    $display_name = $user_account->name->value;
    $url = Url::fromUserInput($path_alias);
    $user_link = Link::fromTextAndUrl($display_name, $url);
    $user_link = $user_link->toRenderable();
    // If you need some attributes.
    $user_link['#attributes'] = ['target' => '_blank'];
    
    return $user_link;
  }
  
  /**
   * Get the base url of site.
   */
  public static function getSiteBaseUrl() {
    return \Drupal::request()->getSchemeAndHttpHost();
  }
}
