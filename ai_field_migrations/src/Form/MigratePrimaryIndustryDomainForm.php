<?php

namespace Drupal\ai_field_migrations\Form;


use Drupal\Core\Form\FormStateInterface;
use Drupal\ai_field_migrations\Services\AiFieldMigrationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;
use Drupal\Core\Language\Language;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Form\FormBase;

class MigratePrimaryIndustryDomainForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_migrate_primary_industry_domain_form';
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['term_mapping_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Import CSV'),
      '#upload_location' => 'public://csv_file/',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv txt'],
      ],
      '#default_value' => 0,
      '#required' => TRUE,
    ];
        
    $form['actions']['submit'] = [
      '#type'    => 'submit',
      '#value' => t('Star Migration'),
      '#button_type' => 'primary',
    ];
    
    return $form;
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $term_mapping_file = $form_state->getValue(['term_mapping_file', '0']);
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $aiFieldMigrationService = \Drupal::service('ai_field_migrations.aiFieldMigrationService');
    $node_term_arr = [];
    $primary_industries = [];
    $primary_domains = [];
    $fid = $form_state->getValue(['term_mapping_file', '0']);
    if ($fid) {
      $term_mapping_file = File::load($fid);
      $destination = $term_mapping_file->getFileUri();
      
      $file = file_get_contents($destination);
      $file_data = explode("\r", $file);
      
      $node_ids = [];
      foreach (explode("\r", $file) as $data) {
        $line_arr = explode("\t", $data);
        if (isset($line_arr[0]) && !empty($line_arr[0]) && is_numeric($line_arr[0])) {
          $node_ids[trim($line_arr[0])] = trim($line_arr[0]);
          $node_term_arr[trim($line_arr[0])] = [
            'primary_industry' => trim($line_arr[1]),
            'primary_domain' => trim($line_arr[2]),
          ];
        }
      }
      
      $nodeRevisions = $aiFieldMigrationService->getLatestRevisionIds($node_ids);
      foreach($nodeRevisions as $node_detail) {
        $primary_industries[] = [
          'bundle' => $node_detail['type'], 
          'deleted' => 0,
          'entity_id' => $node_detail['nid'],
          'revision_id' => $node_detail['vid'],
          'langcode' => 'en',
          'delta' => 0,
          'field_primary_industry_target_id' => $node_term_arr[$node_detail['nid']]['primary_industry'],
        ];
        
        $primary_domains[] = [
          'bundle' => $node_detail['type'],
          'deleted' => 0,
          'entity_id' => $node_detail['nid'],
          'revision_id' => $node_detail['vid'],
          'langcode' => 'en',
          'delta' => 0,
          'field_primary_domain_target_id' => $node_term_arr[$node_detail['nid']]['primary_domain'],
        ];
      }
      
      $count = count($node_term_arr);
      if (!$count) {
        return FALSE;
      }
      $batch = [
        'title' => t('Migrating Primary Industry and Domain'),
        'operations' => [
          [ '\Drupal\ai_field_migrations\Form\MigratePrimaryIndustryDomainForm::migrateTermsToNode', [ $primary_industries, $primary_domains ] ],
        ],
        'finished' => '\Drupal\ai_field_migrations\Form\MigratePrimaryIndustryDomainForm::migrateTermsToNodeFinished',
      ];
    
      $aiFieldMigrationService->addMessage($this->t('Migrating primary Industry and Domains to use cases'), 'status');
    
      if ($batch) {
        batch_set($batch);
      } else {
        $aiFieldMigrationService->addMessage($this->t('There are no node to process'), 'warning');
      }
    }
  }
  
  public static function migrateTermsToNode($primary_industries, $primary_domains, &$context) {
    $aiFieldMigrationService = \Drupal::service('ai_field_migrations.aiFieldMigrationService');
    $context['message'] = t('Processing to Queue...');
    $migration_result = [];
    
    $aiFieldMigrationService->addTaxonomyTermsToNodes($primary_industries, 'node__field_primary_industry');
    $aiFieldMigrationService->addTaxonomyTermsToNodes($primary_industries, 'node_revision__field_primary_industry');
    $migration_result[] = 'Updated Primary Industry';
    
    $aiFieldMigrationService->addTaxonomyTermsToNodes($primary_domains, 'node__field_primary_domain');
    $aiFieldMigrationService->addTaxonomyTermsToNodes($primary_domains, 'node_revision__field_primary_domain');
    $migration_result[] = 'Updated Primary Domains';
    
    $context['results'] = $migration_result;
  }
  public function migrateCollateralToAttachmentFinished($success, $results, $operations) {
    $aiFieldMigrationService = \Drupal::service('ai_field_migrations.aiFieldMigrationService');
    if ($success) {
      $aiFieldMigrationService->addMessage(t('@count nodes processed to queue.', [ '@count' => count($results) ]));
    }
    else {
      $error_operation = reset($operations);
      $aiFieldMigrationService->addMessage(t('An error occurred while processing @operation with arguments : @args', [ '@operation' => $error_operation[0], '@args' => print_r($error_operation[0], TRUE) ]), 'error');
    }
  }
}