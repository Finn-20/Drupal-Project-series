<?php

namespace Drupal\ai_popup_introduction\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Provides a homepage banner video block.
 *
 * @Block(
 *   id = "ai_popup_introduction_homepage_banner_video",
 *   admin_label = @Translation("Home page Banner Video Block"),
 * )
 */
class HomePageVideoBannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'homepage_video_banner_display';
    $build['#attached']['library'][] = 'ai_popup_introduction/ai_popup_introduction.css';
	$build['#attached']['library'][] = 'ai_popup_introduction/ai_popup_introduction.js';

    // Rendering the homepage carousel view to display it on the homepage section.
    $view = \Drupal\views\Views::getView('homepage_carousel');
    if (empty($view)) {
      return True;	  
    }
    $view->execute();
    foreach ($view->result as $result) {
      if (!empty($result->_entity->get('field_video_intro_body')->getValue()[0]['value'])) {
        $banner_body = $result->_entity->get('field_video_intro_body')->getValue()[0]['value'];
      }
      if (!empty($result->_entity->get('field_video_intro_title')->getValue()[0]['value'])) {
        $banner_title = $result->_entity->get('field_video_intro_title')->getValue()[0]['value'];
      }
	  if (!empty($result->_entity->get('field_video_intro_banner_image')->getValue()[0]['target_id'])) {
        $banner_image = $result->_entity->get('field_video_intro_banner_image')->getValue()[0]['target_id'];
      }
	  if (!empty($result->_entity->get('field_introduction_video')->getValue()[0]['target_id'])) {
        $banner_video = $result->_entity->get('field_introduction_video')->getValue()[0]['target_id'];
      }
	 $slider_content = $view->render();
	}
	
    $build['#banner']['label'] = !empty($banner_title) ? $banner_title : NULL;
    $build['#banner']['body'] = !empty($banner_body) ? $banner_body : NULL;
	$build['#banner']['slider_images'] = !empty($image_url) ? $image_url : NULL;
	$build['#banner']['video'] = !empty($url) ? $url : NULL;
    $build['#banner']['slider_content'] = !empty($slider_content) ? $slider_content : NULL; 
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