<?php

namespace Drupal\ai_content_feedback\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Get the route you want to alter.
    $route = $collection->get('content_feedback_add.form');
    $feedback_title = \Drupal::config('content_feedback.settings')->get('feedback_title');
    if ($route) {
      if ($route) {
        $route->setDefaults([
          '_title' => $feedback_title,
          '_form' => '\Drupal\content_feedback\Form\AddContentFeedback',
        ]);
      }

    }
  }

}
