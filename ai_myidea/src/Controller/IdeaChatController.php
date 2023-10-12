<?php

namespace Drupal\ai_myidea\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ai_myidea\AiChatStorage;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides route responses for the Example module.
 */
class IdeaChatController extends ControllerBase {

  public function view(Node $node) {

    $node_title = $node->get('title')->value;
	$idea_contib_id = $node->get('field_idea_contributor_id')->getValue();
    $ind_tid = $node->field_industry_idea->target_id;
	$dom_tid = $node->field_domain_idea->target_id;
	$reg_tid = $node->field_region_idea->target_id;
    $moderation_info = \Drupal::getContainer()->get('workbench_moderation.moderation_information');
    if ($moderation_info->hasForwardRevision($node) && $node->hasLinkTemplate('latest-version')) {
      $latest_revision = \Drupal::entityTypeManager()->getStorage('node')
        ->getQuery()
        ->latestRevision()
        ->condition('nid', $node->id())
        ->execute();
      if (isset($latest_revision) && !empty($latest_revision)) {
        $latest_revision_id = array_keys($latest_revision)[0];
        $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($latest_revision_id);
        $node_title = $node->get('title')->value;
      }
      $url = '/node/' . $node->id() . '/latest';
    }
    else {
      $url = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $node->id());
    }

   if ($node->bundle() != 'my_idea') {
      return [
        '#theme' => 'ai_ideachat_page',
        '#questions_with_category' => [],
        '#ai_ideachat_form' => [],
        '#node_view_link' => $url,
        '#node_title' => (strlen($node_title) > 80) ? substr($node_title, 0, 100) . ' ...' : $node_title,
        '#message' => 'Chat is only use for My Idea content type.',
        '#attached' => ['library' => ['ai_myidea/ai_ideachat_page']],
      ];
    } 

    $current_userid = \Drupal::currentUser()->id();

    $contributors = [];
    $author = $node->get('uid')->getValue();
	
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
	

    $is_author = 1;
    $element = 'answer';
	$contributors =[] ;
	foreach($select_user_rows as $key =>$value) {
		foreach($value as $key1 =>  $value1) {
			if($value1 ==  $current_userid) {
				$contributors[$key1] = $value1;
				$is_author = 0;
				$element = 'review';
				break;
			}
		}
	}

    $checklist_form = \Drupal::formBuilder()->getForm('Drupal\ai_myidea\Form\Ideachat\IdeaChatForm', $node->id());
	$idea_title = $node->get('title')->value;
	$ideadesc   = substr($node->get('body')->value, 0, 200) . '...';
	$ideaauthor = $node->get('field_author')->getValue();
	$questions_with_category['idea_title']= $idea_title;
	$questions_with_category['ideadesc']  = strip_tags($ideadesc);
    $questions_with_category['ideaauthor']= $ideaauthor[0]['value'];

	
	$all_answers = AiChatStorage::loadAllSubmittedComments($node->id());

    $i=0;
	foreach ($all_answers as $key => $answers) { 
		$answers_res['formatted_date'] = date('d M', $answers->timestamp);	
		$submitter = User::load($answers->uid);
		$answers_res['uid']=$answers->uid;
		$answers_res['submitted_by'] = $submitter->getUsername();
		$answers_res['chat_answer'] = nl2br($answers->chat_answer);
		$answers_res['chat_answer_mode'] = $answers->chat_answer_mode;
		
		if($answers_res['chat_answer_mode'] == 'answer') {
			$data_userans[]=$answers_res;
		}
		if($answers_res['chat_answer_mode'] == 'review') { 
			$data_reviewerans[]=$answers_res;
		}
	}
		$author_contrib = in_array($answers_res,$contributors)? TRUE : FALSE;
		$questions_with_category['reviewer_answers'] = $data_reviewerans;
		$questions_with_category['author_answers'] = $data_userans; 
	 
    $build = [
      '#theme' => 'ai_ideachat_page',
      '#questions_with_category' => $questions_with_category,
      '#ai_ideachat_form' => $checklist_form,
      '#node_view_link' => $url,
      '#node_title' => (strlen($node_title) > 80) ? substr($node_title, 0, 50) . ' ...' : $node_title,
      '#attached' => ['library' => ['ai_myidea/ai_ideachat_page']],
    ];
$i++;
    return $build;

  // }
  }
  
  public function ideamoderationlist() {
	 $nid_nid = \Drupal::entityQuery('node')
      ->condition('type', "my_idea", '=')
      ->condition('langcode', 'en', '=')
      ->condition('status', 1, '=')
      ->execute();
    $nodes_sele = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nid_nid);
    foreach ($nodes_sele as $value) {
      $title_sele[$value->nid->value] = $value->title->value;
    }
	 $header = ['Title', 'Contributor', 'Action'];
    // Add the headers.
    $data['asso_bulkimage_upload'] = [
      '#type' => 'table',
      '#title' => 'Sample Table',
      '#header' => $header,
    ];
	 return $data;
  }
  
  
  /**
   * Delete industry owners controller.
   */
  public function deleteindustry($first) {
    $result = AiChatStorage::delete('industry_owner_id', $first, 'industry_owner_details');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the Industry Owner.'));
    }
    return $this->redirect('ai_myidea.industryowner');
  }
/**
   * Delete domain controller.
   */
  public function deletedomain($first) {
    $result = AiChatStorage::delete('domain_owner_id', $first, 'domain_owner_details');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the Domain Owner.'));
    }
    return $this->redirect('ai_myidea.domainowner');
  }
  /**
   * Delete region controller.
   */
  public function deleteregion($first) {
    $result = AiChatStorage::delete('region_owner_id', $first, 'region_owner_details');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the Region Owner.'));
    }
    return $this->redirect('ai_myidea.regionowner');
  }

}
