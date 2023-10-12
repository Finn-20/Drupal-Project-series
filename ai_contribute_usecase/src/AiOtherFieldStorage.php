<?php

namespace Drupal\ai_contribute_usecase;
use Drupal\taxonomy\Entity\Term;

/**
 * Class AiOtherFieldStorage.
 */
class AiOtherFieldStorage {

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
   public static function bulkInsert(array $entry) {
    $return_value = NULL;
    try {
      $query_insert = db_insert('ai_other_tag')
        ->fields(['uniqid', 'other_type','other_tag']);
      foreach ($entry as $other_tag) {
        $query_insert->values($other_tag);
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
  public static function updateByUniqId(array $entry, $uniqid) {
    try {
      $count = db_update("ai_other_tag")
        ->fields($entry)
        ->condition('uniqid', $uniqid, '=')
        ->execute();
    }
    catch (\Exception $e) {
      drupal_set_message(t('db_update failed. Message = %message', [
        '%message' => $e->getMessage(),
      ]), 'error');
    }
    return $count;
  }

  public static function loadByTag($uniqid) {
    $query = db_select('ai_other_tag', 't');
    $query->fields('t', ['other_type'])
      ->condition('t.uniqid', $uniqid, '=')
      ->condition('t.tag_status', 0, '=')
      ->groupBy("t.uniqid")->groupBy("t.other_type");
    return $query->execute()->fetchAll();
  }
  public static function load($uniqid, $other_type) {
    $query = db_select('ai_other_tag', 't');
    $query->fields('t', ['other_tag'])
      ->condition('t.uniqid', $uniqid, '=')
      ->condition('t.other_type', $other_type, '=')
      ->condition('t.tag_status', 0, '=');
    return $query->execute()->fetchAll();
  }

  public static function loadByUniqId($uniqid) {
    $query = db_select('ai_other_tag', 't');
    $query->fields('t', ['other_tag','other_type'])
      ->condition('t.uniqid', $uniqid, '=')
      ->condition('t.tag_status', 0, '=');
    return $query->execute()->fetchAll();
  }

  public static function mappingTags($uniqid) {
    $results = self::loadByUniqId($uniqid);
    $other_tags = [];
    if(!empty($results)){
      $partner_tids = $feature_tids = $framework_tids = [];
      for($i = 0; $i < count($results); $i++){
        switch ($results[$i]->other_type) {
          case 1:
            $term = Term::create([
              'vid' => 'tech_stacks',
              'name' => $results[$i]->other_tag,
              'field_term_status' => 1
            ]);
            $term->save();
            $partner_tids[] = $term->id();
            break;
          
          case 2:  
            $term = Term::create([
              'vid' => 'ai_features',
              'name' => $results[$i]->other_tag,
              'field_term_status' => 1
            ]);
            $term->save();
            $feature_tids[] = $term->id();
            break;

          case 3:
            $term = Term::create([
              'vid' => 'frameworks',
              'name' => $results[$i]->other_tag,
              'field_term_status' => 1
            ]);
            $term->save();
            $framework_tids[] = $term->id();
            break;
        }
      }
      // save to tids
      if(!empty($partner_tids)){
        $other_tags['partner_tids'] = $partner_tids;
      }
      if(!empty($feature_tids)){
        $other_tags['feature_tids'] = $feature_tids;
      }
      if(!empty($framework_tids)){
        $other_tags['framework_tids'] = $framework_tids;
      }
      return $other_tags;
    }
  }

  /**
   * Delete records from table.
   */
  public static function deleteTag($uniqid, $other_type) {
    $select = db_delete('ai_other_tag');
    if(is_array($other_type)){
      $select->condition('other_type', $other_type, 'IN');
    }else{
      $select->condition('other_type', $other_type, '=');
    }
    $select->condition('uniqid', $uniqid, '=');
    $select->condition('tag_status', 0, '=');

    return $select->execute();
  }

  /**
   * Delete records from table.
   */
  public static function deleteTagByUniquid($uniqid) {
    $select = db_delete('ai_other_tag');
    $select->condition('uniqid', $uniqid, '=');
    $select->condition('tag_status', 0, '=');

    return $select->execute();
  }
}
