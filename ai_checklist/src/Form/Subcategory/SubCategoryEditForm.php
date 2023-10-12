<?php

namespace Drupal\ai_checklist\Form\Subcategory;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to Update a AI Category.
 */
class SubCategoryEditForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_sub_category_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $first = NULL) {
    // Query for items to display.
    $entry = AiChecklistStorage::load('sub_category_id', $first, 'ai_checklist_subcategory');

    // Tell the user if there is nothing to display.
    if (empty($entry)) {
      $form['no_values'] = [
        '#markup' => t('<h3>No results found. Please goto checklist sub category Add page.</h3>'),
      ];
      return $form;
    }
    $entry = $entry[0];

    $form['sub_category_id'] = [
      '#type' => 'value',
      '#value' => $entry->sub_category_id,
    ];
    $form['sub_category_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sub Category Name'),
      '#description' => $this->t("Update Sub Category Ex. Taxonomy, Business Drivers etc."),
      '#default_value' => $entry->sub_category_name,
    ];

    $existing_categories = AiChecklistStorage::loadAll('ai_checklist_category');
    $options['-1'] = $this->t('Select Category');
    foreach ($existing_categories as $category) {
      $options[$category->category_id] = $category->category_name;
    }

    $form['associated_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Category'),
      '#options' => $options,
      '#description' => $this->t("Select associated category from the list."),
      '#default_value' => $entry->category_id,
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
      '#value' => $this->t('Update Sub Category'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($values['associated_category'] == '-1') {
      $form_state->setErrorByName('associated_category', 'Please Select Category to which this sub category associated with!');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Save the submitted entry.
    $entry = [
      'sub_category_id' => $values['sub_category_id'],
      'category_id' => $values['associated_category'],
      'sub_category_name' => $values['sub_category_name'],
      'weight' => $values['weight'],
    ];
    $return = AiChecklistStorage::update($entry, 'ai_checklist_subcategory', 'sub_category_id');
    if ($return) {
      drupal_set_message($this->t('Sub Category has been created Successfully.'));
      $url = Url::fromRoute('ai_checklist.sub_category');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message($this->t('Error while creating.'), 'error');
    }
  }

}
