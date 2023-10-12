<?php

/**
 * @file
 * Views integration for the Login Details module.
 */

/**
 * Implements hook_views_data().
 */
function ai_login_details_views_data() {
  $data = [];
  $data['login_details']['table']['group'] = t('User logins');
  $data['login_details']['table']['base'] = [
    'field' => 'record_id',
    'title' => t('User logins'),
    'help'  => t('Contains data about user logins.'),
  ];
  $data['login_details']['table']['join'] = [
    'users_field_data' => [
      'left_field' => 'uid',
      'field'      => 'uid',
    ],
  ];
  // The UID field.
  $data['login_details']['record_id'] = [
    'title' => t('Login record ID'),
    'help'  => t("The unique ID referring to this login record."),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'title' => t('Record ID'),
      'id' => 'numeric',
    ],
  ];
  $data['login_details']['uid'] = [
    'title' => t('UID'),
    'help'  => t("The user's UID."),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'title' => t('UID'),
      'id' => 'numeric',
    ],
    'relationship' => [
      'title'              => t('User who logged in'),
      'help'               => t('The user associated with the login record.'),
      'id'                 => 'standard',
      'base'               => 'users_field_data',
      'base field'         => 'uid',
      'field'              => 'uid',
      'label'              => t('User who logged in'),
    ],
  ];
  // The login timestamp field.
  $data['login_details']['login_timestamp'] = [
    'title'  => t('Login timestamp'),
    'help'   => t('The timestamp that the user logged in at.'),
    'field'  => [
      'id' => 'date',
    ],
    'sort'   => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
  ];
  // The hostname field.
  $data['login_details']['hostname'] = [
    'title'  => t('Host Name'),
    'help'   => t('The hostname that the user logged in at.'),
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
  $data['login_details']['one_time'] = [
    'title'  => t('One time'),
    'help'   => t('The one time that the user logged in at.'),
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
  $data['login_details']['user_agent'] = [
    'title'  => t('User agent'),
    'help'   => t('The user agent that the user logged in at.'),
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

  return $data;
}
