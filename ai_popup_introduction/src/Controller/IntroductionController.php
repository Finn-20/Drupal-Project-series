<?php

namespace Drupal\ai_popup_introduction\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\node\Entity\Node;
/**
 * Provides route responses for the Example module.
 */
class IntroductionController extends ControllerBase {
  
	public function ai_video_watch_later() {
		//return new JsonResponse(array('result' => "true"));
		// 1. get the login user id 
		$total_duration = \Drupal::request()->get('total_duration');
		$mini_time_duration = \Drupal::request()->get('mini_time_duration');
		$watched_duration = \Drupal::request()->get('watched_duration');
		$case = \Drupal::request()->get('case');
		$user = \Drupal::currentUser();
		// 2. Check user already done any operation on video
		$userquery = \Drupal::database()->select('node_field_data', 'n')
		->fields('n', ['nid'])
		//->condition('n.status', 1)
		->condition('n.type', 'user_video_details')
		->condition('unid.field_user_id_value', $user->id());
		$userquery->join('node__field_user_id', 'unid', 'unid.entity_id = n.nid');
		$user_details = $userquery->execute()->fetchAll();
		$field_video_status = 0;	
		$user_interaction = 0;
		switch($case){
			case "autoplay": 
				$user_interaction = 1;
				$field_video_status = 1;
				break;
			case "close": 
				// 2.1. Check the video duration 
				// 2.1.a) check the video has been watched as per mini time duration specified by admin
				//  If mini or completed viewed by user den make status as completed
				$user_interaction = 2;
				if($watched_duration >= $mini_time_duration ){
					$field_video_status = 1;
				}else{
					//21600 = 8hours 
					setcookie("ai_introduction_video", $user->id(), time() + (21600), "/");
				}
				break;
			case "watch_later": 
				$user_interaction = 3; 
				// 7200 = 2hrs
				setcookie("ai_introduction_video", $user->id(), time() + (7200), "/");
				break;
		}
		// 2.2. If so update the status
		if(!empty($user_details)){
			$node = Node::load($user_details[0]->nid);
			//set value for field
			$node->field_video_status->value = $field_video_status;
			$node->field_video_duration->value = $watched_duration;
			$node->field_user_interaction->value = $user_interaction;
			//save to update node
			$node->save();
		}else{
			//2.3 else Insert node if user does not exists
			$node = Node::create([
			  'type'        => 'user_video_details',
			  'title'       => $user->getDisplayName(),
			  'field_user_id' => $user->id(),
			  'field_user_mail' => $user->getEmail(),
			  'field_video_status' => $field_video_status,
			  'field_video_duration' => $watched_duration,
			  'field_user_interaction' => $user_interaction
			]);
			$node->save();
		}
		$response = new AjaxResponse();
		return new JsonResponse(array('result' => $case));
	}
}
