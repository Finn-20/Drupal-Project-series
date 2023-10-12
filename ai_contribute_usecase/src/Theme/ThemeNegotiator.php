<?php

namespace Drupal\ai_contribute_usecase\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Config\ConfigFactory;

/**
 * ThemeNegotiator.
 */
class ThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Service constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Configfactory.
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $route = $route_match->getRouteObject();

    if (!$route instanceof Route) {
      return FALSE;
    }
    $routes_to_match = [
      'node.add',
      'entity.node.edit_form',
      'entity.node.delete_form',
      'view.user_report.page_1',
    ];
    return in_array($route_match->getRouteName(), $routes_to_match);
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    $applicable_bundles = [
      'use_case_or_accelerator',
      'briefcase',
      'simplenews_issue',
	  'my_idea',
	  'business_opportunity',
      'case_study',
	  'collaborate_tribes'
    ];
    $switch_theme = FALSE;

    switch ($route_match->getRouteName()) {
      case 'node.add':
        if ($entity_type_param = $route_match->getParameter('node_type')) {
          if (in_array($entity_type_param->id(), $applicable_bundles)) {
            $switch_theme = TRUE;
          }
        }
        break;

      case 'entity.node.edit_form':
        if ($entity_param = $route_match->getParameter('node')) {
          if (in_array($entity_param->bundle(), $applicable_bundles)) {
            $switch_theme = TRUE;
          }
        }
        break;

      case 'entity.node.delete_form':
        if ($entity_param = $route_match->getParameter('node')) {
          if (in_array($entity_param->bundle(), $applicable_bundles)) {
            $switch_theme = TRUE;
          }
        }
        break;

      case 'view.user_report.page_1':

        $switch_theme = TRUE;

        break;
    }

    if ($switch_theme) {
      // Return the machine name of the front-end theme.
      return $this->configFactory->get('system.theme')->get('default');
    }

    // Not applicable.
    return NULL;
  }

}
