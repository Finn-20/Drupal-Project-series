<?php

namespace Drupal\ai_contribute_usecase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Component\Utility\Html;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides route responses for the Briefcase module.
 */
class AssetMailNofiticationController extends ControllerBase {

  /**
   * Asset to be processed.
   */
  public function assetToProcess() {

    try {
      $data = [];

      // Getting the value to fetch the records on the basis of duration and
      // the records which are processed till now.
      $bulk_email_settings = \Drupal::config('ai_contribute_usecase.bulk_mail.settings');
      $process_duration = $bulk_email_settings->get('bulk_mail_process_duration');
      $process_duration = !empty($process_duration) ? $process_duration : '-1 month';

      $query_processed_limit = $bulk_email_settings->get('asset_bulk_mail_user_processed_limit', 500);

      $query_last_processed_date = $bulk_email_settings->get('bulk_mail_user_last_processed_date');
      if (!empty($query_last_processed_date)) {
        $next_process_time = strtotime('+1 month', $query_last_processed_date);

        // Checking that the process run at the defined duration of one month from
        // last process date.
        if (REQUEST_TIME < $next_process_time) {
          return new JsonResponse(['Not process, current time not in the process duration.']);
        }
      }

      $queue_factory = \Drupal::service('queue');

      /** @var QueueInterface $queue */
      $queue_asset = $queue_factory->get('process_bulk_email_asset_content');
      $queue_user = $queue_factory->get('process_bulk_email_user_data');

      // Maintaing the records of the last user that is process, so once the count
      // matches the total no. of users then we reset it to 1.
      $query_last_processed_user_id = $bulk_email_settings->get('bulk_mail_user_last_processed_user_id', 1);

      $is_prod_test = FALSE;
      $ai_utility_emai_settings = \Drupal::config('ai_utility.general_email_settings');
      $is_prod_test = $ai_utility_emai_settings->get('check_to_enable_testing');

      // Handling the user data to sent the bulk emails.
      $max_user_record = \Drupal::database()->select('users_field_data');
      $max_user_record->condition('status', 1);
      $max_user_record->addExpression('MAX(uid)');

      $max_user_count = $max_user_record->execute()->fetchField();
      // Resetting the value once all the users are processed and removing the
      // related to the content from the queue table.
      if (($max_user_count == $query_last_processed_user_id) || !empty($is_prod_test)) {
        $config = \Drupal::service('config.factory')->getEditable('ai_contribute_usecase.bulk_mail.settings')
          ->set('bulk_mail_user_current_processed_request', FALSE)
          ->set('bulk_mail_user_last_processed_date', REQUEST_TIME)
          ->set('bulk_mail_user_last_processed_user_id', 1)
          ->save();
      }

      $query_current_processed_request = $bulk_email_settings->get('bulk_mail_user_current_processed_request', FALSE);

      if ($query_last_processed_user_id <= 1) {

        \Drupal::database()->delete('queue')
          ->condition('name', 'process_bulk_email_asset_content', '=')
          ->execute();

        $time_diff = strtotime($process_duration);

         $select_assets = \Drupal::database()->select('node_field_data', 'nfd')
          ->fields('nfd', ['nid', 'vid'])
          ->condition('nfd.status', 1)
          ->condition('nfd.moderation_state', 'published')
          ->condition('nfd.changed', $time_diff, '>')
          ->condition('nfd.type', 'use_case_or_accelerator');

        $asset_results = $select_assets->execute()->fetchAll();
        if (count($asset_results) > 0) {

          foreach ($asset_results as $asset_result) {
            $node = Node::load($asset_result->nid);

            // get latest revision ID
            $latest_vid = NULL;
            $latest_vid = \Drupal::entityTypeManager()
              ->getStorage('node')
              ->getLatestRevisionId($node->id());

            if ($latest_vid == $asset_result->vid) {
              $node_title = $node->title->value;

              $url_options = [
                'absolute' => TRUE,
                'language' => \Drupal::languageManager()->getCurrentLanguage(),
              ];

              $node_url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], $url_options);
              $node_title = Link::fromTextAndUrl($node_title, $node_url)->toString();
              $show_more = Link::fromTextAndUrl('Show more', $node_url)->toString();

              $business_driver = !empty($node->body->value) ?
                t(substr(trim(str_replace('&nbsp;', '', strip_tags($node->body->value))), 0, 100) . "..." . $show_more) : NULL;

              $rows[] = [$node_title, $business_driver];
            }
          }

          $header = [t('Title'), t('Business Driver')];
          $rendered_asset_data = [
            '#theme' => 'ai_usecase_bulk_email_template',
            '#header' => $header,
            '#rows' => $rows,
          ];


          $rendered_asset_data = \Drupal::service('renderer')->renderPlain($rendered_asset_data);
          $token_service = \Drupal::token();
          $token_options = ['callback' => 'ai_contribute_usecase_mail_tokens', 'clear' => TRUE];

          $data['asset_template_replacement'] = TRUE;
          $data['rendered_asset_data'] = (string) $rendered_asset_data;

          $msg = $bulk_email_settings->get('asset_bulk_email_body');
          $rendered_asset_data = $token_service->replace($msg['value'], $data, $token_options);

          // Adding the asset data in the html format to the queue process.
          $queue_asset->createItem($rendered_asset_data);

          $query_current_processed_request = TRUE;
          $config = \Drupal::service('config.factory')->getEditable('ai_contribute_usecase.bulk_mail.settings')
            ->set('bulk_mail_user_current_processed_request', $query_current_processed_request)
            ->save();
        }
      }

      if ($query_current_processed_request) {
        if ($is_prod_test) {
          $testing_emails = $ai_utility_emai_settings->get('mail_testing_user_list');
          $test_emails = explode(",", $testing_emails);
          foreach ($test_emails as $test_email) {
            $user_data = user_load_by_mail($test_email);
            if (!empty($user_data)) {
              $users[] = $user_data->id();
            }
          }
        }
        else {
          $users = \Drupal::entityQuery('user')
            ->condition('status', 1)
            ->condition('uid', $query_last_processed_user_id, '>')
            ->range(0, $query_processed_limit)
            ->sort('uid' , 'ASC')
            ->execute();
        }
        if (count($users) > 0) {
          foreach ($users as $user) {
            $user = \Drupal\user\Entity\User::load($user);
            $user_data = [
              'uid' => $user->id(),
              'username' => $user->get('name')->value,
              'fname' => !empty($user->get('field_first_name')->value) ? $user->get('field_first_name')->value : NULL,
              'lname' => !empty($user->get('field_las')->value) ? $user->get('field_las')->value : NULL,
              'mail' => $user->get('mail')->value,
            ];
            $queue_user->createItem($user_data);
          }

          if (is_array($user_data) && empty($is_prod_test)) {

            $config = \Drupal::service('config.factory')->getEditable('ai_contribute_usecase.bulk_mail.settings');
            $config->set('bulk_mail_user_last_processed_user_id', $user_data['uid'])
              ->save();
          }
        }
        return new JsonResponse(['Data processed.']);
      }
      else {
        return new JsonResponse(['No Data processed.']);
      }
    }
    catch (RequestException $e) {
      \Drupal::logger('use_contribute_usecase')->error($e->getMessage());
    }
  }

}
