<?php

namespace Drupal\ai_archive\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\user\Entity\User;

/**
 * Processes Node Tasks.
 *
 * @QueueWorker(
 *   id = "mail_queue_processor",
 *   title = @Translation("Sending mail to users"),
 *   cron = {"time" = 10}
 * )
 */
class MailQueueProcessor extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($node) {
    if (!empty($node->mail_sending_case)) {
      switch ($node->mail_sending_case) {
        case 'archived': 
          _ai_archive_mail_sending($node,'ai_archive_state_submission');
          break;

        case 'un-archived': 
          _ai_archive_mail_sending($node,'ai_unarchive_state_submission'); 
          break;

        case 'published':
          $key = 'ai_checklist_content_published';
          if($node->key){
            $key = $node->key;
          }
          _ai_checklist_published_mail($node,$key);
          _ai_account_subscriber_notification($node);
          break;
        case 'republished':
          _ai_checklist_published_mail($node,'ai_checklist_content_republished');
          _ai_account_subscriber_notification($node);
          break;

        case 'pending_review':
          _ai_checklist_published_mail($node,$node->key);
          break;
        
        default:
          # code...
          break;
      }
    }
  }
}