<?php

namespace Drupal\ai_checklist;

/**
 * Class SimplePopupBlocksStorage.
 */
class AiChecklistStorage {

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
  public static function insert(array $entry, $table_name = NULL) {
    $return_value = NULL;
    try {
      $return_value = db_insert($table_name)
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
  public static function update(array $entry, $table_name, $pid_key) {
    try {
      // db_update()...->execute() returns the number of rows updated.
      $count = db_update($table_name)
        ->fields($entry)
        ->condition($pid_key, $entry[$pid_key])
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
   * Load single popup from table with pid.
   */
  public static function load($pid_key, $pid_value, $table_name) {
    $select = db_select($table_name, 'ai');
    $select->fields('ai');
    $select->condition($pid_key, $pid_value, '=');

    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  /**
   * Load all popup from table.
   */
  public static function loadAll($table_name) {
    $select = db_select($table_name, 'tb');
    $select->fields('tb');
    $select->orderBy('weight', 'ASC');
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  /**
   * Delete popup from table.
   */
  public static function delete($pid_key, $pid_value, $table_name) {
    $select = db_delete($table_name);
    $select->condition($pid_key, $pid_value, '=');

    // Return the result in object format.
    return $select->execute();
  }

  /**
   *
   */
  public static function loadAllSubcategoryByCategory($category_id) {
    $select = db_select('ai_checklist_subcategory', 'sc');
    $select->fields('sc');
    $select->condition('category_id', $category_id, '=');

    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  /**
   *
   */
  public static function loadAllQuestionsWithCategories() {
    $query = db_select('ai_checklist_questions', 'q');
    $query->fields('q', ['question_id', 'checklist_question', 'sub_category_id'])
      ->fields('s', ['sub_category_name', 'category_id'])
      ->fields('c', ['category_name']);
    $query->innerJoin('ai_checklist_subcategory', 's', 's.sub_category_id = q.sub_category_id');
    $query->innerJoin('ai_checklist_category', 'c', 'c.category_id = s.category_id');

    $results = $query->execute()->fetchAll();
    $data = [];
    foreach ($results as $result) {
      if (isset($result->category_id) && !empty($result->category_id)) {
        if (!isset($data[$result->category_id]['category_name']) || empty($data[$result->category_id]['category_name'])) {
          $data[$result->category_id]['category_name'] = $result->category_name;
        }
        if (!isset($data[$result->category_id]['sub_category'][$result->sub_category_id]['sub_category_name']) || empty($data[$result->category_id]['sub_category'][$result->sub_category_id]['sub_category_name'])) {
          $data[$result->category_id]['sub_category'][$result->sub_category_id]['sub_category_id'] = $result->sub_category_id;
          $data[$result->category_id]['sub_category'][$result->sub_category_id]['sub_category_name'] = $result->sub_category_name;
        }

        $data[$result->category_id]['sub_category'][$result->sub_category_id]['questions'][$result->question_id] = $result->checklist_question;
      }
    }
    return $data;
  }

  /**
   *
   */
  public static function loadAllSubmittedComments($nid) {
    $select = db_select('ai_checklist_answers', 'ai');
    $select->fields('ai');
    $select->condition('ref_nid', $nid, '=');
    $select->condition('status', '1', '=');
    $select->orderBy('timestamp', 'DESC');

    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

  /**
   *
   */
  public static function loadAllSavedComments($nid) {
    $select = db_select('ai_checklist_answers', 'ai');
    $select->fields('ai');
    $select->condition('ref_nid', $nid, '=');
    $select->condition('status', '0', '=');

    // Return the result in object format.
    return $select->execute()->fetchAll();
  }

}
