<?php

namespace Drupal\ai_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block to select archive asset.
 *
 * @Block(
 *   id = "ai_search_archive_asset_display",
 *   admin_label = @Translation("Archive asset display block"),
 * )
 */
class AIArchiveAssetDisplayBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'archive_asset_display';
    $build['#cache'] = ['max-age' => 0];

    // URL query parameter.
    $query_string = \Drupal::request()->query->get('f');
    $selection_status = NULL;
    $path = '?f[0]=asset_state:archived&f[1]=asset_state:published';
    if (!empty($query_string)) {
      $path = \Drupal::service('path.current')->getPath();
      $implode_query_string = implode('|', $query_string);
      if (preg_match("/asset_state/i", $implode_query_string)) {
        $selection_status = 'checked';
        $path = $path;
      }
    }
    $build['#blk_id'] = 'archive-asset-display';
    $build['#asset_path'] = $path;
    $build['#checked_status'] = $selection_status;

    return $build;
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
    $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

  }

  /**
   * Implements getCacheMaxAge().
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
