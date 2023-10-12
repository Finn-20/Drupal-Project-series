<?php

/**
 * @file
 * Contains \Drupal\ai_collaborate\Controller\userReportController.
 */

namespace Drupal\ai_collaborate\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Controller routines for user routes.
 */
class collaborateController extends ControllerBase {
	protected $database;

  public function __construct() {
    $this->database = \Drupal::database();
  }

	public function collaborate() {
		return array('markup' => 'Text here');
	}

  /**
   * Display the banner for the collaborated category.
   *
   * @param type $fid
   *   ID of the file to be displayed
   * @return object
   *   Image to be display.
   */
  public function categorycontent($id) {

    if (!empty($id) && is_numeric($id)) {
      // Retreiving the basic site configuration for the site.
      $config = \Drupal::config('ai_site_configuration.settings');

      $response = new AjaxResponse();
      $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($id);
      $content = $term->get('field_banner_category_content')->getValue();
      $response->addCommand(new HtmlCommand('#defaultbanner', $content[0]['value']));
      $category_discription = $term->get('field_category_desciption')->getValue();
      $response->addCommand(new HtmlCommand('#category_discription', $category_discription[0]['value']));
      if (!empty($config->get('display_tribes_cards'))) {
	  $card_paragraph = $term->field_leader_card->getValue();
      foreach ($card_paragraph as $element) {
        $p = \Drupal\paragraphs\Entity\Paragraph::load($element['target_id']);
        if (!empty($p->field_leader_image->getValue()[0]['target_id'])) {
          $image = $p->field_leader_image->getValue()[0]['target_id'];
          $image_media = Media::load($image);
          $image_targetid = $image_media->field_media_image->target_id;

          $image_file = File::load($image_targetid);
          $image_fileSRC = file_create_url($image_file->getFileUri());
        }
        $name = $p->field_name->getValue()[0]['value'];
        $design = $p->field_designation->getValue()[0]['value'];
        $summary = $p->field_descriptions->getValue()[0]['value'];
        $email = $p->field_email_id->getValue()[0]['value'];

        $card_details[] = ['name' => $name, 'image' => $image_fileSRC, 'design' => $design, 'summary' => $summary, 'email' => $email,];
        $cards = $card_details;
      }

      $render_element = [
        '#theme' => 'collaborate_leader_cards',
        '#items' => $cards,
      ];
      $card = \Drupal::service('renderer')->renderPlain($render_element);
      $response->addCommand(new HtmlCommand('#tribes_cards', $card));
	  }

      // Tribes asset to be displayed.
      if (!empty($config->get('display_tribes_carousel'))) {
        $append_html = '#widget_pager_bottom_tribes_asset_listing-block_1';
        $html_text = '<div class="pager_in_pager_wrapper"><span id="prev_pager"></span><span id="next_pager"></span></div>';
        $views_block = views_embed_view('tribes_asset_listing', 'block_1', $id);
        $asset_details = \Drupal::service('renderer')->render($views_block);
        if (!empty($asset_details)) {
          $response->addCommand(new HtmlCommand('#tribes_assets', $asset_details));
          $response->addCommand(new AppendCommand($append_html, $html_text));
          $response->addCommand(new InvokeCommand('.collaborate-page-category-container', 'click'));
        }
      }
      return $response;
    }
  }

  /**
   * Display the PDF for the collaborated newsletter.
   *
   * @param type $fid
   *   ID of the Pdf to be displayed
   * @return object
   *   PDF to be display.
   */
  public function newsletterPdf($fid) {
    $response = new AjaxResponse();

    $newsletter_media = Media::load($fid);

    $newsletter_id = $newsletter_media->field_media_file->getValue()[0]['target_id'];
    $newsletter_file = File::load($newsletter_id);
    $newsletter_file_url = file_create_url($newsletter_file->getFileUri());

    $response->addCommand(new InvokeCommand('#displayPDF', 'attr', array('src', $newsletter_file_url)));

    return $response;
  }

}
