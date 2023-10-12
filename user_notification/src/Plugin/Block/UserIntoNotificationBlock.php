<?php

namespace Drupal\user_notification\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\user_notification\Services\NotificationUtil;
use Drupal\node\NodeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\ai_content_notifications\Services\AIContentNotificationService;
use Drupal\user_notification\UserNotificationStorage;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "user_into_notification_block",
 *   admin_label = @Translation("User Into Notification Block"),
 * )
 */
class UserIntoNotificationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $operation = AIContentNotificationService::getNotificationOperationTypes();
    $uid = \Drupal::currentUser()->id();
    $session = \Drupal::request()->getSession();
    $last_access_time = $session->get('last_access_uid_' . $uid, 0);

    $high_notification_level_set = FALSE;
    $notification_level_feature = $notification_type = NULL;
    $notification_level_config = \Drupal::configFactory()->getEditable('ai_content_notifications.settings');

    if (!$last_access_time) {
      $last_access_time = \Drupal::currentUser()->getLastAccessedTime();
      $session->set('last_access_uid_' . $uid, $last_access_time);
    }
    // 1. Get all latest notification which are not user specific
    // i.e my idea,push notification n published usecase_and_accelerator
    //use_case_or_accelerator,asset,push_notification_description,my_idea
    $database = \Drupal::database();
    $query = $database->select('user_notification','u');
    $query->fields('u');
    $query->condition('created', $last_access_time, '>')->condition('status', 1);
    $query->condition('entity_type', ['my_idea','push_notification_description','use_case_or_accelerator'],'IN');
    $query->condition('operation',$operation,'NOT IN');
    $result = $query->orderBy('created', 'DESC')->execute()->fetchAll();
    $render_array = [];
    $new_notification_count = $nid = 0;
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $nid = $node->id();
    }

    if (isset($result) && !empty($result)) {
      foreach ($result as $content) {
        if (isset($content) && !empty($content)) {
          $content_id = $content->id;
          if (NULL == $content->entity_id)
            continue;

          $notification_title = $content->title;
          $asset_title_type = $notification_level_config->get('newly_added_asset');
          $idea_title_type = $notification_level_config->get('newly_added_idea');
          $content_link = Url::fromRoute('entity.node.canonical', ['node' => $content->entity_id])->toString();

          if ($content->entity_type == 'push_notification_description') {
            $node_state = Node::load($content->entity_id);
            if (NULL == $node_state)
            continue;

            $time = \Drupal::time()->getCurrentTime();
            $pub_enddate = $node_state->get('field_publish_end_date')->value;
            $dateTime = new DrupalDateTime($pub_enddate, 'UTC');
            $end_timestamp = $dateTime->getTimestamp();
            if ($time > $end_timestamp)
              continue;
            $content_link = '/push_notification';
          }

          if ($content->entity_type == 'use_case_or_accelerator'){
            $notification_level_feature = 'newly_added';
            $notification_type = 'newly_added';
            $title_type = $asset_title_type;
          }
          if ($content->entity_type == 'my_idea'){
            $notification_level_feature = 'newly_added';
            $notification_type = 'newly_added';
            $title_type = $idea_title_type;
          }
          if ($content->entity_id == $nid) {
            $render_array[$content_id]['visited_class'] = 'visited';
          }else {
            $render_array[$content_id]['visited_class'] = 'notvisited';
            $new_notification_count++;
          }
          
          $render_array[$content_id]['title'] = $notification_title.'('.$title_type.')';
          $render_array[$content_id]['notification_type'] = $notification_type;
          $render_array[$content_id]['id'] = $content->entity_id;
          $render_array[$content_id]['created_date'] = $content->created;
          
          $render_array[$content_id]['created'] = \Drupal::service('date.formatter')->formatTimeDiffSince($content->created);
          $render_array[$content_id]['content_link'] = $content_link;
        }
      }
    }
    //\Drupal::logger("query")->notice($query->__toString());

    // 2. Get all latest notification which are user specific
    $this->_get_login_user_interaction_notification($render_array,$new_notification_count,$uid,$operation,$high_notification_level_set);

    // Sorting the array on the basis of created date.
    $created_date = array_column($render_array, 'created_date');
    array_multisort($created_date, SORT_DESC, $render_array);  

    if(!empty($render_array)){
      // get d latest notification type to display colour code
      $notification_level_feature = $render_array[0]['notification_type'];
    }
    // Forcefully set the notification level to be high, if any of the
    // notification for the user has update action alert set, it will take precendence over all other notification.
    $notification_level_feature = empty($high_notification_level_set) ? $notification_level_feature : 'update_action_alert';

    $notification_feature = $notification_level_config->get('notification_features');
    $notification_class = $notification_level_config->get('notification_css_class');
    // Setting up the notification class to be displayed on the site.
    $notification_level_class = !empty($notification_feature[$notification_level_feature]) ?
      $notification_feature[$notification_level_feature] : 'no-notify-class';

       
    $renderable = [
      '#theme' => 'notification_view',
      '#notifications' => $render_array,
      '#notification_count' => $new_notification_count,
      '#notification_level' => $notification_level_class,
      '#attached' => [
        'library' => ['user_notification/drupal.user_notification.toolbar'],
      ],
    ];
    $default_theme = \Drupal::configFactory()->getEditable('system.theme')->get('default');
    if ($default_theme == 'aitheme') {
      $rendered = (string) \Drupal::service('renderer')->render($renderable);
      print $rendered;
    }
    else {

      return $renderable;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    
  }

  public function getCacheMaxAge() {
    return 0;
  }
  

  public function _get_login_user_interaction_notification(&$result_notification,&$new_notification_count,$uid,$operation,$high_notification_level_set) {

    $database = \Drupal::database();
    // 1. Get only user related  notification
    $query = $database->select('user_notification','u');
    $query->fields('u');
    $query->condition('status', 1)->condition('operation',$operation,'IN');
    $query->condition('uid',$uid,'=');
    $result = $query->orderBy('created', 'DESC')->execute();
    
    //\Drupal::logger("query")->notice($query->__toString());
    if (isset($result) && !empty($result)) {

      $notification_level_config = \Drupal::configFactory()->getEditable('ai_content_notifications.settings');

      $comment_notification_title = $notification_level_config->get('comment_notification');
      $interact_notification_title = $notification_level_config->get('interact_notification');
      $subscriber_notification_title = $notification_level_config->get('subscriber');
      $inreview_notification_title = $notification_level_config->get('inreview_asset_notification');
      $draft_notification_title = $notification_level_config->get('draft_asset_notification');
      $rating_notification_title = $notification_level_config->get('rating_notification');
      
      foreach ($result as $content_id => $content) {
        if (!isset($result_notification[$content_id]) || empty($result_notification[$content_id])) {
          $new_notification_count++;
          switch ($content->operation) {
            case 'Comment': 
              $result_notification[$content_id]['title'] = $content->title.'('.$comment_notification_title.')';
              $result_notification[$content_id]['notification_type'] = 'communication';
              break;
            
            case 'Email': 
              $result_notification[$content_id]['title'] = $content->title.'('.$interact_notification_title.')';
              $result_notification[$content_id]['notification_type'] = 'interact_marketing';
              break;
            
            case 'Subscriber': 
              $result_notification[$content_id]['title'] = $content->title.'('.$subscriber_notification_title.')';
              $result_notification[$content_id]['notification_type'] = 'subscriber';
              break;
            case 'Rating': 
              $result_notification[$content_id]['title'] = $content->title.'('.$rating_notification_title.')';
              $result_notification[$content_id]['notification_type'] = 'interact_marketing';
              break;

            case 'draft-asset-opt' || 'draft-asset-opt-non-aa': 
              $notification_level_feature = 'update_action_alert';
              $high_notification_level_set = TRUE;
              $node_state = Node::load($content->entity_id);

              if (empty($notification_level_config->get('check_to_notify_non_asset_author_user_list')) && $uid != $node_state->get('uid')->getString()) {
                continue 2;
              }
              $title_type = AIContentNotificationService::getAssetNotificationTitle($node_state->get('uid')->getString(),$draft_notification_title);

              $result_notification[$content_id]['title'] = $content->title.'('.$title_type.')';
              $result_notification[$content_id]['notification_type'] = 'update_action_alert';
              break;

            case 'pending-review-asset-opt' || 'pending-review-asset-opt-non-aa': 
              $notification_level_feature = 'update_action_alert';
              $high_notification_level_set = TRUE;

              $node_state = Node::load($content->entity_id);

              if (empty($notification_level_config->get('check_to_notify_non_asset_author_user_list')) && $uid != $node_state->get('uid')->getString()) {
                continue 2;
              }
              $title_type = AIContentNotificationService::getAssetNotificationTitle($node_state->get('uid')->getString(),$inreview_notification_title);

              $result_notification[$content_id]['title'] = $content->title.'('.$title_type.')';
              $result_notification[$content_id]['notification_type'] = 'update_action_alert';
              break;
          }
          $result_notification[$content_id]['visited_class'] = 'notvisited';
          $result_notification[$content_id]['id'] = $content->entity_id;
          $result_notification[$content_id]['created_date'] = $content->created;
          $result_notification[$content_id]['created'] = \Drupal::service('date.formatter')->formatTimeDiffSince($content->created);
          $result_notification[$content_id]['content_link'] = Url::fromRoute('entity.node.canonical', ['node' => $result_notification[$content_id]['id']])->toString();
        }
      }
    }
  }
}
