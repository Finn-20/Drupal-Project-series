<?php

namespace Drupal\user_notification;

use Drupal\ai_content_notifications\Services\AIContentNotificationService;
/**
 * Class SimplePopupBlocksStorage.
 */
class UserNotificationStorage {

  /**
   * Save an entry in the user_notification table.
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
  public static function bulkInsert(array $entry) {
    $return_value = NULL;
    try {
      $query_insert = db_insert('user_notification')
        ->fields(['title', 'entity_id','entity_type', 'status','operation','uid','uuid','created']);
      foreach ($entry as $notification) {
        $query_insert->values($notification);
      }
      $return_value = $query_insert->execute(); 
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
  public static function update(array $entry, $condition_value) {
    $count = 0;
    try {
      // db_update()...->execute() returns the number of rows updated.
      $count = db_update('user_notification')
        ->fields($entry)
        ->condition('id', $condition_value,'IN')
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
  * check user exist
  */
  public static function _is_user_notification_exist($uid,$entity_type,$entity_id,$operation) {
    $database = \Drupal::database();
    $query = $database->select('user_notification','u')->fields('u', ['id'])
    ->condition('entity_type', $entity_type)->condition('entity_id', $entity_id)->condition('uid', $uid, '=')->condition('operation', $operation, '=');
    $notification_id = $query->execute()->fetchObject();
    return $notification_id;
  }
  /**
  * Get node author,primary and secondary owner
  */
  public static function node_author_pr_se_owner($node,$operation,&$update_user,&$insert_user) {
    $entity_type = $node->bundle();
    $entity_id = $node->id();
    // 1 Get the author of the asset
    $owner_id = $node->getOwnerId();
    $owner_user_exist = UserNotificationStorage::_is_user_notification_exist($owner_id,$entity_type,$entity_id,$operation);
    if($owner_user_exist){
      $update_user[$owner_user_exist->id] = $owner_user_exist->id;
    }else{
      $insert_user[$owner_id] = $owner_id;
    }
    // 2. Primary owner of the asset
    if (isset($node->field_use_case_primary_owner_ema->value) && !empty($node->field_use_case_primary_owner_ema->value)
      && valid_email_address($node->field_use_case_primary_owner_ema->value)) {
      $primary_owner_email = $node->field_use_case_primary_owner_ema->value;
      $primary_user = user_load_by_mail($primary_owner_email);
      if(!empty($primary_user)){
        $primary_user_exist = UserNotificationStorage::_is_user_notification_exist($primary_user->id(),$entity_type,$entity_id,$operation);

        if($primary_user_exist){
          $update_user[$primary_user_exist->id] = $primary_user_exist->id;
        }else{
          $insert_user[$primary_user->id()] = $primary_user->id();
        }
      }
    }
      
  // 3. Secondary owner of the asset
    if (isset($node->field_usecase_secn_owner_email->value) && !empty($node->field_usecase_secn_owner_email->value)
    &&  valid_email_address($node->field_usecase_secn_owner_email->value)) {
      $secondary_owner_email = $node->field_usecase_secn_owner_email->value;
      $secondary_user = user_load_by_mail($secondary_owner_email);
      if(!empty($secondary_user)){
        $secondary_user_exist = UserNotificationStorage::_is_user_notification_exist($secondary_user->id(),$entity_type,$entity_id,$operation);
        if($secondary_user_exist){
          $update_user[$secondary_user_exist->id] = $secondary_user_exist->id;
        }else{
          $insert_user[$secondary_user->id()] = $secondary_user->id();
        }
      }
    }
  }

  /**
  * Notifiy the super user
  */
  public static function notification_default_user(&$update_user,&$insert_user,$operation,$entity_type,$entity_id) {
    $notification_level_config = \Drupal::configFactory()->getEditable('ai_content_notifications.settings');
    $super_mails = $notification_level_config->get('notification_non_asset_author_user_list');
    //echo "<pre>super_mails"; print_r($super_mails); 
    //\Drupal::logger("super_mails")->notice(print_r($super_mails,true));
    if(isset($super_mails)){
      if( strpos($super_mails, ",") !== false ) {
        $super_mails_array = explode(",", $super_mails);
      // echo "super_mails_array"; print_r($super_mails_array); die();
        //\Drupal::logger("super_mails_array")->notice(print_r($super_mails_array,true));
        foreach ($super_mails_array as $key => $value) {
          $sup_user = user_load_by_mail($value);
          if(!empty($sup_user)){
            $super_user_exist = UserNotificationStorage::_is_user_notification_exist($sup_user->id(),$entity_type,$entity_id,$operation);
            if($super_user_exist){
              $update_user[$super_user_exist->id] = $super_user_exist->id;
            }else{
              $insert_user[$sup_user->id()] = $sup_user->id();
            }
          }
        }
      }else{
        $sup_user = user_load_by_mail($super_mails);
        if(!empty($sup_user)){
          $super_user_exist = UserNotificationStorage::_is_user_notification_exist($sup_user->id(),$entity_type,$entity_id,$operation);
          if($super_user_exist){
            $update_user[$super_user_exist->id] = $super_user_exist->id;
          }else{
            $insert_user[$sup_user->id()] = $sup_user->id();
          }
        }
      }
    }
  }
}
