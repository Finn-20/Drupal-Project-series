<?php

namespace Drupal\ai_checklist\Form\Category;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to Update a AI Category.
 */
class CategoryAddForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_category_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $first = NULL) {
    // Tell the user if there is nothing to display.
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category Name'),
      '#description' => $this->t("Add Category Ex. General Information, Demonstration etc."),
    ];

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#default_value' => '0',
      '#delta' => 50,
      '#description' => t('Heavier Category will sink and the lighter items will float to the top.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Category'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Save the submitted entry.
    $entry = [
      'category_name' => $values['category_name'],
      'weight' => $values['weight'],
    ];
    $return = AiChecklistStorage::insert($entry, 'ai_checklist_category');
    if ($return) {
      drupal_set_message($this->t('Category has been added Successfully.'));
      $url = Url::fromRoute('ai_checklist.category');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message($this->t('Error while updating.'), 'error');
    }
  }

}
