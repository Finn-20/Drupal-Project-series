<?php

namespace Drupal\ai_utility\Services;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\file\Entity\File;

class AiUtilityService {

  public function fileUploadOperation($file, $file_name, $destination = NULL) {
    $filesystem = \Drupal::service('file_system');
    $file_usage = \Drupal::service('file.usage');
    $filesystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY);

    // Move the file to new folder.
    $file = file_move($file, $destination . $file_name, FileSystemInterface::EXISTS_REPLACE);

    // Handling file usages.
    $file_usage->add($file, 'ai_collaborate', 'file', $file->id());
    return $file->id();
  }

  public function checkIfCategoryExists($tid, $table_name, $field_name) {
    $query = \Drupal::database()->select($table_name, 'tb')
      ->fields('tb', [$field_name])
      ->condition($field_name, $tid, '=');
    $results = $query->countQuery()->execute()->fetchField();
    return $results;
  }

  /**
   *
   * @param string $email
   *   Email Id added to the asset for pri/ sec owners.
   *
   * @return boolean
   *   Return true if the domain is part of allowed domains list.
   */
  public function validateOwnerContributorEmailID($email) {

    $config = \Drupal::config('ai_utility_general.settings');
    $allowed_domains = $config->get('asset_owner_valid_domain');
    if (!empty($allowed_domains)) {
      $allowed_domains = strtolower($allowed_domains);
      $allowed_domains = explode(',', $allowed_domains); //['capgemini.com', 'altran.com', 'sogeti.com'];
      if (!empty($email)) {
        $email = strtolower($email);
        $mail = explode("@", $email);
        if (!in_array($mail[1], $allowed_domains)) {
          return TRUE;
        }
        else {
          return FALSE;
        }
      }
    }
    return FALSE;
  }

  /**
   * Test function
   */
  function _is_email_debug_mode_enabled(&$message) {
    $ai_utility_emai_settings = \Drupal::config('ai_utility.general_email_settings');
    $is_prod_test = $ai_utility_emai_settings->get('check_to_enable_testing');
    $message['email_debug_mode_enabled'] = TRUE;
    if ($is_prod_test) {
      $message['email_debug_mode_enabled'] = TRUE;
      $testing_users = $ai_utility_emai_settings->get('mail_testing_user_list');
      $bcctesting_users = $ai_utility_emai_settings->get('mail_testing_bcc_user_list');
      if (!empty($testing_users)) {
        $message['subject'] = $message['subject'] . ' to:' . $testing_users;
      }
      if (!empty($bcctesting_users)) {
        $message['subject'] = $message['subject'] . ' other: ' . $bcctesting_users;
      }
    }
    \Drupal::logger('Subject:-')->notice($message['subject']);
    \Drupal::logger('Message:-')->notice(print_r($message['body'], true));
  }

}
