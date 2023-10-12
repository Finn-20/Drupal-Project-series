<?php

namespace Drupal\ai_search_synonyms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\search_api_synonym\Entity\Synonym;

/**
 * Class SynonymImportForm.
 *
 * @package Drupal\search_api_synonym\Form
 *
 * @ingroup search_api_synonym
 */
class SynonymImportAPIForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_search_synonyms_import';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#markup' => '<p>Use this form to upload a CSV file of keywords</p>',
    ];

    $form['import_csv'] = [
      '#type' => 'managed_file',
      '#title' => t('Upload file here'),
      '#upload_location' => 'public://importcsv/',
      '#default_value' => '',
      '#required' => TRUE,
      "#upload_validators"  => ['file_validate_extensions' => ['csv']],
      '#states' => [
        'visible' => [
          ':input[name="File_type"]' => ['value' => t('Upload Your File')],
        ],
      ],
    ];

    $form['actions']['#type'] = 'actions';

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload CSV'),
      '#button_type' => 'primary',
    ];
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) { }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /* Fetch the array of the file stored temporarily in database */
    $csv_file = $form_state->getValue('import_csv');

    /* Load the object of the file by it's fid */
    $file = File::load($csv_file[0]);

    /* Set the status flag permanent of the file object */
    $file->setPermanent();

    /* Save the file in database */
    $file->save();

    // You can use any sort of function to process your data. The goal is to get each 'row' of data into an array
    // If you need to work on how data is extracted, process it here.
    $data = $this->csvtoarray($file->getFileUri(), ',');
    $operations[] = ['\Drupal\ai_search_synonyms\addImportContent::addImportSynonymItem', [$data]];
    

    $batch = [
      'title' => t('Importing Data...'),
      'operations' => $operations,
      'init_message' => t('Import is starting.'),
      'finished' => '\Drupal\ai_search_synonyms\addImportContent::addImportSynonymItemCallback',
    ];
    batch_set($batch);
  }
  
  public function csvtoarray($filename='', $delimiter){
    if(!file_exists($filename) || !is_readable($filename)) return FALSE;
    $data = [];
  
    $synonyms = Synonym::loadMultiple();
    $synonyms_list = [];
    foreach($synonyms as $synonym) {
      $syn_arr = explode(',', $synonym->get('word')->value . ',' . $synonym->get('synonyms')->value);
      foreach ($syn_arr as $syn) {
        if (!in_array($syn, $synonyms_list)) {
          $synonyms_list[] = strtolower($syn);
        } 
      }
    }
    
    if (($handle = fopen($filename, 'r')) !== FALSE ) {
      while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
        if (!in_array(strtolower($row[0]), $data) && !in_array(strtolower($row[0]), $synonyms_list)) {
          $data[] = strtolower($row[0]);
        }
      }
      fclose($handle);
    }
  
    return $data;
  }
}
