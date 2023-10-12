<?php

namespace Drupal\ai_collaborate\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TribesCategorySelectionForm extends TribesAssetsMappingFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'tribes_category_selection';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $collaborate_tribes_vid = 'collaborate_category';
    $tribes_categories = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($collaborate_tribes_vid);
    foreach ($tribes_categories as $tribes_category) {
      $tribes_options[$tribes_category->tid . '|' . $tribes_category->name] = $tribes_category->name;
    }

    $form['tribes_category'] = [
      '#type' => 'select',
      '#multiple' => TRUE,
      '#title' => $this->t('Tribes Category'),
      '#title_display' => 'invisible',
      '#required' => TRUE,
      '#options' => $tribes_options,
    ];
    $form['download'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download File'),
      '#description' => $this->t("It will download a file"),
      '#submit' => array('::downloadCSV'),
    ];
    $form['actions']['submit']['#value'] = $this->t('Next');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function downloadCSV(array &$form, FormStateInterface $form_state) {
    $selected_values = $form_state->getValue('tribes_category');
    $selected_values_arr = array_keys($selected_values);
    $selected_value_explo = array();

    foreach ($selected_values_arr as $array_values) {
      $selected_value_explo = explode("|", $array_values);
      $collected_keys[] = $selected_value_explo[0];
      $collected_values[$selected_value_explo[0]] = $selected_value_explo[1];
    }

    $query = \Drupal::database()->select('node__field_tribes_related_assets', 'nftra');
    $query->join('taxonomy_term_field_data', 'fd', 'nftra.field_tribes_related_assets_target_id = fd.tid');
    $query->join('node_field_data', 'nfd', 'nftra.entity_id = nfd.nid');
    $query->fields('nftra', ['field_tribes_related_assets_target_id']);
    $query->fields('nfd', ['title']);
    $query->fields('fd', ['name']);
    $query->condition('field_tribes_related_assets_target_id', $collected_keys, 'IN');
    $results = $query->execute()->fetchAll();
    $csv_data_arr = [];
    foreach ($results as $key => $array_terms) {
      $tid = $array_terms->field_tribes_related_assets_target_id;
      $name = $array_terms->name;
      $csv_data_arr[$tid][] = $array_terms->title;
      $array_count[] = count($csv_data_arr[$tid]);
    }

    foreach ($csv_data_arr as $arr_key => $value) {
      $array_count[] = count($csv_data_arr[$arr_key]);
    }

    if ($form_state->getValue('download') == true) {
      // Set up the tribes-asset-data downloadable directory.
      $file_path = 'public://tribes-asset-data/';
      if (!\Drupal::service('file_system')->prepareDirectory($file_path, FileSystemInterface::CREATE_DIRECTORY)) {
        drupal_set_message('Could not create directory ' . $file_path . '.');
      }
      $filename = $file_path . 'tribes-asset-matrix.csv';
      $handle = fopen($filename, 'w+');
      $cnt = 0;
      $data = [];

      for ($cnt = 0; $cnt < max($array_count); $cnt++) {
        $array_data = [];
        foreach ($csv_data_arr as $arr_key => $value) {
          if ($cnt == 0) {
            $array_header [] = $collected_values[$arr_key];
          }
          $array_data[] = !empty($csv_data_arr[$arr_key][$cnt]) ? $csv_data_arr[$arr_key][$cnt] : NULL;
        }
        if ($cnt == 0) {
          fputcsv($handle, $array_header);
          unset($array_header);
        }
        fputcsv($handle, $array_data);
      }

      rewind($handle);
      fclose($handle);
    }

    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Description' => 'File Download',
      'Content-Disposition' => 'attachment; filename=' . basename($filename)
    ];
    $uri = $filename;
    $form_state->setResponse(new BinaryFileResponse($uri, 200, $headers, true));

}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('tribes_category', $form_state->getValue('tribes_category'));
    $form_state->setRedirect('ai_collaborate.tribes_asset_listing');
  }

}
