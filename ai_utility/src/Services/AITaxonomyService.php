<?php

namespace Drupal\ai_utility\Services;

use Drupal\taxonomy\Entity\Term;


class AITaxonomyService {

  public static function getTermIdsOfNames($termName,$vid){
    $results = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $termName,'vid' => $vid]);
    $termIds = [];
    foreach ($results as $result) {
      $termIds[] = $result->id();
    }
    return $termIds;
  }

  public static function updateIdsOfField($table, $revision_table, $field_name, $sourceIds, $destination_id){
    
    $database = \Drupal::database();
    try {
      $transaction = $database->startTransaction();

      $update_table = \Drupal::database()->update($table);
      $update_table->fields(array($field_name => $destination_id));
      $update_table->condition($field_name, $sourceIds, 'IN');
      $update_table->execute();

      $update_revision_table = \Drupal::database()->update($revision_table);
      $update_revision_table->fields(array($field_name => $destination_id));
      $update_revision_table->condition($field_name, $sourceIds, 'IN');
      $update_revision_table->execute();

    }catch (Exception $ex) {
      $transaction = $database->rollBack();
      \Drupal::logger('data update : data update issue')->log('debug', print_r($ex, true));
      drupal_set_message(t('System encountered some error while saving the data.'));
    }
  }

  public static function deteleTerms($tids){
    $database = \Drupal::database();
    try {
      $transaction = $database->startTransaction();

      $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
      $entities = $controller->loadMultiple($tids);
      $controller->delete($entities);
      print('Taxononmy deleted successfully.');

    }catch (Exception $ex) {
      $transaction = $database->rollBack();
      \Drupal::logger('Term delete : Term delete issue')->log('debug', print_r($ex, true));
      drupal_set_message(t('System encountered some error while deleting the data.'));
    }
  }
}
