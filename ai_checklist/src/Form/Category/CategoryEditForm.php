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
class CategoryEditForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_category_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $first = NULL) {
    // Query for items to display.
    $entry = AiChecklistStorage::load('category_id', $first, 'ai_checklist_category');

    // Tell the user if there is nothing to display.
    if (empty($entry)) {
      $form['no_values'] = [
        '#markup' => t('<h3>No results found. Please goto checklist category Add page.</h3>'),
      ];
      return $form;
    }
    $entry = $entry[0];

    $form['category_id'] = [
      '#type' => 'value',
      '#value' => $entry->category_id,
    ];
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Category Name'),
      '#description' => $this->t("Update Category Ex. General Information, Demonstration etc."),
      '#default_value' => $entry->category_name,
    ];

    $form['weight'] = [
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#default_value' => $entry->weight,
      '#delta' => 50,
      '#description' => t('Heavier Category will sink and the lighter items will float to the top.'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Category'),
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
      'category_id' => $values['category_id'],
      'weight' => $values['weight'],
    ];
    $return = AiChecklistStorage::update($entry, 'ai_checklist_category', 'category_id');
    if ($return) {
      drupal_set_message($this->t('Category has been updated Successfully.'));
      $url = Url::fromRoute('ai_checklist.category');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message($this->t('Error while updating.'), 'error');
    }
  }

}
