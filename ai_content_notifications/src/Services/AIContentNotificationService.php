<?php

namespace Drupal\ai_content_notifications\Services;

/**
 * Class AiBriefcaseService
 *
 * @package Drupal\ai_briefcase\Services
 *
 * This service is to provide helper methods for BI tagging Framework module functionality.
 */
class AIContentNotificationService {

  /**
   * Get the base url of site.
   */
  public static function getSiteBaseUrl() {
    return \Drupal::requestStack()->getCurrentRequest()->getScheme() . '://' . \Drupal::requestStack()->getCurrentRequest()->getHost();
  }

  /**
   * Save an entry in the ai_checklist table.
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
      $return_value = db_insert('ai_content_notifications_logs')
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
  public static function update(array $entry) {
    try {
      // db_update()...->execute() returns the number of rows updated.
      $count = db_update('ai_content_notifications_logs')
        ->fields($entry)
        ->condition('nid', $entry['nid'])
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
   * Retrieve the nodes which are in draft mode.
   *
   */
  public static function getDraftListOfNodes() {
    $config = \Drupal::config('ai_content_notifications.settings');
    $notification_timeline = $config->get('draft_mode_notify_interval');
    $notification_timeline_duration = $config->get('draft_mode_notify_interval_duration');
    if (!isset($notification_timeline) || empty($notification_timeline)) {
      $notification_timeline = '2';
    }

    $changed_before = strtotime('-' . $notification_timeline . ' ' . $notification_timeline_duration);

    $select = db_select('node_field_data', 'n');
    $select->leftJoin('users_field_data', 'u', 'u.uid = n.uid');
    $select->leftJoin('ai_content_notifications_logs', 'acnl', 'n.nid=acnl.nid');
    $select->fields('n', ['nid', 'title', 'uid', 'changed'])
      ->fields('u', ['mail'])
      ->condition('n.type', 'use_case_or_accelerator', '=')
      ->condition('n.changed', $changed_before, '<=')
      ->isNull('acnl.nid')
      ->condition('n.moderation_state', 'draft', '=');
    // Return the result in object format.

    return $select->execute()->fetchAll();
  }

  /**
   * Processing the notification data.
   *
   * @param array $node_details
   *   Node's that needs to be processed.
   * @param string $operation_type
   *   Processing type.
   */
  public static function processNotificationData($node_details, $operation_type) {
  $config = \Drupal::config('ai_content_notifications.settings');

  $nodes = [];
  foreach ($node_details as $node_detail) {

    $nodes[$node_detail->nid] = [
      'nid' => $node_detail->nid,
      'uid' => $node_detail->uid,
      'title' => $node_detail->title,
      'path' => SELF::getSiteBaseUrl() . \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node_detail->nid),
      'author_id' => $node_detail->uid,
      'author_email' => $node_detail->mail,
      'last_updated' => format_date($node_detail->changed, 'custom', 'F d, Y h:i A')
    ];

    $owner_details = SELF::aiContributeUseCaseNotifyNonAuthorList($node_detail->nid);
    if (!empty($owner_details)) {
      $nodes[$node_detail->nid] = array_merge($nodes[$node_detail->nid], ['owner_details' => $owner_details]);
    }
  }

  $notified_list = [];

  $max_limit = $config->get('max_notified_usecases');
  $count = 0;

