<?php

namespace Drupal\ai_checklist\Form\Checklist;

use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ai_checklist\AiChecklistStorage;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Cache\Cache;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;

/**
 * Form to add a AI Category.
 */
class ChecklistForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_checklist_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $nid = $node;
    $ai_checklist_settings = \Drupal::config('ai_checklist.settings');
    $add_contributor_access_users = $ai_checklist_settings->get('users_with_add_contributor_access');
    $contributor_access_users = [];
    if (NULL != $add_contributor_access_users) {
      foreach ($add_contributor_access_users as $contrib_user) {
        if (isset($contrib_user['target_id']) && !empty($contrib_user['target_id'])) {
          $contributor_access_users[$contrib_user['target_id']] = $contrib_user['target_id'];
        }
      }
    }

    $latest_revision = \Drupal::entityTypeManager()->getStorage('node')
      ->getQuery()
      ->latestRevision()
      ->condition('nid', $nid)
      ->execute();
    $options = $form['moderation_state']['widget'][0]['#options'];

    if (isset($latest_revision) && !empty($latest_revision)) {
      $latest_revision_id = array_keys($latest_revision)[0];
      $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($latest_revision_id);
    }
    else {
      $node = Node::load($ref_nid);
    }
    $moderation_state = $node->get('moderation_state')->getString();

    $form['ref_nid'] = [
      '#type' => 'value',
      '#value' => $nid,
    ];

    $current_userid = \Drupal::currentUser()->id();

    $form['uid'] = [
      '#type' => 'value',
      '#value' => $current_userid,
    ];

    $contributors = [];
    $author = $node->get('uid')->getValue();
    if (isset($author[0]['target_id']) && !empty($author[0]['target_id'])) {
      $contributors[$author[0]['target_id']] = $author[0]['target_id'];
    }

    if (NULL != $node->get('field_other_contributors')->getValue()) {
      foreach ($node->get('field_other_contributors')->getValue() as $contributor) {
        if (isset($contributor['target_id']) && !empty($contributor['target_id'])) {
          $contributors[$contributor['target_id']] = $contributor['target_id'];
        }
      }
    }
    $is_author = 0;
    $element = 'review';
    if (in_array($current_userid, $contributors)) {
      $is_author = 1;
      $element = 'answer';
    }

    $form['is_author'] = [
      '#type' => 'value',
      '#value' => $is_author,
    ];

    $questions_with_category = AiChecklistStorage::loadAllQuestionsWithCategories();
    $default_values = [];
    if ($is_author) {
      $saved_answers_by_author = AiChecklistStorage::loadAllSavedComments($nid);
      if (!empty($saved_answers_by_author)) {
        foreach ($saved_answers_by_author as $saved_answers) {
          $default_values[$saved_answers->sub_category_id] = ['saved_ans_id' => $saved_answers->answer_id, 'saved_answer' => $saved_answers->checklist_answer];
        }
      }
    }

    $form['saved_answer'] = [
      '#type' => 'value',
      '#value' => $default_values,
    ];

    foreach ($questions_with_category as $category_id => $category_details) {
      foreach ($category_details['sub_category'] as $subcategory_id => $subcategory_details) {
        $form[$element . '_' . $subcategory_id] = [
          '#type' => 'textarea',
          // '#required' => $is_author ? TRUE : FALSE,
          '#default_value' => isset($default_values[$subcategory_id]['saved_answer']) ? $default_values[$subcategory_id]['saved_answer'] : '',
          '#rows' => 1,
        ];
      }
    }
    if ($is_author) {
      $form['actions']['save'] = [
        '#type' => 'submit',
        '#value' => 'Save & Edit Draft',
        '#weight' => '-10',
        '#atributes' => ['class' => ['save_edit_draft']],
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => 'Save & Submit for Review',
        '#weight' => '-9',
        '#atributes' => ['class' => ['submit_for_review']],
      ];

      if (in_array($current_userid, $contributor_access_users) && in_array($current_userid, $contributors)) {
        $form['add_remove_contributor']['actions']['remove_as_contributor'] = [
          '#type' => 'submit',
          '#value' => 'Remove myself as contributor',
          '#weight' => '-9',
          '#attributes' => ['class' => ['submit_for_review']],
          '#prefix' => '<div class="add_contrib_desc">To remove yourself as a contributor of this use case, Please click below button.</div>',
        ];
      }
    }
    else {
      if ($this->isUserHasReviewAccess($node)) {
        $form['actions']['save'] = [
          '#type' => 'submit',
          '#value' => 'Submit for review',
          '#weight' => '-10',
          '#atributes' => ['class' => ['save_edit_draft']],
        ];
        $form['actions']['submit'] = [
          '#type' => 'submit',
          '#value' => 'Submit & Publish',
          '#weight' => '-9',
          '#attributes' => ['class' => ['submit_for_review']],
        ];
        if ($moderation_state != 'needs_review') {
          $form['actions']['submit']['#attributes']['disabled'] = 'disabled';
          $form['no_submit_info']['#markup'] = '<div class="disabled_desc">** Use case has NOT been submitted for REVIEW yet, so can\'t be published.</div>';
          if ($moderation_state == 'published') {
            $form['no_submit_info']['#markup'] = '<div class="disabled_desc">** Use case is already PUBLISHED !!</div>';
          }
        }
        if (in_array($current_userid, $contributor_access_users) && !in_array($current_userid, $contributors)) {
          $form['add_remove_contributor']['actions']['add_as_contributor'] = [
            '#type' => 'submit',
            '#value' => 'Add me as contributor',
            '#weight' => '-9',
            '#attributes' => ['class' => ['submit_for_review']],
            '#prefix' => '<div class="add_contrib_desc">You have access to add yourself as a contributor of this use case. Please click below button for the same.</div>',
          ];
        }
      }
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
    $op = $values['op'];
    $status = (($is_author) && ($op == 'Save & Edit Draft') || (in_array($op, ['Add me as contributor', 'Remove myself as contributor']))) ? '0' : '1';
    $saved_answer = $values['saved_answer'];
    $is_new = (!isset($saved_answer) || empty($saved_answer));
    $update_nodeaccess = FALSE;

    $ref_nid = $values['ref_nid'];
    $uid = $values['uid'];
    //update the user draft n review notification
    _ai_contribute_usecase_notification_update($ref_nid);
    $time = \Drupal::time()->getCurrentTime();

    $data = [];
    foreach ($values as $key => $value) {
      $split_key = explode('_', $key);
      if (count($split_key) == 2 && $split_key[0] == $element) {
        $submission = [
          'sub_category_id' => $split_key[1],
          'checklist_answer' => $value,
          'ref_nid' => $ref_nid,
          'uid' => $uid,
          'timestamp' => $time,
          'status' => $status,
        ];
        if (!$is_new && isset($saved_answer[$split_key[1]]['saved_ans_id']) && !empty($saved_answer[$split_key[1]]['saved_ans_id'])) {
          $submission['answer_id'] = $saved_answer[$split_key[1]]['saved_ans_id'];
        }
        $data[] = $submission;
      }
    }

    if (!empty($data)) {
      foreach ($data as $entry) {
        if (isset($entry['answer_id']) && !empty($entry['answer_id'])) {
          AiChecklistStorage::update($entry, 'ai_checklist_answers', 'answer_id');
        }
        else {
          if (isset($entry['checklist_answer']) && !empty($entry['checklist_answer'])) {
            AiChecklistStorage::insert($entry, 'ai_checklist_answers');
          }
        }
      }
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

      $url = Url::fromUserInput('/node/' . $ref_nid . '/checklist');
      // Get moderation state of node.
      $moderation_state = $node->get('moderation_state')->getString();
      if ($is_author) {
        if ($op == 'Remove myself as contributor') {
          $updated_contb = [];
          $other_contb = [];
          $remove_me = FALSE;
          if (NULL != $node->get('field_other_contributors')->getValue()) {
            $other_contb = $node->get('field_other_contributors')->getValue();
            foreach ($other_contb as $contb) {
              if ($contb['target_id'] != $uid) {
                $updated_contb[] = ['target_id' => $contb['target_id']];
              }
            }
          }
          $node->set('field_other_contributors', $updated_contb);
          $message = t('You have been removed as contributor Successfully.');
          $notify = FALSE;
          $update_nodeaccess = TRUE;
        }
        elseif ($op == 'Save & Submit for Review') {
          // Update checklist submitted value to true.
          $node->set('field_is_checklist_submitted', 1);
          // If moderation state draft, change to needs_review and save the node.
          if ($moderation_state == 'draft') {
            $node->set('moderation_state', 'needs_review');
          }
          $message = t('Checklist submissions has been submitted for review Successfully.');
          $queue_factory = \Drupal::service('queue');
          $queue = $queue_factory->get('mail_queue_processor');
          $node->state_changed_user = \Drupal::currentUser()->id();
          $node->mail_sending_case = 'pending_review';
          $node->key = 'ai_checklist_author_submission';
          $queue->createItem($node);
        }
        elseif ($op == 'Save & Edit Draft') {
          // Update checklist submitted value to true.
          $node->set('field_is_checklist_submitted', 0);
          $url = Url::fromUserInput('/node/' . $ref_nid . '/edit');
          $message = t('Checklist submissions has been saved successfully.');
        }
      }
      else {
        if ($op == 'Add me as contributor') {
          $other_contb = [];
          $add_me = TRUE;
          if (NULL != $node->get('field_other_contributors')->getValue()) {
            $other_contb = $node->get('field_other_contributors')->getValue();
            foreach ($other_contb as $contb) {
              if ($contb['target_id'] == $uid) {
                $add_me = FALSE;
                break;
              }
            }
          }
          if ($add_me) {
            $other_contb[] = ['target_id' => $uid];
          }
          $node->set('field_other_contributors', $other_contb);
          $message = t('You have added yourself as contributor Successfully.');
        }
        elseif ($op == 'Submit & Publish') {
          // $moderation_state = $node->get('moderation_state')->getString();
          if ($moderation_state == 'needs_review') {
            $node->set('moderation_state', 'published');
            $node->set('status', 1);
            $node->set('field_is_checklist_submitted', 1);
          }
          $url = Url::fromUserInput('/node/' . $ref_nid);
          $message = t('Content has been published successfully !!');
          $queue_factory = \Drupal::service('queue');
          $queue = $queue_factory->get('mail_queue_processor');
          $node->state_changed_user = \Drupal::currentUser()->id();
          $node->mail_sending_case = 'published';
          $queue->createItem($node);
        }
        elseif ($op == 'Submit for review') {
          $queue_factory = \Drupal::service('queue');
          $queue = $queue_factory->get('mail_queue_processor');
          $node->state_changed_user = \Drupal::currentUser()->id();
          $node->mail_sending_case = 'pending_review';
          $node->key = 'ai_checklist_reviewer_submission';
          $queue->createItem($node);
          if ($moderation_state == 'draft') {
            $node->set('moderation_state', 'needs_review');
          }
          $message = t('Your review comments has been submitted Successfully.');
        }
        else {
          $message = t('Your review comments has been submitted Successfully.');
        }
        $update_nodeaccess = TRUE;
      }
      // Save the node with updated values.
      $node->save();

      if ($update_nodeaccess) {
        $grants = [];
        // Get value of contributers userid.
        $other_contb = [];
        if (NULL != $node->get('field_other_contributors')->getValue()) {
          $other_contb = $node->get('field_other_contributors')->getValue();
        }
        foreach ($other_contb as $other_contbs) {
          $userid = $other_contbs['target_id'];
          if ($userid == NULL) {
            continue;
          }
          $realm = 'nodeaccess_uid';
          $grant = [
            'gid' => $userid,
            'realm' => $realm,
          // In grand tab the vie,edit values will be enable.
            'grant_view' => 1,
            'grant_update' => 1,
            'grant_delete' => 0,
          ];
          $grants[] = $grant;
        }
        if ($node->isPublished()) {
          $settings = \Drupal::configFactory()->get('nodeaccess.settings');
          $role_alias = $settings->get('role_alias');
          $role_map = $settings->get('role_map');
          $allowed_roles = [];
          $allowed_grants = $settings->get('grants');
          foreach ($role_alias as $id => $role) {
            if ($role['allow']) {
              $allowed_roles[] = $id;
            }
          }

          foreach ($allowed_roles as $role) {
            $realm = 'nodeaccess_rid';
            $grant = [
              'gid' => $role_map[$role],
              'realm' => $realm,
            // In grand tab the vie,edit values will be enable.
              'grant_view' => 1,
              'grant_update' => 0,
              'grant_delete' => 0,
            ];
            $grants[] = $grant;
          }
        }

        // Save role and user grants to our own table.
        \Drupal::database()->delete('nodeaccess')
          ->condition('nid', $ref_nid)
          ->execute();
        foreach ($grants as $grant) {
          $id = db_insert('nodeaccess')
            ->fields([
              'nid' => $ref_nid,
          // Userid.
              'gid' => $grant['gid'],
              'realm' => $grant['realm'],
              'grant_view' => $grant['grant_view'],
              'grant_update' => $grant['grant_update'],
              'grant_delete' => $grant['grant_delete'],
            ])
            ->execute();
        }
        \Drupal::entityTypeManager()->getAccessControlHandler('node')->writeGrants($node);
        // drupal_set_message(t('Grants saved.'));.
        $tags = ['node:' . $node->id()];
        Cache::invalidateTags($tags);
      }
      // Set message and redirection.
      drupal_set_message($message);
      $form_state->setRedirectUrl($url);
    }
  }

  /**
   *
   */
  public function isUserHasReviewAccess(Node $node) {
    $published_access = TRUE;
    $current_user = \Drupal::currentUser();
    $current_userid = $current_user->id();
    $roles = $current_user->getRoles();

    if (in_array('administrator', $roles)) {
      return TRUE;
    }

    $user_list = [];
    if (isset($node) && ($node instanceof Node)) {
      $primary_industry_tid = (NULL != $node->get('field_primary_industry')->target_id) ? $node->get('field_primary_industry')->target_id : NULL;
      $primary_domain_tid = (NULL != $node->get('field_primary_domain')->target_id) ? $node->get('field_primary_domain')->target_id : NULL;

      if (isset($primary_industry_tid) && !empty($primary_industry_tid)) {
        $primary_industry = Term::load($primary_industry_tid);
        $industry_lead_by = $primary_industry->get('field_lead_by')->getValue();
        if (isset($industry_lead_by[0]['target_id']) && !empty($industry_lead_by[0]['target_id'])) {
          $user_list[] = $industry_lead_by[0]['target_id'];
        }
      }

      if (isset($primary_domain_tid) && !empty($primary_domain_tid)) {
        $primary_domain = Term::load($primary_domain_tid);
        $domain_lead_by = $primary_domain->get('field_lead_by')->getValue();
        if (isset($domain_lead_by[0]['target_id']) && !empty($domain_lead_by[0]['target_id'])) {
          $user_list[] = $domain_lead_by[0]['target_id'];
        }
      }
    }

    if (isset($user_list) && !empty($user_list)) {
      $published_access = in_array($current_userid, $user_list);
    }

    return $published_access;
  }

}
