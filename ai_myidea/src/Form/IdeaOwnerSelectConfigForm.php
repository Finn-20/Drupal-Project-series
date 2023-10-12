<?php
namespace Drupal\ai_myidea\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\field\Entity\FieldStorageConfig;


/**
* Defines a form that configures module settings.
*/
class IdeaOwnerSelectConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'industry_owner_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'idea_owner_select.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $config = $this->config('idea_owner_select.settings');
    $region_details = $config->get('ideaowner_region_fieldset');
	$form['ai_ideaownersele'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('AI Owner Select'),
    ];
	// This is the field Group fieldset.
    $form['ideaowner_region_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Idea Region Owner Settings'),
        '#group' => 'ai_ideaownersele',
        '#open' => TRUE,
    );
	$region = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('geography');
	$regionterms = array();
	foreach ($region as $regions) {
		$regionterms[$regions->tid] = $regions->name;
	}

	$form['ideaowner_region_fieldset']['region_terms'] = array(
		'#type' => 'select',
		'#options' => $regionterms,
		'#title' => t('Region'),
	);
	$form['ideaowner_region_fieldset']['reg_authoruid'] = [
		'#type' => 'entity_autocomplete',
		'#target_type' => 'user',
		'#selection_settings' => ['include_anonymous' => FALSE],
		'#title' => t('Region Onwers'),
	];
	
	$regheader = [
	'regionname' => t('Region'),
	'regionowner' => t('Owner Name'),
	];
	$form['ideaowner_region_fieldset']['table'] = [
	'#type' => 'tableselect',
	'#header' => $regheader,
	'#options' => '',
	'#empty' => t('No users found'),
	];
	
	//Industry Owner Details
	$form['ideaowner_industry_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Idea Industry Owner Settings'),
        '#group' => 'ai_ideaownersele',
        '#open' => TRUE,
    );
	$industrie = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('industries');
	$industryterms = array();
	foreach ($industrie as $industries) {
		$industryterms[$industries->tid] = $industries->name;
	}
    $form['ideaowner_industry_fieldset']['industry_terms'] = array(
		'#type' => 'select',
		'#options' => $industryterms,
		'#title' => t('Industry'),
	);
	$form['ideaowner_industry_fieldset']['indu_authoruid'] = [
		'#type' => 'entity_autocomplete',
		'#target_type' => 'user',
		'#selection_settings' => ['include_anonymous' => FALSE],
		'#title' => t('Region Onwers'),
	];
	//Domain Owner Details
	$form['ideaowner_domain_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Idea Domain Owner Settings'),
        '#group' => 'ai_ideaownersele',
        '#open' => TRUE,
    );
	$domain = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('domain');
	$domainsterms = array();
	foreach ($domain as $domains) {
		$domainsterms[$domains->tid] = $domains->name;
	}
    $form['ideaowner_domain_fieldset']['industry_terms'] = array(
		'#type' => 'select',
		'#options' => $domainsterms,
		'#title' => t('Domains'),
	);
	$form['ideaowner_domain_fieldset']['domain_authoruid'] = [
		'#type' => 'entity_autocomplete',
		'#target_type' => 'user',
		'#selection_settings' => ['include_anonymous' => FALSE],
		'#title' => t('Domain Onwers'),
	];
	
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
	
    $config = $this->config('idea_owner_select.settings');
    
    $config->set('region_terms', $form_state->getValue('region_terms'));
    $config->set('reg_authoruid', $form_state->getValue('reg_authoruid'));
	$config->set('region_terms', $form_state->getValue('industry_terms'));
    $config->set('reg_authoruid', $form_state->getValue('rexg nnnnnbbx d_authoruid'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
}
