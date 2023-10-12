<?php
namespace Drupal\ai_related_content\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldStorageConfig;


/**
* Defines a form that configures site matrix settings.
*/
class SiteMatricConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'site_matrix_config_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'site_matrix_config_admin.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $config = $this->config('site_matrix_config_admin.settings');

    // This is the field Group fieldset.
    $form['matrix_settings'] = array(
        '#type' => 'details',
        '#title' => t('Site matrix settings'),
        '#group' => 'settingsform',
        '#open' => TRUE,
    );
   // google analytics settings
    $form['matrix_settings']['display_google_matrix'] = [
      '#type' => 'checkbox',
      '#title' => t('Display google matrix'),
      '#default_value' => $config->get('display_google_matrix'),
    ];
    $form['matrix_settings']['footfall_text'] = [
      '#type' => 'textfield',
      '#title' => t('Footfall text'),
      '#default_value' => !empty($config->get('footfall_text')) ? $config->get('footfall_text') : 'Gallery Footfall',
    ];
    $form['matrix_settings']['user_text'] = [
      '#type' => 'textfield',
      '#title' => t('User text'),
      '#default_value' => !empty($config->get('user_text')) ? $config->get('user_text') : 'Growing Userbase',
    ];

    // Asset matrix settings.
    $form['matrix_settings']['display_asset_matrix'] = [
      '#type' => 'checkbox',
      '#title' => t('Display asset stats'),
      '#default_value' => $config->get('display_asset_matrix'),
    ];
    $form['matrix_settings']['display_asset_default_value'] = [
      '#type' => 'checkbox',
      '#title' => t('Display asset default value'),
      '#default_value' => $config->get('display_asset_default_value'),
    ];
    $form['matrix_settings']['asset_default_display_value'] = [
      '#type' => 'textfield',
      '#title' => t('Asset default display value'),
      '#default_value' => !empty($config->get('asset_default_display_value')) ? $config->get('asset_default_display_value') : '183',
    ];
    $form['matrix_settings']['asset_text'] = [
      '#type' => 'textfield',
      '#title' => t('Asset text'),
      '#default_value' => !empty($config->get('asset_text')) ? $config->get('asset_text') : 'Our Assets',
    ];

    // Asset matrix settings.
    $form['matrix_settings']['display_live_demo_matrix'] = [
      '#type' => 'checkbox',
      '#title' => t('Display live demo stats'),
      '#default_value' => $config->get('display_live_demo_matrix'),
    ];
    $form['matrix_settings']['display_livedemo_default_value'] = [
      '#type' => 'checkbox',
      '#title' => t('Display live demo default value'),
      '#default_value' => $config->get('display_livedemo_default_value'),
    ];
    $form['matrix_settings']['live_demo_default_display_value'] = [
      '#type' => 'textfield',
      '#title' => t('Live demo default display value'),
      '#default_value' => !empty($config->get('live_demo_default_display_value')) ? $config->get('live_demo_default_display_value') : '47',
    ];
    $form['matrix_settings']['live_demo_text'] = [
      '#type' => 'textfield',
      '#title' => t('Live demo text'),
      '#default_value' => !empty($config->get('live_demo_text')) ? $config->get('live_demo_text') : 'Live Demo/Videos',
    ];
    // Asset matrix settings.
    $form['matrix_settings']['display_stakeholder_matrix'] = [
      '#type' => 'checkbox',
      '#title' => t('Display stakeholder stats'),
      '#default_value' => $config->get('display_stakeholder_matrix'),
    ];
    $form['matrix_settings']['display_stakeholder_default_value'] = [
      '#type' => 'checkbox',
      '#title' => t('Display stakeholder default value'),
      '#default_value' => $config->get('display_stakeholder_default_value'),
    ];
    $form['matrix_settings']['stakeholder_connect_default_display_value'] = [
      '#type' => 'textfield',
      '#title' => t('Stakeholder connect default display value'),
      '#default_value' => !empty($config->get('stakeholder_connect_default_display_value')) ? $config->get('stakeholder_connect_default_display_value') : '191',
    ];
    $form['matrix_settings']['stakeholder_text'] = [
      '#type' => 'textfield',
      '#title' => t('Stakeholder text'),
      '#default_value' => !empty($config->get('stakeholder_text')) ? $config->get('stakeholder_text') : 'Stakeholder Reachouts',
    ];
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('site_matrix_config_admin.settings');

    $config->set('display_google_matrix', $form_state->getValue('display_google_matrix'));
    $config->set('footfall_text', $form_state->getValue('footfall_text'));
    $config->set('matrix_settings', $form_state->getValue('matrix_settings'));
    $config->set('user_text', $form_state->getValue('user_text'));
    $config->set('display_asset_matrix', $form_state->getValue('display_asset_matrix'));
    $config->set('display_asset_default_value', $form_state->getValue('display_asset_default_value'));
    $config->set('asset_default_display_value', $form_state->getValue('asset_default_display_value'));
    $config->set('asset_text', $form_state->getValue('asset_text'));
    $config->set('display_live_demo_matrix', $form_state->getValue('display_live_demo_matrix'));
    $config->set('display_livedemo_default_value', $form_state->getValue('display_livedemo_default_value'));
    $config->set('live_demo_default_display_value', $form_state->getValue('live_demo_default_display_value'));
    $config->set('live_demo_text', $form_state->getValue('live_demo_text'));
    $config->set('display_stakeholder_matrix', $form_state->getValue('display_stakeholder_matrix'));
    $config->set('display_stakeholder_default_value', $form_state->getValue('display_stakeholder_default_value'));
    $config->set('stakeholder_connect_default_display_value', $form_state->getValue('stakeholder_connect_default_display_value'));
    $config->set('stakeholder_text', $form_state->getValue('stakeholder_text'));
    

    $config->save();

    parent::submitForm($form, $form_state);
  }
}
