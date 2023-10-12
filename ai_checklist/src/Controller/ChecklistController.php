<?php

namespace Drupal\ai_checklist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ai_checklist\AiChecklistStorage;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Provides route responses for the Example module.
 */
class ChecklistController extends ControllerBase {

  /**
   *
   */
  public function view(Node $node) {

    $node_title = $node->get('title')->value;
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

    if ($node->bundle() != 'use_case_or_accelerator') {
      return [
        '#theme' => 'ai_checklist_page',
        '#questions_with_category' => [],
        '#checklist_form' => [],
        '#node_view_link' => $url,
        '#node_title' => (strlen($node_title) > 80) ? substr($node_title, 0, 50) . ' ...' : $node_title,
        '#message' => 'Checklist is only use for Use Case or Accelerator content type.',
        '#attached' => ['library' => ['ai_checklist/ai_checklist_page']],
      ];
    }

    $current_userid = \Drupal::currentUser()->id();

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

    $checklist_form = \Drupal::formBuilder()->getForm('Drupal\ai_checklist\Form\Checklist\ChecklistForm', $node->id());
    $questions_with_category = AiChecklistStorage::loadAllQuestionsWithCategories();
    $all_answers = AiChecklistStorage::loadAllSubmittedComments($node->id());

    $author_answers = [];
    $reviewer_answers = [];

    foreach ($all_answers as $answers) {
      foreach ($answers as $key => $value) {
        if ($key == 'timestamp') {
          $data['formatted_date'] = date('d M', $value);
        }
        elseif ($key == 'uid') {
          // Pass your uid.
          $submitter = User::load($value);
          $data['submitted_by'] = $submitter->getUsername();
        }

        if ($key == 'checklist_answer') {
          $value = nl2br($value);
        }
        $data[$key] = $value;
      }
      $is_author = in_array($answers->uid, $contributors) ? TRUE : FALSE;

      foreach ($questions_with_category as $category_id => $question_category) {
        if (isset($question_category['sub_category'][$data['sub_category_id']])) {
          if ($is_author) {
            $questions_with_category[$category_id]['sub_category'][$answers->sub_category_id]['author_answers'][] = $data;
          }
          else {
            $questions_with_category[$category_id]['sub_category'][$answers->sub_category_id]['reviewer_answers'][] = $data;
          }
        }
      }
    }

    $build = [
      '#theme' => 'ai_checklist_page',
      '#questions_with_category' => $questions_with_category,
      '#checklist_form' => $checklist_form,
      '#node_view_link' => $url,
      '#node_title' => (strlen($node_title) > 80) ? substr($node_title, 0, 50) . ' ...' : $node_title,
      '#attached' => ['library' => ['ai_checklist/ai_checklist_page']],
    ];

    return $build;

  }

  /**
   * Delete category controller.
   */
  public function deleteCategory($first) {
    $result = AiChecklistStorage::delete('category_id', $first, 'ai_checklist_category');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the category.'));
    }
    return $this->redirect('ai_checklist.category');
  }

  /**
   * Delete sub category controller.
   */
  public function deleteSubCategory($first) {
    $result = AiChecklistStorage::delete('sub_category_id', $first, 'ai_checklist_subcategory');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the sub category.'));
    }
    return $this->redirect('ai_checklist.sub_category');
  }

  /**
   * Delete sub category controller.
   */
  public function deleteQuestion($first) {
    $result = AiChecklistStorage::delete('question_id', $first, 'ai_checklist_questions');
    if ($result) {
      drupal_set_message($this->t('Successfully deleted the question.'));
    }
    return $this->redirect('ai_checklist.questions');
  }

}
