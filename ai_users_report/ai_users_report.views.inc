<?php

/**
 * @file
 * Views integration for ai_users_report module.
 */

/**
 * Implements hook_views_data().
 */
 
function ai_users_report_views_data() {
	$data = [];
	_ai_users_report_month_report($data);
	_ai_users_report_daily_report($data);
	return $data;
}

/**
* Exposing the ai_users_report table
*/
function _ai_users_report_month_report(&$data) {
	$data['ai_user_month_report']['table']['group'] = t('Monthly user report view');
	$data['ai_user_month_report']['table']['base'] = [
		'field' => 'urid',
		'title' => t('Monthly user report view'),
		'help'  => t('Monthly user report view.'),
	];
	$data['ai_user_month_report']['table']['join'] = [
		// #global is a special flag which allows a table to appear all the time.
		'#global' => [],
	];
  
  $data['ai_user_month_report']['year'] = [
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
  
  $data['ai_user_month_report']['month'] = [
    'title'  => t('Month Name'),
    'help'   => t('The month name that the user logged in at.'),
    'field' => array(
      'id' => 'users_report',
      'click sortable' => TRUE,
    ),
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
  ];
 
  
  $data['ai_user_month_report']['unique_visit'] = [
    'title'  => t('Unique visit/Registration'),
    'help'   => t('Unique visit/Registration added in the month.'),
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
  
  $data['ai_user_month_report']['all_visit'] = [
    'title'  => t('No of visits to AIG in a month'),
    'help'   => t('No of visits to AIG in a month.'),
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
  
  $data['ai_user_month_report']['cumulative_unique_visit'] = [
    'title'  => t('No of cumulative unique visits/registration to AIG (old + new)'),
    'help'   => t('No of cumulative unique visits/registration to AIG (old + new).'),
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
  
  $data['ai_user_month_report']['cumulative_visit'] = [
    'title'  => t('No of cumulative visits to AIG (old + new)'),
    'help'   => t('No of cumulative visits to AIG (old + new).'),
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
}

/**
* Exposing the ai_user_daily_login table
*/
function _ai_users_report_daily_report(&$data) {
	$data['ai_user_daily_login']['table']['group'] = t('Daily user login view');
	$data['ai_user_daily_login']['table']['base'] = [
		'field' => 'urid',
		'title' => t('Daily user login report view'),
		'help'  => t('Daily user  loginreport view.'),
	];
	$data['ai_user_daily_login']['table']['join'] = [
		// #global is a special flag which allows a table to appear all the time.
		'#global' => [],
	];
  $data['ai_user_daily_login']['uid'] = array(
    'title' => t('User ID'),
    'help' => t('Login user ID.'),
    'relationship' => array(
      'base' => 'users_field_data',
      'base field' => 'uid',
      'id' => 'standard',
      'label' => t('Login user'),
    ),
  );
  $data['ai_user_daily_login']['login_date'] = [
    'title'  => t('Uer login Date'),
    'help'   => t('The date that the user logged in at.'),
    'field'  => [
      'id' => 'date',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'date',
    ],
  ];
  
  $data['ai_user_daily_login']['year'] = [
    'title'  => t('Year'),
    'help'   => t('The year that the user logged in at.'),
    'field'  => [
      'id' => 'standard',
    ],
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => array(
      'id' => 'numeric',
    ),
  ];
  $data['ai_user_daily_login']['month'] = [
    'title'  => t('Month Name'),
    'help'   => t('The month name that the user logged in at.'),
    'field' => array(
      'id' => 'users_report',
      'click sortable' => TRUE,
    ),
    'sort'   => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => array(
      'id' => 'numeric',
    ),
  ];
  
 
}
