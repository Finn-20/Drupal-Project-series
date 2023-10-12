<?php

namespace Drupal\ai_related_content\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\facets\Entity\Facet;
use Drupal\facets\Entity\FacetSource;

/**
 * Provides site matrix data.
 *
 * @Block(
 *   id = "ai_site_matrix_data_block",
 *   admin_label = @Translation("AI site matrix data"),
 * )
 */
class AIGoogleMatrixDataBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#theme'] = 'site_matrix_data';
    $build['#cache'] = ['max-age' => 0];

    $config = \Drupal::config('site_matrix_config_admin.settings');

    if ($config->get('display_google_matrix')) {
      // Google analystics data.
      $view_name = 'google_matrix_data';
      $view = \Drupal\views\Views::getView($view_name);
      $view->setArguments(array());
      $view->execute();
      foreach ($view->result as $result) {
        
      }
        $build['#google']['footfall'] = $result->pageviews;
        $build['#google']['footfall_text'] = $config->get('footfall_text');
        $build['#google']['users'] = $result->newUsers;
        $build['#google']['users_text'] = $config->get('user_text');
    }

    // Data related to the total no. of assets on the sites.
    if ($config->get('display_asset_matrix')) {
      if (!$config->get('display_asset_default_value')) {
        $view_browse_all = \Drupal\views\Views::getView('search_content_2_0');
        $view_browse_all->setDisplay('browse_all_total_count');
        $view_browse_all->get_total_rows = TRUE;
        $view_browse_all->execute();
        $asset_count = $view_browse_all->total_rows;
        $build['#asset']['count'] = $asset_count;
      }
      else {
        $build['#asset']['count'] = $config->get('asset_default_display_value');
      }
      $build['#asset']['asset_text'] = $config->get('asset_text');
    }
    // Data related to the total no. of livedemo on the sites.
    if ($config->get('display_live_demo_matrix')) {
      if ($config->get('display_livedemo_default_value')) {
        $build['#livedemo']['count'] = $config->get('live_demo_default_display_value');
      }
      else {
        $build['#livedemo']['count'] = $config->get('live_demo_default_display_value');
      }
      $build['#livedemo']['livedemo_text'] = $config->get('live_demo_text');
    }

    // Data related to total no. of stakeholder connect on the sites.
    if ($config->get('display_stakeholder_matrix')) {
      if ($config->get('display_stakeholder_default_value')) {
        $build['#stakeholderconnect']['count'] = $config->get('stakeholder_connect_default_display_value');;
      }
      else {
        $build['#stakeholderconnect']['count'] = $config->get('stakeholder_connect_default_display_value');
      }
      $build['#stakeholderconnect']['stakeholder_text'] = $config->get('stakeholder_text');
    }
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
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * Cache maxage.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}