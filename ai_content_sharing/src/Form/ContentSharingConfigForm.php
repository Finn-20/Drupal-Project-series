<?php
namespace Drupal\ai_content_sharing\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
//use Drupal\field\Entity\FieldStorageConfig;


/**
* Defines a form that configures module settings.
*/
class ContentSharingConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contentsharing_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'content_sharing.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $config = $this->config('content_sharing.settings');
    
    $email_body_author_format = isset($email_body_author['format']) ? $email_body_author['format'] : 'basic_html';
    $content_email_body = $config->get('email_content_body');
    // Set default email body by reviewer value.
    $email_body_content = isset($content_email_body['value']) ? $content_email_body['value'] : '';
	// This is the field Group fieldset.
    $form['contentsharing_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Content Sharing settings'),
        '#group' => 'settingsform',
        '#open' => TRUE,
    );
   // This is the field Group fieldset.
    $form['contentsharing_fieldset']['email_content_body'] = [
      '#type' => 'text_format',
      '#title' => t('Email Template'),
      '#default_value' => $email_body_content,
      '#format' => $email_body_author_format,
    ];
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
	//print_r($values);die;
    $config = $this->config('content_sharing.settings');
    
    $config->set('email_content_body', $form_state->getValue('email_content_body'));
    

    $config->save();

    parent::submitForm($form, $form_state);
  }
}
