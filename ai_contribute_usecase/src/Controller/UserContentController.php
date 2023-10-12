<?php

namespace Drupal\ai_contribute_usecase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Provides route responses for the Example module.
 */
class UserContentController extends ControllerBase {

  /**
   *
   */
  public function internalExternalPreview(Node $node,$view_flag = NULL) {
	$vars = $filearray = array();
	$have_demonstration = $node->get('field_have_demonstration')->value;
	$have_demo_video = $node->get('field_demo_video')->value;
	$have_usecase_video = $node->get('field_have_video_usecase')->value;
	if(strtolower($view_flag) == "external") {
		
		_aigallery_demonstration_details($node,$vars,true);
		$vars['business_driver_d'] = $node->field_business_driver_duplicate->value;
		$vars['solution_data'] = $node->field_solution_duplicate->value;
		/** requirement has been changed and based on the global check 
		business driver and solutions are mandatory for external people
		// 4. check user selected external business drive as yes
		$business_driver_check = $node->get('field_business_driver_check')->value;
		if(strtolower($business_driver_check) == 'yes') {
			$vars['business_driver_d'] = $node->field_business_driver_duplicate->value;
		}
		// 5. check user selected external solution as yes
		$solution_check = $node->get('field_s')->value;
		if(strtolower($solution_check) == 'yes') {
			$vars['solution_data'] = $node->field_solution_duplicate->value;
		}
		*/
		// 6. check user selected external demostration details.
		$demostration_check = $node->get('field_live_demo_env_check')->value;
		if((strtolower($demostration_check) == 'yes') && (strtolower($have_demonstration) == 'yes')) {
			_aigallery_demonstration_details($node,$vars,true);
		}
		// 7. check user selected external demo video
		$video_check = $node->get('field_demo_video_check')->value;
		if((strtolower($video_check) == 'yes') && (strtolower($have_demo_video) == 'yes')) {
			$demo_video_link = isset($node->get('field_demo_video_modified_')->target_id) ? $node->get('field_demo_video_modified_')->target_id : '';
			if (!empty($demo_video_link)) {
			  $vars['demo_video'] = $node->get('field_demo_video_modified_')->target_id;
			  _aigallery_demo_video($node,$vars,true);
			}
		}
		// 8. check user selected external use case
		// 8.1 display use case video only demo video is no
		$usecase_video_check = $node->get('field_usecase_video_check')->value;
		if((strtolower($have_demo_video) == 'no') && (strtolower($usecase_video_check) == 'yes') && (strtolower($have_usecase_video) == 'yes')) {
			$usecase_video_link = isset($node->get('field_usecase_video_modified_')->target_id) ? $node->get('field_usecase_video_modified_')->target_id : '';
			if (!empty($usecase_video_link)) {
				$vars['usecase_link'] = $node->get('field_usecase_video_modified_')->target_id;
			}
		}
		// 8.3 show  external use case video link in download only 
		if((strtolower($have_demo_video) == 'yes') && (strtolower($usecase_video_check) == 'yes') && (strtolower($have_usecase_video) == 'yes')) {
			_aigallery_usecase_video($node,$vars,$filearray,true);
		}
	}else if(strtolower($view_flag) == "internal") {
		_aigallery_show_default_internal_field($node,$vars,$filearray);
		if(strtolower($have_demo_video) == 'yes') {
			_aigallery_demo_video($node,$vars);
		}
	}
	// Get Author and Contributor names.
	$uid = $node->getOwnerId();
	$author_contributor = [];
	if (!is_null($uid)) {
		$author_name = _aigallery_get_user_formatted_name($uid);
		$vars['author_name'] = $author_name;
		$author_contributor[$uid] = $author_name;
	}
    $node_title = $node->get('title')->value;
    if (NULL != $node->get('field_other_contributors')->getValue()) {
        foreach ($node->get('field_other_contributors')->getValue() as $contributor) {
          if (isset($contributor['target_id']) && !empty($contributor['target_id'])) {
            if (!isset($author_contributor[$contributor['target_id']]) || empty($author_contributor[$contributor['target_id']])) {
              $author_contributor[$contributor['target_id']] = _aigallery_get_user_formatted_name($contributor['target_id']);
            }
          }
        }
    }
	$author_contributor_name = '';
	if (count($author_contributor) > 2) {
		$first = TRUE;
		$count = 0;
		foreach ($author_contributor as $value) {
			if ($first) {
				$author_contributor_name .= trim($value);
				$first = FALSE;
			}else {
				$author_contributor_name .= ', ' . trim($value);
			}
			$count++;
			if ($count > 1) {
				break;
			}
		}
		$author_contributor_name = trim($author_contributor_name) . ' + ' . (count($author_contributor) - 2) . ' more';
	}else {
		$author_contributor_name = implode(', ', $author_contributor);
    }
	// Get Node statistics (number of views).
	$statistics = statistics_get($node->id());
    $node_stats = \Drupal::translation()->formatPlural($statistics['totalcount'], '1', '@count');
	// Get Comments Count.
    $comment_count = $node->get('comment')->comment_count;
    $comment_title = \Drupal::translation()->formatPlural($comment_count, '1 Comment', '@count Comments');

	foreach ($node->field_attachments as $attachment) {
        $media_exists = _ai_media_file_details($attachment->getValue()['target_id'],TRUE);
		if($media_exists) {
			$filearray[] = $media_exists;
		}
      }
	
    $contact_owner_email = '';
	$contact_owner_name = '';
	$owner_linkedin = '';

	if (isset($node->field_use_case_primary_owner_ema->value) && !empty($node->field_use_case_primary_owner_ema->value)
	&& valid_email_address($node->field_use_case_primary_owner_ema->value) && isset($node->field_use_case_primary_owner_nam->value) && !empty($node->field_use_case_primary_owner_nam->value)) {
		$contact_owner_email = $node->field_use_case_primary_owner_ema->value;
		$contact_owner_name = $node->field_use_case_primary_owner_nam->value;
	}
	elseif (isset($node->field_usecase_secn_owner_email->value) && !empty($node->field_usecase_secn_owner_email->value)
	&&  valid_email_address($node->field_usecase_secn_owner_email->value) && isset($node->field_usecase_secn_owner_name->value) && !empty($node->field_usecase_secn_owner_name->value)) {
		$contact_owner_email = $node->field_usecase_secn_owner_email->value;
		$contact_owner_name = $node->field_usecase_secn_owner_name->value;
	}
	
	if (isset($node->field_linked_url->uri) && !empty($node->field_linked_url->uri)) {
		$owner_linkedin = $node->field_linked_url->uri;
	}
	elseif (isset($node->field_linkedin_sec_url->uri) && !empty($node->field_linkedin_sec_url->uri)) {
		$owner_linkedin = $node->field_linkedin_sec_url->uri;
	}

	if (empty($contact_owner_email)) {
		$contact_owner_email = $node->getOwner()->getEmail();
	}

	if (empty($contact_owner_name)) {
		$contact_owner_name = _aigallery_get_user_formatted_name($node->getOwnerId());
	}
	$usecase_tags = _aigallery_get_usecase_tags($node);
    $build = [
      '#theme' => 'ai_usecase_accelerator_page',
	  '#usecase_or_accelerator' => strtolower($node->get('field_usecase_or_accelerator')->getValue()[0]['value']) == 'accelerator'?'Accelerator':'Usecase',
	  '#author_name' => $vars['author_name'],
	  '#author_contributor_name' => $author_contributor_name,
	  '#business_driver_d' => $vars['business_driver_d'],
	  '#solution_data' => $vars['solution_data'],
	  '#demo_video' => $vars['demo_video'],
	  '#usecase_link' => $vars['usecase_link'],
	  '#content_field_rate' => $node->get('field_rate')->getValue(),
	  '#node_stats' => $node_stats,
	  '#comment_count' => $comment_count,
	  '#comment_title' => $comment_title,
	  '#filearray' => $filearray,
	  '#contact_owner_email' => $contact_owner_email,
	  '#contact_owner_name' => $contact_owner_name,
	  '#owner_linkedin' => $owner_linkedin,
	  '#usecase_tags' => $usecase_tags,
      '#node_title' => (strlen($node_title) > 80) ? substr($node_title, 0, 50) . ' ...' : $node_title,
	  '#have_demonstration' => $vars['have_demonstration'],
	  '#demo_script_filesrc' => $vars['demo_script_filesrc'],
	  '#demo_script_filename' => $vars['demo_script_filename'],
	  '#associated_image' => $vars['associated_image'],
    ];
    return $build;
  }


}
