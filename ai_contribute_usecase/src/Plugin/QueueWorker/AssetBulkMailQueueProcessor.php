<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\ai_contribute_usecase\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\user\Entity\User;

/**
 * Asset to be process as the part of bulk mail feature.
 *
 * @QueueWorker(
 *   id = "process_bulk_email_user_data",
 *   title = @Translation("Notifying the user, with the asset that are recently created/updated.")
 * )
 */
class AssetBulkMailQueueProcessor extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($items) {
    if (!empty($items)) {
      $bulk_email_settings = \Drupal::config('ai_contribute_usecase.bulk_mail.settings');
      $query_last_processed_date = $bulk_email_settings->get('bulk_mail_user_last_processed_date');

      if (!$this->doBulkEmailRecordExists($items['uid'], $query_last_processed_date)) {
        $message = t('From Bulk mail: An email notification has already been sent to @email and process date : @process_date', array(['@email' => $items['mail'], '@process_date' => $query_last_processed_date]));
        \Drupal::logger('bulk-email-process')->notice($message);
        return;
      }
      $mailManager = \Drupal::service('plugin.manager.mail');
      $module = 'ai_contribute_usecase';
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
      $send = TRUE;

      $key = 'ai_contribute_usecase_asset_bulk_mail';
      $from = \Drupal::config('system.site')->get('mail');

      $to = $items['mail'];
      $message['user_name'] = $items['name'];
      $message['asset_template_replacement'] = FALSE;
      $message['get_bulk_email_content'] = TRUE;
      $message['user_name'] = !(empty($items['fname']) || empty($items['lname'])) ? trim($items['fname'] . " " . $items['lname']) :
        $items['username'];
      $result = $mailManager->mail($module, $key, $to, $langcode, $message, $from);
      $process_details = [
        'uid' => $items['uid'],
        'email' => $to,
        'mail_stats' => $key . " -- To : " . $to . " -- From : " . $from,
        'sent_result' => 1,
        'process_date' => $query_last_processed_date,
      ];
      // Record the details that are process as the part of above mail sent.
      $this->recordBulkEmailDetails($process_details);

      if ($result['result'] != TRUE) {
        $message = t('From Bulk mail :There was a problem sending your email notification to @email. and @cc and From: @from: Message : @message', ['@message' => print_r($message['body'], true), '@email' => $to, '@cc' => $params['cc'], '@from' => $from]);
        \Drupal::logger('bulk-email-process')->error($message);
        return;
      }
      $message = t('From Bulk mail: An email notification has been sent to @email and @cc and From: @from : Message : @message', ['@message' => print_r($message['body'], true), '@email' => $to, '@cc' => $params['cc'], '@from' => $from]);
      \Drupal::logger('bulk-email-process')->notice($message);
    }
  }

  /**
   * Adding the data to the table to record the mail details.
   *
   * @param array $process_details
   *   Array containing the mail detail.
   */
  protected function recordBulkEmailDetails($process_details) {

    if (!empty($process_details)) {
      echo "<pre>";
      print_R($process_details);
      $process_mail_data = \Drupal::database()->insert('asset_bulk_email_details')->fields(
          [
            'uid' => $process_details['uid'],
            'email' => $process_details['email'],
            'mail_stats' => $process_details['mail_stats'],
            'sent_result' => $process_details['sent_result'],
            'process_date' => $process_details['process_date'],
          ]
        )->execute();
    }
  }

  /**
   * Check if the record exists for the user for same process date.
   * 
   * @param int $user_id
   *   User id of the user to whom the mail is been sent.
   * @param int $process_date
   *   Time on which the mail is been sent.
   *
   * @return boolean
   *   Check whether the record exits or not.
   */  
  protected function doBulkEmailRecordExists($user_id, $process_date) {
    $process_data = \Drupal::database()->select('asset_bulk_email_details', 'abed')
      ->fields('abed', ['uid'])
      ->condition('abed.uid', $user_id)
      ->condition('abed.process_date', $process_date, '=');
    $asset_results = $process_data->execute()->fetchAll();

    if (count($asset_results) == 0) {
      return TRUE;
    }
    return FALSE;
  }

}
