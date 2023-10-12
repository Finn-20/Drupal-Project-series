<?php

namespace Drupal\ai_field_migrations\Form;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ai_field_migrations\Services\AiFieldMigrationService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;
use Drupal\Core\Language\Language;
use Drupal\node\Entity\Node;

class MigrateCollateralForm extends ConfigFormBase {
  /**
   * @var Drupal\ai_field_migrations\Services\AiFieldMigrationService
   */
  private $aiFieldMigrationService;
  
  public function __construct(ConfigFactoryInterface $config_factory, AiFieldMigrationService $aiFieldMigrationService) {
    parent::__construct($config_factory);
    $this->aiFieldMigrationService = $aiFieldMigrationService;
  }
  
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('config.factory'),
        $container->get('ai_field_migrations.aiFieldMigrationService')
    );
  }
  
  /**
   * {@inheritdoc}
   */
  protected  function  getEditableConfigNames() {
    return [
      'ai_migrate_collateral.config'
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_migrate_collateral_form';
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['submit'] = [
      '#type'    => 'submit',
      '#value' => t('Migrate Collaterals to Media Attachment'),
    ];
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $collateral_arr = [];
    $results = $this->aiFieldMigrationService->getCollateralsDetails();
    foreach ($results as $result) {
      if (strpos($result->type, 'video') === 0) {
        $type = 'video';
      }
      else if (strpos($result->type, 'image') === 0) {
        $type = 'image';
      }
      else {
        $type = 'file';
      }
      $collateral_arr[$result->nid][$type][] = $result->fid;
    }
    $count = count($collateral_arr);
    if (!$count) {
      return FALSE;
    }
    $batch = [
      'title' => t('Migrating Collaterals'),
      'operations' => [
        [ '\Drupal\ai_field_migrations\Form\MigrateCollateralForm::migrateCollateralToAttachment', [ $collateral_arr ] ],
      ],
      'finished' => '\Drupal\ai_field_migrations\Form\MigrateCollateralForm::migrateCollateralToAttachmentFinished',
    ];
  
    $this->aiFieldMigrationService->addMessage($this->t('Migrating Collaterals to Attachment Media'), 'status');
  
    if ($batch) {
      batch_set($batch);
    } else {
      $this->aiFieldMigrationService->addMessage($this->t('There are no collaterals to process'), 'warning');
    }
  }
  
  public static function migrateCollateralToAttachment($collaterals, &$context) {
    $aiFieldMigrationService = \Drupal::service('ai_field_migrations.aiFieldMigrationService');
    $context['message'] = t('Processing to Queue...');
    $migration_result = [];
    foreach ($collaterals as $nid => $collateral) {
      //$node = $this->aiFieldMigrationService->getNodeDetails($nid);
      $node = Node::load($nid);
      $media_attachment = [];
      foreach ($collateral as $key => $values) {
        foreach ($values as $fid) {
          if ($key == 'file') {
            $field_name = 'field_media_file';
            $media_details = ['target_id' => $fid];
          }
          else if ($key == 'image') {
            $field_name = 'field_media_image';
            $media_details = [
              'target_id' => $fid,
              'alt' => $node->title->value,
              'title' => $node->title->value,
            ];
          }
          else if ($key == 'video') {
            $field_name = 'field_media_video_file';
            $media_details = ['target_id' => $fid];
          }
          
          $media_entity = [
            'bundle' => $key,
            'uid' => $node->getOwnerId(),
            'langcode' => 'en',
            'status' => '1',
            $field_name => $media_details,
          ];
                    
          $media = Media::create($media_entity);
          
          $media->save();
          $media_attachment[] = ['target_id' => $media->id()];
        }
      }
      
      $node->set('field_attachments', $media_attachment);
      $node->save();
      $migration_result = $node->id() . ': ' . $node->getTitle();
    }
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