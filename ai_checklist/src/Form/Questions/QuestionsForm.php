<?php

namespace Drupal\ai_checklist\Form\Questions;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to add a AI Category.
 */
class QuestionsForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_questions_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $categories = [];
    $sub_categories = [];
    $questions = [];

    $existing_categories = AiChecklistStorage::loadAll('ai_checklist_category');
    $existing_subcategories = AiChecklistStorage::loadAll('ai_checklist_subcategory');

    foreach ($existing_categories as $category) {
      $categories[$category->category_id] = $category->category_name;
    }

    $existing_subcategories = AiChecklistStorage::loadAll('ai_checklist_subcategory');
    $sub_category_parent = [];
    foreach ($existing_subcategories as $sub_category) {
      $sub_categories[$sub_category->sub_category_id] = $sub_category->sub_category_name;
      $sub_category_parent[$sub_category->sub_category_id] = $categories[$sub_category->category_id];
    }

    $header = [
      'question' => 'Checklist Questions',
      'category_name' => 'Category Name',
      'sub_category_name' => 'Sub Category Name',
      'weight' => 'Weight',
      'action' => 'Actions',
    ];

    $existing_questions = AiChecklistStorage::loadAll('ai_checklist_questions');

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      // '#rows' => $rows,
      '#empty' => $this->t('There is no question added yet. <a href="/admin/config/content/checklist_questions/add">Add a question.</a>'),
      '#tableselect' => FALSE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-order-weight',
        ],
      ],
    ];

    foreach ($existing_questions as $question) {
      $id = $question->question_id;

      // TableDrag: Mark the table row as draggable.
      $form['table'][$id]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['table'][$id]['#weight'] = $question->weight;

      // Some table columns containing raw markup.
      $form['table'][$id]['question'] = [
        '#plain_text' => $question->checklist_question,
      ];

      $form['table'][$id]['category_name'] = [
        '#plain_text' => $sub_category_parent[$question->sub_category_id],
      ];

      $form['table'][$id]['sub_category_name'] = [
        '#plain_text' => $sub_categories[$question->sub_category_id],
      ];

      $form['table'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $question->checklist_question]),
        '#title_display' => 'invisible',
        '#default_value' => $question->weight,
        '#delta' => 50,
        // Classify the weight element for #tabledrag.
        '#attributes' => ['class' => ['table-order-weight']],
      ];

      // Operations (dropbutton) column.
      $form['table'][$id]['operations'] = [
        '#type' => 'operations',
        '#links' => [],
      ];

      $form['table'][$id]['operations']['#links']['edit'] = [
        'title' => t('Edit'),
        'url' => Url::fromUserInput('/admin/config/content/checklist_questions/' . $id . '/edit'),
      ];
      $form['table'][$id]['operations']['#links']['delete'] = [
        'title' => t('Delete'),
        'url' => Url::fromUserInput('/admin/config/content/checklist_questions/' . $id . '/delete'),
      ];
      $questions[$id] = ['sub_category_id' => $question->sub_category_id, 'checklist_question' => $question->checklist_question];
    }
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save changes'),
        // TableSelect: Enable the built-in form validation for #tableselect for
        // this form button, so as to ensure that the bulk operations form cannot
        // be submitted without any selected items.
      '#tableselect' => TRUE,
    ];
    $form['questions'] = [
      '#type' => 'value',
      '#value' => $questions,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $questions_details = $values['questions'];

    // Save the submitted entry.
    foreach ($values['table'] as $question_id => $question_weight) {
      $entry = [
        'question_id' => $question_id,
        'sub_category_id' => $questions_details[$question_id]['sub_category_id'],
        'checklist_question' => $questions_details[$question_id]['checklist_question'],
        'weight' => $question_weight['weight'],
      ];
      // Save the submitted entry.
      $return = AiChecklistStorage::update($entry, 'ai_checklist_questions', 'question_id');
    }
    if ($return) {
      drupal_set_message($this->t('Questions has been saved Successfully.'));
    }
    else {
      drupal_set_message($this->t('Error while creating.'), 'error');
    }
  }

}
