<?php

/**
 * @file
 * Current end date is not taking so we have alrred the query.
 */

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

/**
 * Implements hook_views_query_alter().
 */
function ai_login_details_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {

  if ($view->id() == 'user_login_details') {
    foreach ($query->where as &$condition_group) {
      foreach ($condition_group['conditions'] as &$condition) {
        if (strpos($condition['field'], 'BETWEEN') !== FALSE) {
          $date_string = $condition['field'];
          $date_exp_array = explode(' AND ', $date_string);
          $date_exp_array[count($date_exp_array) - 1] = end($date_exp_array) + (24 * 60 * 60);
          $date_string = implode(' AND ', $date_exp_array);
          $condition['field'] = $date_string;
        }

      }

    }
  }
}
