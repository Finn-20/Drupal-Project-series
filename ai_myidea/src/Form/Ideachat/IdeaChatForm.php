<?php

namespace Drupal\ai_myidea\Form\Ideachat;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ai_myidea\AiChatStorage;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\Cache;
use Drupal\taxonomy\Entity\Term;

/**
 * Form to add a AI Category.
 */
class IdeaChatForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_ideachat_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$node=NULL) {
    $nid = $node;
	$node = Node::load($nid);
	//print_r($node);die;
	$idea_title  = $node->title->value;
	$idea_desc   = substr($node->get('body')->value, 0, 100) . '...';
	$idea_author = $node->get('field_author')->getValue();
    $idea_contib_id = $node->get('field_idea_contributor_id')->getValue();
    $ind_tid = $node->field_industry_idea->target_id;
	$dom_tid = $node->field_domain_idea->target_id;
	$reg_tid = $node->field_region_idea->target_id;
	$nodeuid = $node->getOwnerId();
	//industry
	$indus_query = \Drupal::database()->select('industry_owner_details','indusowner')
			->fields('indusowner', ['industry_author'])
			->condition('industry_terms',$ind_tid, '=');
	$select_user_rows[] = $indus_query->execute()->fetchCol(0);
	
	//domain
	$domain_query = \Drupal::database()->select('domain_owner_details','domainowner')
		  ->fields('domainowner', ['domain_author'])
		  ->condition('domain_terms',$dom_tid, '=');
	$select_user_rows[] = $domain_query->execute()->fetchCol(0);
	
	//region
	$region_query = \Drupal::database()->select('region_owner_details','regionowner')
		  ->fields('regionowner', ['region_author'])
		  ->condition('region_terms',$reg_tid, '=');
	$select_user_rows[] = $region_query->execute()->fetchCol(0);
	
	$current_userid = \Drupal::currentUser()->id();
	
	//print_r($select_user_rows); die;
    $is_author = 1;
    $element = 'answer';
	foreach($select_user_rows as $key =>$value) {
		foreach($value as $key1 =>  $value1) {
			if($value1 ==  $current_userid) {
				//echo 'found ' . $value1;
				$is_author = 0;
				$element = 'review';
				break;
			}
		}
	}	
	//	$owner_ids[]= $ownerid;
	
	
		
    $form['ref_nid'] = [
      '#type' => 'value',
      '#value' => $nid,
    ];

    $form['uid'] = [
      '#type' => 'value',
      '#value' => $current_userid,
    ];

    
	
    $form['is_author'] = [
      '#type' => 'value',
      '#value' => $is_author,
    ];
	
	
    $default_values = [];
    if ($is_author) {
      $saved_answers_by_author = AiChatStorage::loadAllSavedComments($nid);
	//print_r($saved_answers_by_author);
      if (!empty($saved_answers_by_author)) {
        foreach ($saved_answers_by_author as $saved_answers) {
          $default_values[$saved_answers->answer_chat_id] = ['saved_ans_id' => $saved_answers->answer_chat_id, 'saved_answer' => $saved_answers->chat_answer];
        }
      }
   } 

    $form['saved_answer'] = [
      '#type' => 'value',
      '#value' => $default_values,
    ];
	
if ($nodeuid == $current_userid) { 
	if($element == 'answer'){
	$moderation_state =  $node->get('moderation_state')->getString();
	if($moderation_state != 'idea_closed' && $moderation_state != 'idea_accept'){
		$form[$element . '_chatans'] = [
		  '#type' => 'textarea',
		  '#default_value' => isset($default_values[$subcategory_id]['saved_answer']) ? $default_values[$subcategory_id]['saved_answer'] : '',
		  '#rows' => 1,
		];
	}
	}
}
	if($element == 'review'){
	$moderation_state =  $node->get('moderation_state')->getString();
	if($moderation_state != 'idea_closed' && $moderation_state != 'idea_accept'){

	  $form[$element . '_chatans'] = [
	  '#type' => 'textarea',
	  '#default_value' => '',
	  '#rows' => 1,
	  ];
	}
	}

