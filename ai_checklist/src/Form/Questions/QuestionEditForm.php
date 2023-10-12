<?php

namespace Drupal\ai_checklist\Form\Questions;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to Update a AI Category.
 */
class QuestionEditForm extends QuestionAddForm {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_question_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $first = NULL) {
    $default_category = '-1';
    $default_sub_category = '-1';

    // Query for items to display.
    $entry = AiChecklistStorage::load('question_id', $first, 'ai_checklist_questions');
    $default_question = isset($entry[0]->checklist_question) && !empty($entry[0]->checklist_question) ? $entry[0]->checklist_question : '';
    $default_weight = isset($entry[0]->weight) && !empty($entry[0]->weight) ? $entry[0]->weight : '0';

    if (isset($entry[0]->sub_category_id) && !empty($entry[0]->sub_category_id)) {
      $default_sub_category = $entry[0]->sub_category_id;
      $sub_category = AiChecklistStorage::load('sub_category_id', $default_sub_category, 'ai_checklist_subcategory');
      $default_category = isset($sub_category[0]->category_id) && !empty($sub_category[0]->category_id) ? $sub_category[0]->category_id : '-1';
    }

    $options['-1'] = $this->t('- Select sub category -');
    $sub_categories = AiChecklistStorage::load('category_id', $default_category, 'ai_checklist_subcategory');
    foreach ($sub_categories as $sub_category) {
      $options[$sub_category->sub_category_id] = $sub_category->sub_category_name;
    }

    $form = parent::buildForm($form, $form_state);
    $form['checklist_question']['#default_value'] = $default_question;
    $form['associated_category']['#default_value'] = $default_category;

    $form['associated_sub_category']['#default_value'] = $default_sub_category;
    $form['associated_sub_category']['#options'] = $options;

    $form['weight']['#default_value'] = $default_weight;

    $form['question_id'] = [
      '#type' => 'value',
      '#value' => $first,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Question'),
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
      'question_id' => $values['question_id'],
      'checklist_question' => $values['checklist_question'],
      'sub_category_id' => $values['associated_sub_category'],
      'weight' => $values['weight'],
    ];
    $return = AiChecklistStorage::update($entry, 'ai_checklist_questions', 'question_id');
    if ($return) {
      drupal_set_message($this->t('Question has been updated Successfully.'));
      $url = Url::fromRoute('ai_checklist.questions');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message($this->t('Error while creating.'), 'error');
    }
  }

}
