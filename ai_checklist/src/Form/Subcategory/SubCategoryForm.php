<?php

namespace Drupal\ai_checklist\Form\Subcategory;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ai_checklist\AiChecklistStorage;

/**
 * Form to add a AI Category.
 */
class SubCategoryForm implements FormInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_sub_category_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $existing_categories = AiChecklistStorage::loadAll('ai_checklist_category');
    $existing_subcategories = AiChecklistStorage::loadAll('ai_checklist_subcategory');

    $categories = [];
    $sub_categories = [];

    foreach ($existing_categories as $category) {
      $categories[$category->category_id] = $category->category_name;
    }

    $header = [
      'sub_category_name' => 'Sub Category Name',
      'category_name' => 'Category Name',
      'weight' => 'Weight',
      'action' => 'Actions',
    ];

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      // '#rows' => $rows,
      '#empty' => $this->t('There is no sub category added yet. <a href="/admin/config/content/checklist_sub_category/add">Add an sub category.</a>'),
      '#tableselect' => FALSE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-order-weight',
        ],
      ],
    ];
    foreach ($existing_subcategories as $sub_category) {
      $id = $sub_category->sub_category_id;

      // TableDrag: Mark the table row as draggable.
      $form['table'][$id]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['table'][$id]['#weight'] = $sub_category->weight;

      // Some table columns containing raw markup.
      $form['table'][$id]['sub_category_name'] = [
        '#plain_text' => $sub_category->sub_category_name,
      ];

      $form['table'][$id]['category_name'] = [
        '#plain_text' => $categories[$sub_category->category_id],
      ];

      $form['table'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $sub_category->sub_category_name]),
        '#title_display' => 'invisible',
        '#default_value' => $sub_category->weight,
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
        'url' => Url::fromUserInput('/admin/config/content/checklist_sub_category/' . $id . '/edit'),
      ];
      $form['table'][$id]['operations']['#links']['delete'] = [
        'title' => t('Delete'),
        'url' => Url::fromUserInput('/admin/config/content/checklist_sub_category/' . $id . '/delete'),
      ];
      $sub_categories[$id] = ['sub_category_name' => $sub_category->sub_category_name, 'category_id' => $sub_category->category_id];
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
    $form['sub_categories'] = [
      '#type' => 'value',
      '#value' => $sub_categories,
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
    $sub_category_details = $values['sub_categories'];

    // Save the submitted entry.
    foreach ($values['table'] as $sub_category_id => $sub_category_weight) {
      $entry = [
        'sub_category_id' => $sub_category_id,
        'sub_category_name' => $sub_category_details[$sub_category_id]['sub_category_name'],
        'category_id' => $sub_category_details[$sub_category_id]['category_id'],
        'weight' => $sub_category_weight['weight'],
      ];
      // Save the submitted entry.
      $return = AiChecklistStorage::update($entry, 'ai_checklist_subcategory', 'sub_category_id');
    }
    if ($return) {
      drupal_set_message($this->t('Sub Categories has been saved Successfully.'));
    }
    else {
      drupal_set_message($this->t('Error while creating.'), 'error');
    }
  }

}
