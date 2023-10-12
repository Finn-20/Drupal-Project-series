<?php

namespace Drupal\ai_associated_image_update\Form;

use Drupal\node\NodeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormBase;
use Drupal\file\Entity\File;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class AIAssociatedImageUpdateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_associated_image_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Associated Image Bulk Update Fields');
    $form['filters'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Filter'),
      '#open'  => FALSE,
    ];

    $nid_nid = \Drupal::entityQuery('node')
      ->condition('type', "use_case_or_accelerator", '=')
      ->condition('langcode', 'en', '=')
      ->condition('status', 1, '=')
      ->execute();
    $nodes_sele = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nid_nid);
    foreach ($nodes_sele as $value) {
      $title_sele[$value->nid->value] = $value->title->value;
    }
    $form['filters']['title'] = [
      '#type' => 'select',
      '#options' => $title_sele,
      '#default_value' => 0,
      '#description' => t('Select Titles From Use-case/Accelerator'),
      '#multiple' => TRUE,
      '#attributes' => [
        'style' => 'width:700px',
        'multiple' => TRUE,
      ],
    ];

    $form['filters']['actions']['filter_sub'] = [
      '#type'  => 'submit',
      '#value' => 'Filter',
      '#name'    => 'filterbutton',
    ];
    $form['filters']['actions']['filter_reset'] = [
      '#type' => 'submit',
      '#value' => 'Reset',
      '#weight' => 100,
      '#validate' => [],
    ];
    $header = ['NodeID', 'Title', 'Associated Image', 'Upload Image'];
    // Add the headers.
    $form['asso_bulkimage_upload'] = [
      '#type' => 'table',
      '#title' => 'Sample Table',
      '#header' => $header,
    ];

    // Get parameter value while submitting filter form
    // $selected_title = \Drupal::request()->request->get('title');
    $get_temp_data = \Drupal::service('user.private_tempstore')->get('ai_associated_image_update');
    $nids_data = $get_temp_data->get('tempvalue');

    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery();
    if (!empty($nids_data)) {
      $query->condition('nid', $nids_data, 'IN');
    }
    $query->condition('status', NodeInterface::PUBLISHED);
    $query->condition('type', 'use_case_or_accelerator');
    $query->sort('nid', 'DESC');

    $query->pager(10);
    $nids = $query->execute();

    foreach ($nids as $nid) {
      $node = Node::load($nid);
      $nodeid = $nid;

      $title = $node->title->value;
      $node_asso_img = $node->field_associated_image->target_id;
      // Add input fields in table cells.
      /* $form['asso_bulkimage_upload'][$nodeid]['selected_checkboxes'] = array(
      '#type' => 'checkboxes',
      '#options' => array($nodeid=>$nodeid),
      //'#value' =>  [$nodeid => $nodeid],

      ); */
      $form['asso_bulkimage_upload'][$nodeid]['selected_checkboxes'] = [
        '#type' => 'checkboxes',
        '#options' => [$nodeid => $nodeid],
      ];
      $form['asso_bulkimage_upload'][$nodeid]['assoc_title'] = [
        '#type' => 'markup',
        '#markup' => $title,
      ];
      if (!empty($node_asso_img)) {
        $assoc_img_file = File::load($node_asso_img);
        $style = \Drupal::entityTypeManager()->getStorage('image_style')->load('thumbnail');
        $url = $style->buildUrl($assoc_img_file->getFileUri());
        // $url = $assoc_img_file->getFileUri();
        $form['asso_bulkimage_upload'][$nodeid]['selected_node_Image'] = [
          '#type' => 'markup',
          '#markup' => '<img src="' . $url . '" alt="" title=""/>',
        ];
        $form['asso_bulkimage_upload'][$nodeid]['upload_assco_image'] = [
          '#type' => 'managed_file',
          '#title' => 'Associated Image',
          '#theme' => 'image_widget',
          '#preview_image_style' => 'thumbnail',
          '#upload_location' => 'public://files/styles/medium/public/images',
          '#upload_validators' => ['file_validate_extensions' => ['png gif jpg jpeg']],
        ];
      }
    }
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Apply Bulk Image Update',
    ];
    $form['pager'] = [
      '#type' => 'pager',
    ];
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    if (isset($values['filter_reset'])) {
      $request = \Drupal::request();
      $session = $request->getSession();
      // Remove later when you don't need the value anymore.
      $session->remove($session);
    }

    if (isset($values['title'])) {
      $sub_value = $form_state->cleanValues()->getValue('title');
      foreach ($sub_value as $c_value) {
        $nid_data[] = $c_value;
      }
      // Store data in session.
      $temp_data = $nid_data;
      $temp_nids = \Drupal::service('user.private_tempstore')->get('ai_associated_image_update');
      $temp_nids->set('tempvalue', $temp_data);
    }

    if (isset($values['asso_bulkimage_upload'])) {
      $seleted_array = [];
      foreach ($values['asso_bulkimage_upload'] as $key => $nodeid) {
        $node = Node::load($key);
        if ($nodeid['selected_checkboxes'][$key] != 0) {
          $seleted_array[$key]['nodeid'] = $nodeid['selected_checkboxes'][$key];
          $seleted_array[$key]['associate_new_img'] = $nodeid['upload_assco_image'][0];
          if (!empty($nodeid['upload_assco_image'][0])) {
            $assoc_img_file = File::load($nodeid['upload_assco_image'][0]);
            $style = \Drupal::entityTypeManager()->getStorage('image_style')->load('large');
            $target_featured_value = [
              'target_id' => $nodeid['upload_assco_image'][0],
              'alt' => '',
              'title' => '',
            ];
            // print_r($target_featured_value);
            $node->set('field_associated_image', $target_featured_value);
            $node->save();
            drupal_set_message($this->t('Associated Image Updated Successfully.'));
          }
        }

      }

    }
  }

}
