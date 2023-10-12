<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;

/**
 * Implements hook_views_data_alter().
 *
 * Alter DB tables defined via hook_views_data().
 */
function user_notification_views_data_alter(array &$data) {
  $data['user_notification']['table']['group'] = t('User Notification');
  $data['user_notification']['table']['base'] = [
    'field' => 'entity_id',
    'title' => t('User nofitication count'),
    'help' => t('This table is containing the data about user notification count of the node.'),
    'weight' => -10,
  ];

  $data['user_notification']['table']['join'] = [
    'node_field_data' => [
      'left_field' => 'nid',
      'field' => 'entity_id',
    ],
  ];
  $data['user_notification']['id'] = [
    'title' => t('ID'),
    'field' => [
      'title' => t('Notification ID'),
      'help' => t('Notification id'),
      'id' => 'numeric',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];
  $data['user_notification']['operation'] = [
    'title' => t('Notification operation'),
    'field' => [
      'title' => t('Operation'),
      'help' => t('Notification operation.'),
      'id' => 'standard',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'id' => 'standard',
    ],    
    'argument' => [
      'id' => 'standard',
    ],    

  ];
  $data['user_notification']['created'] = [
    'title' => t('Created'),
    'help' => t('When the notification operation was carried out.'),
    'field' => [
      'id' => 'date',
      'click sortable' => TRUE,
    ],
    'filter' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
  ];

  return $data;
}

