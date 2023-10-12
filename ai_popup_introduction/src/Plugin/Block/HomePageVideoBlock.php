<?php

namespace Drupal\ai_popup_introduction\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "ai_popup_introduction_homepage_video",
 *   admin_label = @Translation("Home page Video Block"),
 * )
 */
class HomePageVideoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'homepage_video_display';
    $build['#cache'] = ['max-age' => 0];
    $view_name = 'introduction_video_view';
    $view = \Drupal\views\Views::getView($view_name);
    $view->setArguments(array());
    $view->execute();
	$video_id = 0;
    foreach ($view->result as $result) {
      $video_id = $result->_entity->get('field_introduction_video')->getValue()[0]['target_id'];
    }
    // Getting the video id for the introduction popup video.
	if (!empty($video_id)) {
      // Getting the video id for the introduction popup video.
      $media = Media::load($video_id);
      $fid = $media->get('field_media_video_file')->target_id;
      $file = File::load($fid);
      $url = file_create_url($file->getFileUri());
    }
    $build['#video'] = !empty($url) ? $url : NULL;

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
