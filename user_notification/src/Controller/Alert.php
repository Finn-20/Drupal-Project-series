<?php

namespace Drupal\user_notification\Controller;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Url;

/**
 * Class Alert.
 */
class Alert extends ControllerBase {

  /**
   * View Notification Popup.
   */
  public function viewPopup() {

    $result = \Drupal::entityTypeManager()->getStorage('user_notification')->loadMultiple();
	
    $render_array = [];
	 
    //$render_push_array = ['testig'];
    foreach ($result as $content_id => $content) {
	 //if($content->bundle() == 'use_case_or_accelerator' && $content->get('moderation_state')->getString() == 'published'){
      // \Drupal::logger("user_notification")->notice($content->get('uid')->value);.
      $render_array[$content_id]['title'] = $content->get('title')->value;
      $render_array[$content_id]['id'] = $content->get('entity_id')->value;
      $render_array[$content_id]['type'] = $content->get('entity_type')->value;
      $render_array[$content_id]['status'] = $content->get('status')->value;
      $render_array[$content_id]['operation'] = $content->get('operation')->value;
      $render_array[$content_id]['uid'] = $content->get('uid')->value;
      $render_array[$content_id]['created'] = \Drupal::service('date.formatter')->formatTimeDiffSince($content->get('created')->value);

      $account = User::load($render_array[$content_id]['uid']);
	 
     // $render_array[$content_id]['username'] = $account->name->getValue()[0][value];
$render_array[$content_id]['username'] = "shjf";
      $user_link = 'internal:/user/' . $render_array[$content_id]['uid'];
      $render_array[$content_id]['user_link'] = Url::fromUri($user_link)->toString();

      $render_array[$content_id]['content_link'] = Url::fromRoute('entity.node.canonical', ['node' => $render_array[$content_id]['id']])->toString();
	// }
	 
    }
//print_r($render_array);die;
    $renderable = [
      '#theme' => 'notification_view',
      '#notifications' => $render_array,
	 // '#notifications_push' => $render_push_array,
    ];
    $rendered = (string) \Drupal::service('renderer')->render($renderable);

    $options = [
      'dialogClass' => 'popup-dialog-class',
      'width' => '25%',
      'draggable' => FALSE,
      'appendTo' => '.user-notification-toolbar-tab.toolbar-tab',
      'resizable' => FALSE,
      'position' => [
        'my' => 'right top+40',
        'at' => 'right top',
        'of' => '.user-notification-toolbar-tab.toolbar-tab',
      ],
    ];
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand(t('Notifications'), $rendered, $options));

    return $response;
  }

}