    /**/

	$moderation_state =  $node->get('moderation_state')->getString();
	
	if($moderation_state != 'idea_closed' && $moderation_state != 'idea_accept'){
		  $form['actions']['submit'] = [
			'#type' => 'submit',
			'#value' => 'Submit comment',
			'#weight' => '-10',
			'#atributes' => ['class' => ['submit_for_review']],
		  ];
		
	  if($element == 'review'){
		   $form['actions']['accpidea'] = [
			'#type' => 'submit',
			'#value' => 'Accept Idea',
			'#weight' => '-9',
			'#atributes' => array('class' => 'my_class'),
		  ];
		  $form['actions']['save'] = [
			'#type' => 'submit',
			'#value' => 'Close the idea',
			'#weight' => '-9',
			'#atributes' => array('class' => 'my_class'),
		  ];
		  }
	}
	if ($moderation_state == 'idea_closed' ) {
            $form['no_submit_info']['#markup'] = '<div class="disabled_desc">** Idea is CLOSED !!</div>';
     }
	if ($moderation_state == 'idea_accept'){
		$form['no_submit_info']['#markup'] = '<div class="disabled_desc">** Idea is Accepted no further modifications!!</div>';
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
    $is_author = $values['is_author'];
    $element = $is_author ? 'answer' : 'review';
	$values['chat_ans'] = $values[$element .'_chatans'];
    $op = $values['op']; 
	
	$status = (($is_author) && ($op == 'Submit for Review Process')) ? '1' : '1';
	//print $is_author.$status;die;
    $notify = FALSE;
	$saved_answer = $values['saved_answer'];
    $is_new = (!isset($saved_answer) || empty($saved_answer));
    $update_nodeaccess = FALSE;
    $accept_ownerid = \Drupal::currentUser()->id();
    $ref_nid = $values['ref_nid'];
    $uid = $values['uid'];
    $time = \Drupal::time()->getCurrentTime();
	$data = [];
	
	if($op == 'Submit comment'){
		$submission = [
		  'chat_answer' => $values['chat_ans'],
		  'chat_answer_mode' => $element,
          'ref_nid' => $ref_nid,
          'uid' => $uid,
          'timestamp' => $time,
          'status' => $status,
        ];
		//if(!$is_new){
		if (!empty($submission)) { 
			AiChatStorage::insert($submission, 'ai_chat_answers');
		}
		//}
	}
	
	 if (isset($ref_nid) && !empty($ref_nid)) {
      // Load the node values.
      $latest_revision = \Drupal::entityTypeManager()->getStorage('node')
        ->getQuery()
        ->latestRevision()
        ->condition('nid', $ref_nid)
        ->execute();

      if (isset($latest_revision) && !empty($latest_revision)) {
        $latest_revision_id = array_keys($latest_revision)[0];
        $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($latest_revision_id);
      }
      else {
        $node = Node::load($ref_nid);
      }

	 $url = Url::fromUserInput('/node/' . $ref_nid . '/ideachat');
	 
	$moderation_state = $node->get('moderation_state')->getString();
	//print "sdf=".$op."==".$moderation_state.$is_author; die;
      if ($is_author) {
		  if ($op == 'Submit comment') {
			  // Update checklist submitted value to true.
          $node->set('field_idea_checklist_submitted', 1);
		  $node->set('status', 1);
          // If moderation state draft, change to needs_review and save the node.
			if ($moderation_state == 'idea_submitted') { 
				$node->set('moderation_state', 'idea_review');
				$node->set('status', 1);
				$node->set('field_idea_checklist_submitted', 1);
			}
			if ($moderation_state == 'update_idea') { 
				$node->set('moderation_state', 'idea_review');
				$node->set('status', 1);
				$node->set('field_idea_checklist_submitted', 1);
			}
          $message = t('Chat submissions has been submitted for review Successfully.');
          $notify = TRUE;
		  }
	    }
		if ($is_author == 0) {
		
	    if ($op == 'Submit comment') {
			 $node->set('field_idea_checklist_submitted', 1);
		  $node->set('status', 1);
          // If moderation state draft, change to needs_review and save the node.
			if ($moderation_state == 'idea_submitted') { 
				$node->set('moderation_state', 'idea_review');
				$node->set('status', 1);
				$node->set('field_idea_checklist_submitted', 1);
			}
          $message = t('Chat submissions has been submitted for review Successfully.');
          $notify = TRUE;
		}
		
		if ($op == 'Close the idea') {
		  if ($moderation_state == 'idea_submitted' || $moderation_state == 'idea_review') {
            $node->set('moderation_state', 'idea_closed');
           // $node->set('status', 1);
            $node->set('field_idea_checklist_submitted', 1);
          }
          $url = Url::fromUserInput('/node/' . $ref_nid);
          $message = t('Idea Discussion End successfully !!');
          $notify = TRUE;
		  }
		  else {
          $notify = TRUE;
          $message = t('Your comments have been submitted successfully.');
         }
		 if($op == 'Accept Idea') {
			$node->set('moderation_state', 'idea_accept');
            $node->set('status', 1);
			$node->set('field_accept_owner_id', $accept_ownerid);
            $node->set('field_idea_checklist_submitted', 1);
			$message = t('Idea Accepted Successfully.');
		 }
	  }
	  $node->save();
	//  $message =  t('Your comments have been submitted successfully.');
	  // TODO: Send email to reviewer.
      if ($notify) {
		$node = Node::load($ref_nid);
        $node_contrib = $node->get('field_author')->getValue();		
		$current_reviwerid = \Drupal::currentUser()->id();
		if($current_reviwerid != $node_contrib ){ print "dsfsdf".$current_reviwerid;
        $node_path_alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $ref_nid);
        $chatlist_data = [
          'url' => \Drupal::request()->getSchemeAndHttpHost() . '/node/' . $ref_nid . '/ideachat',
          'usecase-url' => \Drupal::request()->getSchemeAndHttpHost() . $node_path_alias,
        ];
        $params = ['node_contrib'=>$node_contrib, 'checklist' => $chatlist_data, 'node' => $node];

        /* if ($op == 'Submit comment') {
          $key = 'ai_myidea_reviwer_submission';
        } */
        
        $this->_ai_chatlistreview_notify($key, $params);
      }
	  }
	 // Set message and redirection.
      drupal_set_message($message);
      $form_state->setRedirectUrl($url);
	 }

  }