  foreach ($nodes as $node) {
    //if (!in_array($node['nid'], $notifiedNodes)) {
    $notification = [
      'title' => $node['title'],
      'entity_id' => $node['nid'],
      'uid' => $node['uid'],
      'entity_type' => 'use_case_or_accelerator',
      'status' => TRUE,
      'operation' => $operation_type,
    ];
    \Drupal::entityTypeManager()->getStorage('user_notification')->create($notification)->save();
    //Adding the notification for the mapped primary and secondary owner of the asset.
    if (!empty($node['owner_details'])) {
      foreach ($node['owner_details'] as $owner_detail) {
        $owner_notification = [
          'title' => $node['title'],
          'entity_id' => $node['nid'],
          'uid' => $owner_detail,
          'entity_type' => 'use_case_or_accelerator',
          'status' => TRUE,
          'operation' => $operation_type . '-non-aa',
        ];
        \Drupal::entityTypeManager()->getStorage('user_notification')->create($owner_notification)->save();
      }
    }
    $entry = ['nid' => $node['nid'], 'timestamp' => REQUEST_TIME];
    // Check if it is a new entry or already exists
    if (SELF::checkIfUsecaseNotifiedBefore($node['nid'])) {
      // If exists, update the timestamp.
      SELF::update($entry);
    }
    else {
      // Otherwise, Insert the new entry.
      SELF::insert($entry);
    }
  }
}

  /**
   * Processing unattended comment for the assets.
   *
   * @return type
   */
  public static function getUnAttendedCommentListOfNodes() {

    $nodes =[];

    $config = \Drupal::config('ai_content_notifications.settings');
    $notification_timeline = $config->get('draft_mode_notify_interval');
    $notification_timeline_duration = $config->get('draft_mode_notify_interval_duration');
    if (!isset($notification_timeline) || empty($notification_timeline)) {
      $notification_timeline = '1';
    }
    $changed_before = strtotime('-' . $notification_timeline . ' ' . $notification_timeline_duration);
    $select = db_select('node_field_data', 'n');
    $select->leftJoin('users_field_data', 'u', 'u.uid = n.uid');
    $select->leftJoin('ai_content_notifications_logs', 'acnl', 'n.nid=acnl.nid');
    $select->fields('n', ['nid', 'title', 'uid', 'changed'])
      ->fields('u', ['mail'])
      ->condition('n.type', 'use_case_or_accelerator', '=')
      ->condition('n.changed', $changed_before, '<=')
      ->isNull('acnl.nid')
      ->condition('n.moderation_state', 'needs_review', '=');

    $node_details = $select->execute()->fetchAll();
    foreach ($node_details as $node_detail) {

      $comment_query = db_select('ai_checklist_answers', 'aca');
      $comment_query->fields('aca', ['answer_id', 'ref_nid', 'timestamp', 'uid'])
        ->condition('aca.ref_nid', $node_detail->nid, '=')
        ->condition('aca.timestamp', $changed_before, '<=')
        ->orderBy('aca.answer_id', 'desc')
        ->range(0, 1);
      $comment_results = $comment_query->execute()->fetchAll();

      foreach ($comment_results as $comment_result) {
        if ($node_detail->uid != $comment_result->uid) {
          $nodes[] = $node_detail;
        }
      }
    }

    // Return the result in object format.
    return $nodes;
  }

  public static function getListOfNodes() {
    $config = \Drupal::config('ai_content_notifications.settings');
    $notification_timeline = $config->get('last_changed_notify_interval');
    if (!isset($notification_timeline) || empty($notification_timeline)) {
      $notification_timeline = '6';
    }
    $changed_before = strtotime('-' . $notification_timeline . ' months');
    $select = db_select('node_field_data', 'n');
    $select->fields('n', ['nid', 'title', 'uid', 'changed'])
      ->fields('u', ['mail'])
      ->condition('n.type', 'use_case_or_accelerator', '=')
      ->condition('n.changed', $changed_before, '<=')
      ->condition('n.moderation_state', 'archived', '<>');

    $select->join('users_field_data', 'u', 'u.uid = n.uid');

    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  public static function getContributorsList($node_ids) {
    $contributorList = [];
    $select = db_select('node__field_other_contributors', 'n');
    $select->fields('n', ['entity_id'])
      ->fields('u', ['mail'])
      ->condition('n.entity_id', $node_ids, 'IN');
    $select->join('users_field_data', 'u', 'u.uid = n.field_other_contributors_target_id');
    // Return the result in object format.
    $results = $select->execute()->fetchAll();
    foreach ($results as $result) {
      $contributorList[$result->entity_id][] = $result->mail;
    }
    return $contributorList;
  }

  public static function getNotifiedUsecasesByTime() {
    $config = \Drupal::config('ai_content_notifications.settings');
    $frequency = $config->get('last_reminder_notify_interval');
    if (!isset($frequency) || empty($frequency)) {
      $frequency = '30';
    }
    $notified_within = strtotime('-' . $frequency . ' days');
    $notified_nodes = [];
    $select = db_select('ai_content_notifications_logs', 'l')
      ->fields('l', ['nid'])
      ->condition('l.timestamp', $notified_within, '>=');

    // Return the result in object format.
    $results = $select->execute()->fetchAll();
    foreach ($results as $result) {
      $notified_nodes[$result->nid] = $result->nid;
    }
    return $notified_nodes;
  }

  public static function checkIfUsecaseNotifiedBefore($nid) {
    $result = db_select('ai_content_notifications_logs', 'l')
        ->fields('l', ['nid'])
        ->condition('l.nid', $nid, '=')
        ->execute()->fetchField();

    return $result;
  }

  public static function notifyAuthorAndContributors($key, $params = []) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ai_content_notifications';
    $langcode = 'en';
    $send = true;
    $config = \Drupal::config('ai_content_notifications.settings');
    $from = (null != $config->get('email_from')) ? $config->get('email_from') : \Drupal::config('system.site')->get('mail');

    $reciever = [];
    $contributors = [];

    if (isset($params['node']['author_email']) && !empty($params['node']['author_email'])) {
      $reciever[] = $params['node']['author_email'];
    }

    if (isset($params['node']['contributors']) && !empty($params['node']['contributors'])) {
      foreach ($params['node']['contributors'] as $contributor) {
        if (!in_array($contributor, $reciever)) {
          $reciever[] = $contributor;
        }
      }
    }

    if (is_array($reciever)) {
      $to = implode(',', $reciever);
    }

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
      \Drupal::logger('mail-log')->error($message);
      return;
    }

    $message = t('Notify author and contributor : An email notification has been sent to @email ', array('@email' => $to));
    \Drupal::logger('mail-log')->notice($message);
  }

  public static function notifyGalleryTeamWithAttachment($key, $params = []) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ai_content_notifications';
    $langcode = 'en';
    $send = true;
    $config = \Drupal::config('ai_content_notifications.settings');
    $from = (null != $config->get('email_from')) ? $config->get('email_from') : \Drupal::config('system.site')->get('mail');
    $to = 'aigallerycentralteam.fr@capgemini.com';

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
      \Drupal::logger('mail-log')->error($message);
      return;
    }

    $message = t('Notify Gallery team : An email notification has been sent to @email ', array('@email' => $to));
    \Drupal::logger('mail-log')->notice($message);
  }

  /*
   * @param type $nid
   *  Node ID mapped to the primary and secondary owner of the asset.
   */
  public static function aiContributeUseCaseNotifyNonAuthorList($nid) {
    $config = \Drupal::config('ai_content_notifications.settings');
    $check_to_notify_non_asset_author_user_list = $config->get('check_to_notify_non_asset_author_user_list');
    if ($check_to_notify_non_asset_author_user_list) {
      $owner_details = [];

      $primary_owner_query = \Drupal::database()->select('node__field_use_case_primary_owner_ema', 'poe')
        ->fields('poe', ['field_use_case_primary_owner_ema_value'])
        ->condition('poe.entity_id', $nid, '=');
      $po_results = $primary_owner_query->execute()->fetchAssoc();

      if (!empty($po_results)) {
        $primary_owner_email = $po_results['field_use_case_primary_owner_ema_value'];

        $primary_user = \Drupal::entityTypeManager()->getStorage('user')
          ->loadByProperties(['mail' => $primary_owner_email]);
        $primary_user = reset($primary_user);
        if ($primary_user) {
          $owner_details[] = $primary_user->id();
        }
      }

      $secondary_owner_query = \Drupal::database()->select('node__field_usecase_secn_owner_email', 'soe')
        ->fields('soe', ['field_usecase_secn_owner_email_value'])
        ->condition('soe.entity_id', $nid, '=');
      $so_results = $secondary_owner_query->execute()->fetchAssoc();

      if (!empty($so_results)) {
        $secondary_owner_email = $so_results['field_usecase_secn_owner_email_value'];
        $secondary_user = \Drupal::entityTypeManager()->getStorage('user')
          ->loadByProperties(['mail' => $secondary_owner_email]);
        $secondary_user = reset($secondary_user);
        if ($secondary_user) {
          $owner_details[] = $secondary_user->id();
        }
      }

      // Getting non author user list to whom the notification needs to be sent.
      $non_asset_author_user_list = $config->get('notification_non_asset_author_user_list');
      if (!empty($non_asset_author_user_list)) {
        $user_mail_lists = explode(',', $non_asset_author_user_list);
        foreach ($user_mail_lists as $user_mail_list) {
          $author_user_list = \Drupal::entityTypeManager()->getStorage('user')
            ->loadByProperties(['mail' => $user_mail_list]);
          $author_user_list = reset($author_user_list);
          if ($author_user_list) {
            $owner_details[] = $author_user_list->id();
          }
        }
      }

      return $owner_details;
    }
  }

  /**
   * Get the configured notification title to be display on the basis of operation type. 
   *
   * @param string $assetUserID
   *   Asset owner
   * @param string $notificationOperationMessage
   *   Notification asset message to be displayed on the site.
   * @return string
   *   Notification title to be displayed on the notification block for the asset.
   */
  public static function getAssetNotificationTitle($assetUserID, $notificationOperationMessage) {
    $config = \Drupal::config('ai_content_notifications.settings');
    $notification_title = NULL;
    if ($assetUserID == \Drupal::currentUser()->id()) {
      $notification_title = $notificationOperationMessage;
    }
    else {
      $notification_append_text = $config->get('append_message_text_non_asset_user');
      $notification_title = !empty($notification_append_text) ? $notification_append_text . ' '
        . $notificationOperationMessage : $notificationOperationMessage;
    }

    return $notification_title;
  }

  public static function getNotificationOperationTypes() {
    return array('Rating','Comment','Email','Subscriber','draft-asset-opt','pending-review-asset-opt','draft-asset-opt-non-aa','pending-review-asset-opt-non-aa');
  }

}
