<?php

/**
 * @file
 * Contains \Drupal\demo\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\ai_collaborate\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class TribesAssestListingForm extends TribesAssetsMappingFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'tribes_assetListing';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $tribes_categories = $this->store->get('tribes_category');

    $tribes_headers[] = 'Title';
    foreach ($tribes_categories as $tribes_category) {
      $tribes_category_array = explode('|', $tribes_category);
      $tribes_headers[] = $tribes_category_array[1];
      $tribes_category_ids[] = $tribes_category_array[0];
    }

    $view_name = 'tribes_asset_matrix';
    $view = \Drupal\views\Views::getView($view_name);
    if (empty($view)) {
      return;
    }
    $view->setArguments(array());
    $view->execute();

    // Add the headers.
    $form['tribes_assets'] = [
      '#type' => 'table',
      '#title' => t('Asset details'),
      '#header' => $tribes_headers,
    ];

    foreach ($view->result as $key => $result) {
      $asset_id = $result->_entity->get('nid')->getValue()[0]['value'];

      $form['tribes_assets'][$asset_id]['title'] = array(
        '#markup' => $result->_entity->get('title')->getValue()[0]['value'],
      );

      $tribes_assets = $result->_entity->get('field_tribes_related_assets')->getValue();
	  
      foreach ($tribes_category_ids as $cid => $tribes_category_id) {
        $category_selected = FALSE;

        $index_key = array_search($tribes_category_id, array_column($tribes_assets, 'target_id'));

        if (!empty($result->_entity->get('field_tribes_related_assets')->getValue())) {
          if (!empty(is_numeric($index_key))) {
            $category_selected = TRUE;
            $category_selected_list[] = $tribes_category_id;
            $form['tribes_assets']['mapped_assets'][$asset_id] = [
              '#type' => 'hidden',
              '#default_value' => $category_selected_list,
            ];
          }
        }

        $form['tribes_assets'][$asset_id][$tribes_category_id] = [
          '#type' => 'checkbox',
          '#default_value' => $category_selected,
        ];
      }
      unset($category_selected_list);
    }
    // Pager to be display on the form.
    $form['pager'] = array(
      '#type' => 'pager'
    );

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('ai_collaborate.tribes_categories'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    // Save the data
    parent::saveData($values['tribes_assets']);
    $form_state->setRedirect('ai_collaborate.tribes_categories');
  }
}