/** Revieweer comment **/
	public function _ai_chatlistreview_notify($key, $params = []) {
		$mailManager = \Drupal::service('plugin.manager.mail');
		$module = 'ai_myidea';
		$langcode = \Drupal::currentUser()->getPreferredLangcode();
		$send = TRUE;
		$reciever = [];
		$current_userid = \Drupal::currentUser()->id();
		$from = User::load($current_userid)->get('mail')->value;
		$reciever[] = $from;
		$contributors = [];
		if (isset($params['node']) && !empty($params['node'])) {
		 // $contributors = $this->get_contributors_mail($params['node']);
		  $author = $params['node']->getOwner()->getEmail();
		  if (!in_array($author, $reciever)) {
			$reciever[] = $author;
		  }
		}
		if (is_array($reciever)) {
		$to = implode(',', $reciever);
		}
	//	 Print "key=".$key."=to=".$to."=langcode=".$langcode."=from=".$from."=send=".$send; print "<pre>"; die;
       $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);

    if ($result['result'] != TRUE) {
      $message = t('There was a problem sending your email notification to @email.', ['@email' => $to]);
      \Drupal::logger('mail-log')->error($message);
      return;
    }

    $message = t('An email notification has been sent to @email ', ['@email' => $to]);
    \Drupal::logger('mail-log')->notice($message);
	}
}