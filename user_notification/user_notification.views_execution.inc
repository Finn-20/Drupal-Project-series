<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

/**
 * Implement hook_views_query_alter();
 */
function user_notification_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if ($view->id() == 'new_User_notification') {

    $operation_condition = [
      'field' => 'user_notification.operation',
      'value' => ['Create', 'Update'],
      'operator' => 'NOT IN',
    ];
    $query->where['AND']['conditions'][1] = $operation_condition;

    $current_user = $view->getUser();
    $currentUserId = $current_user->id();
    $user_condition = [
      'field' => 'user_notification.uid',
      'value' => $currentUserId,
      'operator' => '=',
    ];
    $query->where['AND']['conditions'][2] = $user_condition;
  }
}
