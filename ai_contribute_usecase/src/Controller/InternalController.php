<?php

namespace Drupal\ai_contribute_usecase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides route responses for the Briefcase module.
 */
class InternalController extends ControllerBase {

  /**
   * GetParentTermofchild.
   */
  public function getInternalcontroller($parent_term = NULL) {
    $parents = [];
    $parents = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($parent_term);
    if (!empty($parents)) {
      if (count($parents > 1)) {
        foreach ($parents as $parent) {
          $parent_names .= $parent->getName();
          $parent_ids .= $parent->id();
        }
      }
      else {
        $parent_names = $parents[0]->getName();
        $parent_ids = $parents[0]->id();
      }
    }
    else {
      $parent_ids = $parent_term;
    }
    return new JsonResponse($parent_ids);
  }

}
