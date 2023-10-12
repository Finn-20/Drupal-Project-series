<?php

namespace Drupal\ai_contact_owner_tracking\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_contact_owner_tracking\AIContactOwnerTrackingStorage;
use Drupal\Core\Link;
use Drupal\user\Entity\User;

/**
 * Form to filter contact owner tracking report.
 */
class ContactOwnerTrackingReport implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_contact_owner_tracking_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $where = [];
    
    $is_submitted = \Drupal::request()->query->get('is_submitted');
    if (isset($is_submitted) && !empty($is_submitted) && in_array($is_submitted, ['yes', 'no'])) {
      $submitted_value = ($is_submitted == 'yes') ? '1' : '0';
      $where['is_submitted'] = ['value' => $submitted_value, 'operator' => '='];
    }

    $date_from = \Drupal::request()->query->get('date_from');
    if (isset($date_from) && !empty($date_from)) {
      $date_from_value = strtotime($date_from . ' 00:00:00');
      $where['date_from'] = ['value' => $date_from_value, 'operator' => '>='];
    }
    
    $date_to = \Drupal::request()->query->get('date_to');
    if (isset($date_to) && !empty($date_to)) {
      $date_to_value = strtotime($date_to . ' 23:59:59');
      $where['date_to'] = ['value' => $date_to_value, 'operator' => '<'];
    }
    
    $tracking_data = AIContactOwnerTrackingStorage::loadAll('timestamp', $where);
    $data = [];
    $header = [
      'nid' => 'Content Title',
      'uid' => 'Submitted By',
      'is_submitted' => 'Submitted?',
      'time' => 'Date/Time',
      'submission' => 'Details'
    ];
    
    $form['filter_by'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Filter Tracking Report By'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];
    
    $form['filter_by']['is_submitted'] = [
      '#type' => 'select',
      '#title' => $this->t('Form Submitted'),
      '#options' => ['-1' => 'Select',  'no' => 'No', 'yes' => 'Yes'],
      '#default_value' => isset($is_submitted) && !empty($is_submitted) ? $is_submitted : '-1',
      '#prefix' => '<div class="filters_wrapper"><div class="filter_is_submitted">',
      '#suffix' => '</div>'
    ];
    
    $form['filter_by']['date_from'] = [
      '#type' => 'date',
      '#title' => $this->t('Date range (From)'),
      '#default_value' => isset($date_from) && !empty($date_from) ? $date_from : '',
      '#prefix' => '<div class="filter_date_range"><div class="filter_date_from">',
      '#suffix' => '</div>',
    ];
    
    $form['filter_by']['date_to'] = [
      '#type' => 'date',
      '#title' => $this->t('To'),
      '#default_value' => isset($date_to) && !empty($date_to) ? $date_to : '',
      '#prefix' => '<div class="filter_date_to">',
      '#suffix' => '</div><div class="clearfix"></div></div>',
    ];
    
    $form['filter_by']['actions'] = [
      '#prefix' => '<div class="filter_actions_wrapper">',
      '#suffix' => '</div><div class="clearfix"></div></div>'
          
    ];
    $form['filter_by']['actions']['apply_filter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply Filter'),
      '#name' => 'apply_filter',
    ];
    if (isset($where) && !empty($where)) {
      $form['filter_by']['actions']['reset'] = [
        '#type' => 'submit',
        '#value' => $this->t('Reset'),
        '#name' => 'reset_filter',
        '#submit' => array([$this, 'resetFilters']),
      ];
    }
    
    $form['filter_by']['actions']['export_to_csv'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
      '#name' => 'export_to_csv',
      '#submit' => array([$this, 'exportToCsv']),
    ];
    
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      //'#rows' => $rows,
      '#empty' => $this->t('There is no record found yet.'),
      '#tableselect' => FALSE,
    ];

    foreach ($tracking_data as $tracking) {
      $id = $tracking->tracking_id;

      // Some table columns containing raw markup.
      $node_link = AIContactOwnerTrackingStorage::getNodeTitleLinkByNodeId($tracking->nid);
      $form['table'][$id]['content'] = array(
        '#markup' => render($node_link),
      );
      
      $user_link = AIContactOwnerTrackingStorage::getUserNameLinkByUserId($tracking->uid);
      $form['table'][$id]['uid'] = array(
        '#markup' => render($user_link),
      );
      
      $form['table'][$id]['is_submitted'] = array(
          '#plain_text' => $tracking->is_submitted ? 'Yes' : 'No',
      );
      
      $form['table'][$id]['time'] = array(
          '#plain_text' => date('d M Y, h:i',$tracking->timestamp),
      );
      
      $submission_details = 'NA';
      if ($tracking->submission_id) {
        $url = Url::fromUserInput('/admin/structure/webform/manage/contact/submission/' . $tracking->submission_id);
        $submission_details = Link::fromTextAndUrl(t('View Submission'), $url);
        $submission_details = $submission_details->toRenderable();
        // If you need some attributes.
        $submission_details['#attributes'] = ['target' => '_blank'];
      }
      $form['table'][$id]['submission'] = array(
        '#markup' => render($submission_details),
      );
    }
    
    $form['#attached']['library'][] = 'ai_contact_owner_tracking/ai_contact_owner_tracking_admin';
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
    $values = $form_state->getValues();
    $parameters = [];
    if (isset($values['is_submitted']) && !empty($values['is_submitted']) && $values['is_submitted'] != '-1') {
      $parameters['is_submitted'] = $values['is_submitted'];
    }
    
    if (isset($values['date_from']) && !empty($values['date_from'])) {
      $parameters['date_from'] = $values['date_from'];
    }
    
    if (isset($values['date_to']) && !empty($values['date_to'])) {
      $parameters['date_to'] = $values['date_to'];
    }
    
    # set form data in url redirect
    $option = [
      'query' => $parameters,
    ];
    $url = Url::fromRoute('ai_contact_owner_tracking.tracking_details', [], $option);
    $form_state->setRedirectUrl($url);
  }
  
  /**
   * Reset Filter submit handler.
   */
  public function resetFilters(array &$form, FormStateInterface $form_state) {
    $url = Url::fromRoute('ai_contact_owner_tracking.tracking_details');
    $form_state->setRedirectUrl($url);
  }
  
  /**
   * Export To CSV.
   */
  public function exportToCsv(array &$form, FormStateInterface $form_state) {
    // If you need to work on how data is extracted, process it here.
    $data = $this->getTrackingData($form_state->getValues());
    $operations[] = ['\Drupal\ai_contact_owner_tracking\exportTrackingDetails::getTrackingDetails', [$data]];

    $batch = [
      'title' => t('Exporting Data...'),
      'operations' => $operations,
      'init_message' => t('Export is starting.'),
      'finished' => '\Drupal\ai_contact_owner_tracking\exportTrackingDetails::getTrackingDetailsCallback',
    ];
    batch_set($batch);
  }
  
  public function getTrackingData($values){
    $where = [];
    if (isset($values['is_submitted']) && !empty($values['is_submitted']) && $values['is_submitted'] != '-1') {
      $submitted_value = ($values['is_submitted'] == 'yes') ? '1' : '0';
      $where['is_submitted'] = ['value' => $submitted_value, 'operator' => '='];
    }
    
    if (isset($values['date_from']) && !empty($values['date_from'])) {
      $date_from_value = strtotime($values['date_from'] . ' 00:00:00');
      $where['date_from'] = ['value' => $date_from_value, 'operator' => '>='];
    }
    
    if (isset($values['date_to']) && !empty($values['date_to'])) {
      $date_to_value = strtotime($values['date_to'] . ' 23:59:59');
      $where['date_to'] = ['value' => $date_to_value, 'operator' => '<'];
    }
    
    $tracking_data = AIContactOwnerTrackingStorage::loadAll('timestamp', $where);
    $data = [];
    foreach ($tracking_data as $tracking) {
      $record = [];
      
      $user_account = User::load($tracking->uid);
      // Some table columns containing raw markup.
      $record['node_id'] = $tracking->nid;
      $record['node_title'] = AIContactOwnerTrackingStorage::getNodeTitleByNodeId($tracking->nid);
      $record['node_url'] = AIContactOwnerTrackingStorage::getSiteBaseUrl() . \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $tracking->nid);;
      $record['uid'] = $tracking->uid;
      $record['user_name'] = $user_account->getDisplayName();
      $record['user_email'] = $user_account->getEmail();
      $record['is_submitted'] = $tracking->is_submitted ? 'Yes' : 'No';
      $record['time'] = date('d M Y, h:i',$tracking->timestamp);
      
      $submission_details = 'NA';
      if ($tracking->submission_id) {
        $submission_details = AIContactOwnerTrackingStorage::getSiteBaseUrl() . '/admin/structure/webform/manage/contact/submission/' . $tracking->submission_id;
      }
      $record['submission_detail'] = $submission_details;
      $data[] = $record;
    }
    return $data;
  }
}
