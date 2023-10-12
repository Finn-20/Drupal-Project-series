<?php

namespace Drupal\ai_popup_introduction\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * ModalFormExampleController class.
 */
class HomeVideoCarouselController extends ControllerBase {

  /**
   * Callback for opening the modal form.
   */
  public function videoCarouselPopup($id) {
    $response = new AjaxResponse();
    // Get the video url on the basis of id.
      $media = Media::load($id);
      $fid = $media->get('field_media_video_file')->target_id;
      $file = File::load($fid);
      $url = file_create_url($file->getFileUri());
    $video = [
      '#theme' => 'video-carousel-display',
      '#video' => ['url' => $url],
    ];
    $options = [
      'dialogClass' => 'popup-dialog-class',
      'width' => '80%',
      'height' => '80%', 
    ];

    $rendered_video = \Drupal::service('renderer')->render($video);
    // Add an AJAX command to open a modal dialog with the video as the content.
    $response->addCommand(new OpenModalDialogCommand(NULL, $rendered_video, $options));

    return $response;
  }

}