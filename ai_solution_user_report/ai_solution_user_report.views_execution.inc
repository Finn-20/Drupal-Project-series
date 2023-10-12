<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

/**
 * Implement hook_views_query_alter();
 */
function ai_solution_user_report_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if (($view->id() == 'solution_view' || $view->id() == 'as') && $view->current_display == 'page_1') {
    $query_string = \Drupal::request()->query->all();
    // Handle the display of the assert tracker report unique visitor count sorting
    // feature by adding the unique distinct user who had viewed that content.
    if ($query_string['order'] == 'unique_visits') {
      $query->orderby[0]['field'] = 'nodeviewcount_uid';
      $query->orderby[0]['direction'] = $query_string['sort'];
    }
  }
}
