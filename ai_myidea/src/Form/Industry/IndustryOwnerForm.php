<?php
namespace Drupal\ai_myidea\Form\Industry;
use Drupal\ai_myidea\AiChatStorage;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\Cache;
use Drupal\taxonomy\Entity\Term;
use Drupal\Component\Utility\Unicode;

/**
* Defines a form that configures module settings.
*/
class IndustryOwnerForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'industry_owner_form';
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
   
	// This is the field Group fieldset.
    $form['ideaowner_industry_fieldset'] = array(
        '#type' => 'details',
        '#title' => t('Idea Industry Owner Settings'),
        '#group' => 'ai_ideaownersele',
        '#open' => TRUE,
    );
	$industry = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('industries');
	$industryterms = array();
	foreach ($industry as $industrys) {
		$industryterms[$industrys->tid] = $industrys->name;
	}
    
	$form['ideaowner_industry_fieldset']['industry_terms'] = array(
		'#type' => 'select',
		'#options' => $industryterms,
		'#title' => t('Industry'),
	);
	$form['ideaowner_industry_fieldset']['industry_authoruid'] = [
		'#type' => 'entity_autocomplete',
		'#target_type' => 'user',
		'#selection_settings' => ['include_anonymous' => FALSE],
		'#title' => t('Industry Owners'),
	];
	$form['ideaowner_industry_fieldset']['actions']['save'] = [
        '#type' => 'submit',
        '#value' => 'Save',
        '#weight' => '-10',
        '#atributes' => ['class' => ['save_edit_draft']],
      ];
	$existing_industrys = AiChatStorage::loadAll('industry_owner_details');
	$industrys = [];
    $header = [
      'industry_term' => 'Industry Name',
      'owner_name' => 'Owner Name',
	  'owner_user_name' => 'User Name',
      'action' => 'Actions',
    ];

    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      // '#rows' => $rows,
      '#empty' => 'There is no Industry Owner added yet.',
      '#tableselect' => FALSE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-order-weight',
        ],
      ],
    ];

    foreach ($existing_industrys as $industrys) {
		
      $id = $industrys->industry_owner_id ;
      // Some table columns containing raw markup.
	  $term_name = \Drupal\taxonomy\Entity\Term::load($industrys->industry_terms)->get('name')->value;
      $form['table'][$id]['industry_term'] = [
        '#plain_text' => $term_name,
      ];
	$user_account = User::load($industrys->industry_author);
	$display_name = $user_account->getUsername();
	$firstName = (NULL != $user_account->field_first_name->value) ? Unicode::ucfirst(Unicode::strtolower($user_account->field_first_name->value)) : '';
	$lastName = (NULL != $user_account->field_las->value) ? Unicode::ucfirst(Unicode::strtolower($user_account->field_las->value)) : '';
	if ((NULL != $firstName) && (NULL != $lastName)) {
		$name = t('@last_name, @first_name', [
		  '@first_name' => $firstName,
		  '@last_name' => $lastName,
		]
		);
	}

      $form['table'][$id]['owner_name'] = [
        '#plain_text' => $firstName.','.$lastName,
      ];
      $form['table'][$id]['owner_user_name'] = [
        '#plain_text' => $display_name,
      ];
      // Operations (dropbutton) column.
      $form['table'][$id]['operations'] = [
        '#type' => 'operations',
        '#links' => [],
      ];

     
      $form['table'][$id]['operations']['#links']['delete'] = [
        'title' => t('Delete'),
        'url' => Url::fromUserInput('/admin/config/content/industry_owner/' . $industrys->industry_owner_id . '/delete'),
      ];
      $categories[$id] =  $term_name;
    }
	
	
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
	 
       $entry = [
        'industry_terms' => $values['industry_terms'],
        'industry_author' => $values['industry_authoruid'],
      ]; 
      // Save the submitted entry.
      // $return = AiChatStorage::update($entry, 'industry_owner_details','');
	 $return = AiChatStorage::insert($entry, 'industry_owner_details');
    if ($return) {
      drupal_set_message('Industry Owner details has been added Successfully.');
      $url = Url::fromRoute('ai_myidea.industryowner');
      $form_state->setRedirectUrl($url);
    }
    else {
      drupal_set_message('Error while updating.', 'error');
    }

  }
}
