<?php

namespace Drupal\ai_myidea\AccessChecks;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * A custom access check for grants form.
 */
class IdeaChatAccessCheck implements AccessInterface {

  /**
   * A custom access check.
   */
  public function access($node, AccountInterface $account) {
    if (!$node) {
      return AccessResult::forbidden();
    }
    $nid = $node;

    $latest_revision = \Drupal::entityTypeManager()->getStorage('node')
      ->getQuery()
      ->latestRevision()
      ->condition('nid', $nid)
      ->execute();

    if (isset($latest_revision) && !empty($latest_revision)) {
      $latest_revision_id = array_keys($latest_revision)[0];
      $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($latest_revision_id);
    }
    else {
      $node = Node::load($nid);
    }

    // Check if user is author of the node
    // Check if user is contributor of the node
    // Check if user has permission to view any checklist (admin or CoE role)
     if ($node instanceof Node && $node->bundle() == 'my_idea') {
      $access_list = [];
      $author = $node->get('uid')->getValue();
      $other_contributors = [];
     /*  if (NULL != $node->get('field_other_contributors')->getValue()) {
        foreach ($node->get('field_other_contributors')->getValue() as $contributor) {
          if (isset($contributor['target_id']) && !empty($contributor['target_id'])) {
            $other_contributors[$contributor['target_id']] = $contributor['target_id'];
          }
        }
      } */

      /* if ((isset($author[0]['target_id']) && !empty($author[0]['target_id']) && $author[0]['target_id'] == $account->id()) || in_array($account->id(), $other_contributors)) {
        if ($account->hasPermission('view own checklist')) {
          return AccessResult::Allowed();
        }
      } */
      /* elseif ($account->hasPermission('view any checklist')) {
        if ($this->isUserHasChecklistAccess($node)) {
          return AccessResult::Allowed();
        }
      } */
    }
	return AccessResult::Allowed();
   // return AccessResult::forbidden();
  }

  /**
   *
   */
  /* public function isUserHasChecklistAccess(Node $node) {
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
  } */

}
