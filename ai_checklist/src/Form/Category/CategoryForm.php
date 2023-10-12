<?php

namespace Drupal\ai_checklist\Form\Category;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to add a AI Category.
 */
class CategoryForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_category_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $existing_categories = AiChecklistStorage::loadAll('ai_checklist_category');
    $categories = [];
    $header = [
      'category_name' => 'Category Name',
      'weight' => 'Weight',
      'action' => 'Actions',
    ];

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      // '#rows' => $rows,
      '#empty' => $this->t('There is no category added yet. <a href="/admin/config/content/checklist_category/add">Add an category.</a>'),
      '#tableselect' => FALSE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-order-weight',
        ],
      ],
    ];

    foreach ($existing_categories as $category) {
      $id = $category->category_id;

      // TableDrag: Mark the table row as draggable.
      $form['table'][$id]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['table'][$id]['#weight'] = $category->weight;

      // Some table columns containing raw markup.
      $form['table'][$id]['category_name'] = [
        '#plain_text' => $category->category_name,
      ];

      $form['table'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $category->category_name]),
        '#title_display' => 'invisible',
        '#default_value' => $category->weight,
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
        'url' => Url::fromUserInput('/admin/config/content/checklist_category/' . $category->category_id . '/edit'),
      ];
      $form['table'][$id]['operations']['#links']['delete'] = [
        'title' => t('Delete'),
        'url' => Url::fromUserInput('/admin/config/content/checklist_category/' . $category->category_id . '/delete'),
      ];
      $categories[$id] = $category->category_name;
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
    $form['categories'] = [
      '#type' => 'value',
      '#value' => $categories,
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
    $category_names = $values['categories'];
    foreach ($values['table'] as $category_id => $category_weight) {
      $entry = [
        'category_id' => $category_id,
        'category_name' => $category_names[$category_id],
        'weight' => $category_weight['weight'],
      ];
      // Save the submitted entry.
      $return = AiChecklistStorage::update($entry, 'ai_checklist_category', 'category_id');
    }
    if ($return) {
      drupal_set_message($this->t('Category has been updated Successfully.'));
    }
    else {
      drupal_set_message($this->t('Error while updating.'), 'error');
    }
  }

}
