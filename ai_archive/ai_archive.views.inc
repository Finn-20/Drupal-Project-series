<?php

/**
 * @file
 * Views integration for ai_archive module.
 */

/**
 * Implements hook_views_data().
 */
 
function ai_archive_views_data() {
	$data = [];
	_ai_archive_asset_log($data);
	return $data;
}


/**
* Exposing the ai_user_daily_login table
*/
function _ai_archive_asset_log(&$data) {
	$data['ai_archive_log']['table']['group'] = t('Asset archive log');
	$data['ai_archive_log']['table']['base'] = [
		'title' => t('Asset archive log view'),
		'help'  => t('Asset archive log view.'),
	];
	$data['ai_archive_log']['table']['join'] = [
		// #global is a special flag which allows a table to appear all the time.
		'#global' => [],
	];
  $data['ai_archive_log']['nid'] = array(
    'title' => t('node ID'),
    'help' => t('Node Id.'),
    'relationship' => array(
      'base' => 'node_field_data',
      'base field' => 'nid',
      'id' => 'standard',
      'label' => t('Asset id'),
    ),
  );
  $data['ai_archive_log']['uid'] = array(
    'title' => t('User ID'),
    'help' => t('who has created log.'),
    'relationship' => array(
      'base' => 'users_field_data',
      'base field' => 'uid',
      'id' => 'standard',
      'label' => t('Asset log user'),
    ),
  );
  $data['ai_archive_log']['timestamp'] = [
    'title'  => t('created Date'),
    'help'   => t('When asset log has created.'),
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
  
  $data['ai_archive_log']['status'] = [
    'title'  => t('Asset Status'),
    'help'   => t('Asset Status.'),
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
}
