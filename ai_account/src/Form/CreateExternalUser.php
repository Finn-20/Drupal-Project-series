<?php

namespace Drupal\ai_account\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;
use Drupal\user\UserAuthInterface;
use Drupal\user\UserInterface;
use Drupal\user\UserStorageInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class CreateExternalUser extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_acccount_externaluser_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

  $form['create_externaluser'] = [
  '#type' => 'vertical_tabs',
  '#title' => t('External User Form'),
  ];
  $form['create_externaluser_fieldset'] = array(
  '#type' => 'details',
  '#title' => t('External User Form'),
  '#group' => 'ai_externaluser',
  '#open' => TRUE,
  );

  $form['create_externaluser_fieldset']['external_firstname'] = [
    '#type' => 'textfield',
    '#title' => t('First Name'),
    '#default_value' =>'',
    '#required' => TRUE,
    '#maxlength' => 180,
  ];
  $form['create_externaluser_fieldset']['external_lastname'] = [
    '#type' => 'textfield',
    '#title' => t('Last Name'),
    '#default_value' =>'',
    '#required' => TRUE,
    '#maxlength' => 180,
  ];
  $form['create_externaluser_fieldset']['external_email'] = [
    '#type' => 'email',
    '#title' => t('Email ID:'),
    '#default_value' =>'',
    '#required' => TRUE,
    '#maxlength' => 180,
  ];
  $profilegrp = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('profile_group');
  $profgrpterms = array();

  foreach ($profilegrp as $profilegrps) {
  $profgrpterms[$profilegrps->tid] = $profilegrps->name;
  }

  $profgrparr = array_slice($profgrpterms, 1, NULL, true);

  $form['create_externaluser_fieldset']['external_profilegrp'] = [
    '#type' => 'radios',
    '#title' => t('Profile Group'),
    '#options' =>$profgrparr,
    '#required' => TRUE,
    '#maxlength' => 180,
  ];
  $form['create_externaluser_fieldset']['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Create User'),
  ];

  return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
   
    $user_by_email = user_load_by_mail($values['external_email']);
    $user_exists = FALSE;
    if ($user_by_email) {
      $user_exists = TRUE;
    }
    if(!$user_exists) {
   // New User Save & Login.
      $user = User::create();
      $password = user_password(10);

      $user->setUsername($values['external_email']);
      $user->setPassword($password);
      $user->setEmail($values['external_email']);
      $user->addRole('external_user');
      $user->set('field_first_name', $values['external_firstname']);
      $user->set('field_las', $values['external_lastname']);
      $user->set('field_profile_group', $values['external_profilegrp']);
      $user->enforceIsNew();

     $userRequest = $user->save();
	$newid = $user->id();
	$account = User::load($newid);
	$op = 'register_admin_created';
	$langcode =  \Drupal::languageManager()->getCurrentLanguage()->getId();
	// Send an email.
	$mail =   _user_mail_notify($op, $account); 
		if (empty($mail)) { print "Sdf";die;
			drupal_set_message('Unable to send email. Contact the site administrator if the problem persists.');
		}
		else { 
		drupal_set_message(t("Password reset instructions mailed to @email.",['@email' =>$account->getEmail()]));		
		}   
    }
  }

}
