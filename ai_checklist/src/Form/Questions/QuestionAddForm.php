<?php

namespace Drupal\ai_checklist\Form\Questions;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to Update a AI Category.
 */
class QuestionAddForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_question_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $existing_categories = AiChecklistStorage::loadAll('ai_checklist_category');
    $categories['-1'] = $this->t('Select Category');
    foreach ($existing_categories as $category) {
      $categories[$category->category_id] = $category->category_name;
    }

    $existing_subcategories = AiChecklistStorage::loadAll('ai_checklist_subcategory');
    $sub_categories['-1'] = $this->t('Select Sub Category');
    $sub_category_parent = [];
    foreach ($existing_subcategories as $sub_category) {
      $sub_categories[$sub_category->sub_category_id] = $sub_category->sub_category_name;
      $sub_category_parent[$sub_category->sub_category_id] = $categories[$sub_category->category_id];
    }

    $form['checklist_question'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Question'),
      '#required' => TRUE,
      '#description' => $this->t("Add Question"),
      '#default_value' => '',
    ];

    $form['associated_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Category'),
      '#options' => $categories,
      '#description' => $this->t("Select associated category from the list."),
      '#default_value' => '-1',
      '#ajax'    => [
        'callback' => [$this, 'selectSubCategoryAjax'],
        'wrapper'  => 'sub_category_wrapper',
      ],
    ];

    $form['associated_sub_category'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Sub Category'),
      '#options'   => ['-1' => $this->t('- Select a category before -')],
      '#description' => $this->t("Select associated sub category from the list."),
      '#prefix'    => '<div id="sub_category_wrapper">',
      '#suffix'    => '</div>',
      '#validated' => TRUE,
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
      '#value' => $this->t('Add Question'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    if ($values['associated_category'] == '-1') {
      $form_state->setErrorByName('associated_category', 'Please Select Category to which this question is associated with!');
    }
    if ($values['associated_sub_category'] == '-1') {
      $form_state->setErrorByName('associated_sub_category', 'Please Select Sub Category to which this question is associated with!');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Save the submitted entry.
    $entry = [
      'checklist_question' => $values['checklist_question'],
      'sub_category_id' => $values['associated_sub_category'],
      'weight' => 0,
    ];
    $return = AiChecklistStorage::insert($entry, 'ai_checklist_questions');
    if ($return) {
      drupal_set_message($this->t('Question has been created Successfully.'));
      $url = Url::fromRoute('ai_checklist.questions');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message($this->t('Error while creating.'), 'error');
    }
  }

  /**
   * Called via Ajax to populate the Sub Category field according brand.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form model field structure.
   */
  public function selectSubCategoryAjax(array &$form, FormStateInterface $form_state) {
    $options = [];
    $category_id = $form_state->getValue('associated_category');

    if ($category_id == '-1') {
      $form['associated_sub_category']['#options'] = ['-1' => $this->t('- Select a category before -')];
      return $form['associated_sub_category'];
    }

    $sub_categories = AiChecklistStorage::loadAllSubcategoryByCategory($category_id);
    foreach ($sub_categories as $sub_category) {
      $options[$sub_category->sub_category_id] = $sub_category->sub_category_name;
    }

    if (empty($options)) {
      $options = ['-1' => 'No Sub category'];
    }
    $form['associated_sub_category']['#options'] = $options;

    return $form['associated_sub_category'];
  }

}
