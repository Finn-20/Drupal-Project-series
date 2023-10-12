<?php

namespace Drupal\ai_archive;

class AiArchiveStorage {

  /**
   * Save an entry in the ai_archive_log table.
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
      $return_value = db_insert('ai_archive_log')
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
}
