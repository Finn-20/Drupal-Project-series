<?php
namespace Drupal\ai_featured_usecase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\flag\Entity\Flagging;
use Drupal\ai_briefcase\Services\AiBriefcaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides route responses for the Briefcase module.
 */
class AIFeaturedUsecaseController extends ControllerBase {
  public function modifyFeaturedUsecase($nid = NULL, $value = 'add') {
    $node = Node::load($nid);
    $existing_featured_value = $node->get('field_featured_usecase')->getValue()[0]['value'];
    $target_featured_value = ($value == 'add') ? 1 : 0;
    if ($existing_featured_value != $target_featured_value) {
      $node->set('field_featured_usecase', $target_featured_value);
      $node->save();
    }
    return [
      '#type' => 'markup',
      '#markup' => 'Done !!'
    ]; 
  }
}