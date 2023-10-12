<?php

/**
 * @file
 * Views integration for ai_solution_user_report module.
 */

/**
 * Implements hook_views_data().
 */
function ai_solution_user_report_views_data() {
  $data = [];
  $data['solution_view_report']['table']['group'] = t('Solution user custom view');
  $data['solution_view_report']['table']['base'] = [
    'field' => 'record_id',
    'title' => t('solution view report'),
    'help'  => t('Contains data about user and hits report.'),
  ];
  $data['solution_view_report']['table']['join'] = [
    // #global is a special flag which allows a table to appear all the time.
    '#global' => [],
  ];
  // Year field.
  $data['solution_view_report']['year_name'] = [
    'title'  => t('Year'),
    'help'   => t('The year that the user logged in at.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  // The Month name field.
  $data['solution_view_report']['month_name'] = [
    'title'  => t('Month Name'),
    'help'   => t('The month name that the user logged in at.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
  // The ont_time field.
  $data['solution_view_report']['new_logins'] = [
    'title'  => t('New Login'),
    'help'   => t('New users added in the month.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];
  // The user_agent field.
  $data['solution_view_report']['total_users'] = [
    'title'  => t('Total Users'),
    'help'   => t('Total users month wise.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];
  // The user_agent field.
  $data['solution_view_report']['new_solutions'] = [
    'title'  => t('New Solutions'),
    'help'   => t('New Solutions added in the month.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];
  // The user_agent field.
  $data['solution_view_report']['total_solutions'] = [
    'title'  => t('Total Solutions'),
    'help'   => t('Total Solutions month wise.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];
  // The user_agent field.
  $data['solution_view_report']['avg_session'] = [
    'title'  => t('Average Session'),
    'help'   => t('Average session month wise.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];
  // The user_agent field.
  $data['solution_view_report']['total_visits'] = [
    'title'  => t('Total visits'),
    'help'   => t('Total visits month wise.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
  ];

  return $data;
}
