<?php

namespace Drupal\ai_content_reference_filter\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure ai_login_details settings for this site.
 */
class ReferenceFilterSettingsForm extends ConfigFormBase
{

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'reference.filter.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'tracker_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {
	 $vid = 'profile_group';
     $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
     $term_data = array();
     foreach ($terms as $term) {
       $term_data[] = array(
       'id' => $term->tid,
       'name' => $term->name
     );
	 }

	 $config = $this->config(static::SETTINGS);
	 $vid_voc = 'reference_filter_fields';
   $voc_terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid_voc);
   foreach ($voc_terms as $vterm) {
     $voc_term_data[] = array(
       'tid' => $vterm->tid,
       'tname' => $vterm->name
     );
	 }
  $options = array();
	foreach($voc_term_data as $term_value) {
    $options += array(
      $term_value['tname'] => $term_value['tname']
    );
  }

$form['scope_profile'] = array(
  '#type' => 'details',
  '#title' => $this->t('Show/Hide fields settings'),
  );
	foreach($term_data as $term_val) {

  $form['scope_profile'][$term_val['name']] = array (
    '#open' => TRUE,

    '#type' => 'checkboxes',
    '#title' => $this->t($term_val['name']),
    '#options' => $options,
   // '#attributes' => array('class' => array('ref_filter_class')),
    '#default_value' => $config->get($term_val['name'])
    );

	}


  $vid_scope = 'content_scope';
  $scope_terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid_scope);
  foreach ($scope_terms as $term_val) {
    $term_val_data[] = array(
      'tid' => $term_val->tid,
      'tname' => $term_val->name
    );
  }
  $options_scope = array();
  foreach($term_val_data as $term_data_s) {
    $options_scope += array(
    $term_data_s['tname'] => $term_data_s['tname']
    );
  }


$form['Scope'] = array(
  '#type' => 'details',
  '#title' => $this->t('Scope'),
  );
  foreach($term_data as $key =>  $term_val) {


    $fieldname = $term_val['name'].'_scope';
    $form['Scope'][$fieldname] = array (
      '#type' => 'radios',
      '#title' => $this->t($term_val['name']),
      '#options' => $options_scope,

      '#default_value' => $config->get($term_val['name'].'_scope')
      );
  }
  return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $vid = 'profile_group';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $term_data[] = array(
      'id' => $term->tid,
      'name' => $term->name
      );
	  }
	 foreach($term_data as $term_val) {
     $this->configFactory->getEditable(static::SETTINGS)
     // Set the submitted configuration setting.
      ->set($term_val['name'], $form_state->getValue($term_val['name']))
      ->set($term_val['name'].'_scope', $form_state->getValue($term_val['name'].'_scope'))
      ->save();
	 }

      parent::submitForm($form, $form_state);
  }
}
