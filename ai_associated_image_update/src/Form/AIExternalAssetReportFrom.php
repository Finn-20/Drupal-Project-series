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
class AIExternalAssetReportFrom extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_external_asset_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('External Assets Report');
	
	$form['csvdownload'] = [
     '#type' => 'markup',
      '#title' => t('No of Internal Published Assets'),
	  '#markup' => '<a href="/external-assets-reportcsv?page&_format=csv">Download</a>',
    ];
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
	$form['summary'] = [
      '#type'  => 'details',
      '#title' => $this->t('Summary Statistics'),
	  '#open' => TRUE,
    ];
	$form['summary']['noofin'] = [
      '#type'  => 'details',
      '#title' => $this->t('No of Internal Published Assets'),
      '#open' => TRUE,
	 
    ];
	
	$intstorage = \Drupal::database()->select('node_field_data', 'nfd')
    ->fields('nfd', ['nid'])
    ->fields('nf', ['field_external_flag_value'])
    ->condition('nfd.status', 1)
    ->condition('nfd.type', 'use_case_or_accelerator')
	->condition('nf.field_external_flag_value', 1);
  $intstorage->join('node__field_external_flag', 'nf', 'nf.entity_id = nfd.nid');
  //print $intstorage;
  $indu_result = $intstorage->execute()->fetchAll();
	

	 $form['summary']['noofin']['intenral'] = [
      '#type' => 'markup',
      '#title' => t('No of Internal Published Assets'),
	  '#markup' => count($indu_result),
    ];
	$extstorage = \Drupal::database()->select('node_field_data', 'nfd')
    ->fields('nfd', ['nid'])
    ->fields('nf', ['field_external_flag_value'])
    ->condition('nfd.status', 1)
    ->condition('nfd.type', 'use_case_or_accelerator')
	->condition('nf.field_external_flag_value', 0);
  $extstorage->join('node__field_external_flag', 'nf', 'nf.entity_id = nfd.nid');
 
  $ext_result = $extstorage->execute()->fetchAll();
	
	$form['summary']['noofex'] = [
      '#type'  => 'details',
      '#title' => $this->t('No of External Published Assets'),
      '#open' => TRUE,
	 
    ];
	$form['summary']['noofex']['external'] = [
      '#type' => 'markup',
      '#title' => t('No of External Published Assets'),
	  '#markup' => count($ext_result),
    ];
	
    $header = ['NodeID', 'Title', 'External View'];
    // Add the headers.
    $form['asset_external_rep'] = [
      '#type' => 'table',
      '#title' => 'External Asset Report',
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
      $nodeurl = $node->toUrl('canonical', [
        'absolute' => TRUE,
        'language' => $node->language(),
      ])->toString();
      $title = $node->title->value;
  //  print_r($node->field_external_flag);die;
	$node_externalflag = $node->field_external_flag->value;
     if(!empty($node_externalflag)){
		 if($node_externalflag == '0'){
		  $externalview = 'No';
	  } 
	  if($node_externalflag == '1'){
		  $externalview = 'Yes';
	  }
	 }else{
		 $externalview = 'Need to Update'; 
	  }
      $form['asset_external_rep'][$nodeid]['asset_nid'] = [
        '#type' => 'markup',
        '#markup' => $nodeid,
      ];
      $form['asset_external_rep'][$nodeid]['assoc_title'] = [
        '#type' => 'markup',
        '#markup' => '<a href="'.$nodeurl.'" target="_blank">'.$title.'</a>',
      ];
      $form['asset_external_rep'][$nodeid]['externalview'] = [
          '#type' => 'markup',
          '#title' => 'External View(Y/N)',
		  '#markup' => $externalview,
        ];
    }
    /* $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Apply Bulk Image Update',
    ]; */
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

  
  }

}
