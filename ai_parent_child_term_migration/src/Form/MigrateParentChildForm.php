<?php

namespace Drupal\ai_parent_child_term_migration\Form;


use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Form\FormBase;


class MigrateParentChildForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_migrate_primary_industry_domain_form';
  }
  
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
     $form['submit'] = [
      '#type'    => 'submit',
      '#value' => t('Migrate Parent Child Terms Of Contents'),
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
  }
}