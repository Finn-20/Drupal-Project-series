<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

function ai_myfeed_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {

  // Adding the custom filter conditions for Primary Industry, Primary Domain,
  // UseCase AI Feature values as per the logged user preference settings.
  if ($view->id() == 'search_content_2_0' && $view->current_display == 'my_feed_search') {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    $query1 = db_select('ai_myfeed_content', 'f');
    $query1->fields('f', ['myfeed_termdata'])->condition('f.myfeed_uid', $uid, '=');
    $results = $query1->execute()->fetchAll();
    $termvalues = !empty($results) ? json_decode($results[0]->myfeed_termdata, true) : [];
    $condition_group = &$query->getwhere();
    $cnt = count($condition_group);

    $include_archive = \Drupal::request()->query->get('include-archive');
    if (!empty($include_archive)) {
      foreach ($condition_group[$cnt]['conditions'] as &$condition) {
        if ($condition[0] == 'moderation_state') {
          $condition = ['moderation_state', ['published', 'archived'], 'IN'];
        }
      }
    }
    $cnt++;
    if (!empty($termvalues[1])) {
      $condition_group[$cnt]['conditions'][] = ['field_primary_industry', $termvalues[1], 'IN'];
    }
    else {
      $condition_group[$cnt]['conditions'][] = ['field_primary_industry', [0], 'IN'];
    }
    if (!empty($termvalues[2])) {
      $condition_group[$cnt]['conditions'][] = ['field_primary_domain', $termvalues[2], 'IN'];
    }
    else {
      $condition_group[$cnt]['conditions'][] = ['field_primary_domain', [0], 'IN'];
    }
    if (!empty($termvalues[3])) {
      if (count($termvalues[3]) <= 1) {
        $condition_group[$cnt]['conditions'][] = ['field_usecase_aifeatures', key($termvalues[3]), '='];
      }
      else {
        $condition_group[$cnt]['conditions'][] = ['field_usecase_aifeatures', $termvalues[3], 'IN'];
      }
    }
    else {
      $condition_group[$cnt]['conditions'][] = ['field_usecase_aifeatures', [0], 'IN'];
    }
    $condition_group[$cnt]['args'] = [];
    $condition_group[$cnt]['type'] = 'OR';
    $query->getwhere($condition_group);
  }
}
