<?php

namespace Drupal\ai_briefcase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\flag\Entity\Flagging;
use Drupal\ai_briefcase\Services\AiBriefcaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides route responses for the Briefcase module.
 */
class BriefcaseController extends ControllerBase {
  /**
   * Aibrifcase @var Drupal\ai_briefcase\Services\AiBriefcaseService.
   */
  private $aiBriefcaseService;

  /**
   * Constructor.
   *
   * Services ai @param Drupal\ai_briefcase\Services\AiBriefcaseService.
   */
  public function __construct(AiBriefcaseService $aiBriefcaseService) {
    $this->aiBriefcaseService = $aiBriefcaseService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('ai_briefcase.aiBriefcaseService')
    );
  }

  /**
   * AddFavoriteToBriefcase function.
   */
  public function addFavoriteToBriefcase(NodeInterface $fav_node = NULL, NodeInterface $briefcase = NULL) {
    $favorites = [];
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
    $isFlagged = $this->aiBriefcaseService->isContentFlagged($fav_node->id(), $uid);
    if (!$isFlagged) {
      $flagging = Flagging::create([
        'uid' => $uid,
        'session_id' => NULL,
        'flag_id' => 'favourites',
        'entity_id' => $fav_node->id(),
        'entity_type' => 'node',
        'global' => 0,
      ]);

      $flagging->save();
    }

    $existing_favorites = $briefcase->get('field_favorites')->getValue();
    foreach ($existing_favorites as $favorite) {
      $favorites[$favorite['target_id']] = $favorite['target_id'];
    }

    if (!in_array($fav_node->id(), $favorites)) {
      $existing_favorites[] = ['target_id' => $fav_node->id()];
    }

    $briefcase->set('field_favorites', $existing_favorites);
    $briefcase->save();

    $tags = ['node:' . $briefcase->id()];
    Cache::invalidateTags($tags);

    $build = [
      '#type' => 'markup',
      '#markup' => $this->t('Added "@node" into "@briefcase"', ['@node' => $fav_node->get('title')->value, '@briefcase' => $briefcase->get('title')->value]),
    ];

    return $build;
  }

  /**
   * DeleteMyBriefcase.
   */
  public function deleteMyBriefcase(NodeInterface $briefcase = NULL) {
    $briefcase = \Drupal::entityTypeManager()->getStorage('node')->load($briefcase->id());

    // Check if node exists with the given nid.
    if (NULL != $briefcase) {
      $briefcase_title = $briefcase->get('title')->value;
      $briefcase->delete();
      $response = 'Briefcase "' . $briefcase_title . '" deleted successfully.';
    }
    else {
      $response = 'Briefcase not found!.';
    }

    return new JsonResponse($response);
  }

  /**
   * Delete favorite from briefcase.
   */
  public function deleteFavoriteFromBriefcase(NodeInterface $fav_node = NULL, NodeInterface $briefcase = NULL) {
    $favorites = [];
    $existing_favorites = $briefcase->get('field_favorites')->getValue();
    foreach ($existing_favorites as $favorite) {
      if ($favorite['target_id'] != $fav_node->id()) {
        $favorites[] = ['target_id' => $favorite['target_id']];
      }
    }

    $briefcase->set('field_favorites', $favorites);
    $briefcase->save();

    $tags = ['node:' . $briefcase->id()];
    Cache::invalidateTags($tags);

    $build = [
      '#type' => 'markup',
      '#markup' => $this->t('Removed "@node" into "@briefcase"', ['@node' => $fav_node->get('title')->value, '@briefcase' => $briefcase->get('title')->value]),
    ];

    return $build;
  }

  /**
   * ModifyFeaturedBriefcases.
   */
  public function modifyFeaturedBriefcases($briefcase = NULL) {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();

    $query = \Drupal::entityQuery('node')
    // Published or not.
      ->condition('status', 1)
    // Content type.
      ->condition('type', 'briefcase')
      ->condition('uid', $uid);

    $nids = $query->execute();

    $briefcase_nodes = [];

    foreach ($nids as $nid) {
      $target_featured_value = (isset($briefcase) && !empty($briefcase) && ($nid == $briefcase));
      $node = Node::load($nid);
      $existing_featured_value = $node->get('field_featured_briefcase')->getValue()[0]['value'];
      if ($existing_featured_value != $target_featured_value) {
        $node->set('field_featured_briefcase', $target_featured_value);
        $node->save();
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => 'Done !!',
    ];

  }

  /**
   * ModifyBriefcase.
   */
  public function modifyBriefcase() {
    // Form param.
    $briefcase_id = \Drupal::request()->request->get('briefcase_id');
    // Form param.
    $briefcase_element = \Drupal::request()->request->get('briefcase_element');
    // Form param.
    $updated_data = \Drupal::request()->request->get('briefcase_data');

    if (isset($briefcase_id) && !empty($briefcase_id)) {
      $briefcase = Node::load($briefcase_id);
      if (isset($updated_data) && !empty($updated_data)) {
        if ($briefcase_element == 'title') {
          if ($updated_data != $briefcase->get('title')->value) {
            $briefcase->set('title', $updated_data);
            $briefcase->save();
            $response = 'Briefcase title updated successfully.';
          }
          else {
            $response = 'No change in briefcase title.';
          }
        }
        elseif ($briefcase_element == 'description') {
          if ($updated_data != $briefcase->get('body')->value) {
            $briefcase->set('body', $updated_data);
            $briefcase->save();
            $response = 'Briefcase description updated successfully.';
          }
          else {
            $response = 'No change in briefcase title.';
          }
        }
        else {
          $response = 'Element not defined to update.';
        }
      }
      else {
        $response = 'Title can not be empty.';
      }
    }
    else {
      $response = 'Briefcase Id can not be null.';
    }

    return new JsonResponse($response);
  }

  /**
   * RedirectExploreallUrl.
   */
  public function redirectExploreallUrl($argument_tid = NULL) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($argument_tid);
    $voc_get = $term->getVocabularyId();
    $voc_id  = strtolower($voc_get);
    if ($voc_id == 'industries') {
      $taxonomy_fields = 'browse_all_industry_2_0';
    }
    if ($voc_id == 'domain') {
      $taxonomy_fields = 'browse_all_domain_2_0';
    }
    if ($voc_id == 'offer') {
      $taxonomy_fields = 'browse_all_offer_2_0';
    }
    if ($voc_id == 'collaborate_category') {
      $taxonomy_fields = 'browse_all_collaborate_tribes_assets';
    }
    // $generate_url  = '/ai-browse-all-search?ai-browse-all[0]='.$taxonomy_fields.':'.$argument_tid;
    // Choose a path.
    $url = Url::fromUri('internal:/ai-browse-all-search');
    $link_options = [
      'query' => [
        'ai-browse-all[0]' => $taxonomy_fields . ':' . $argument_tid,
      ],
    ];
    $url->setOptions($link_options);
    $destination = $url->toString();
    return new RedirectResponse($destination);
  }

}
