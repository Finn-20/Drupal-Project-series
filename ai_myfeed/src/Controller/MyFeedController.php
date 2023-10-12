<?php

namespace Drupal\ai_myfeed\Controller;

use Drupal\Core\Controller\ControllerBase;
//use Drupal\ai_myidea\AiChatStorage;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;
use Drupal\Component\Utility\Unicode;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Core\Asset\file_create_url;
use Drupal\views\Form\ViewsForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\votingapi\Entity\Vote;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Provides route responses for the Example module.
 */
class MyFeedController extends ControllerBase {
  
  public function ai_my_feed_search_modal_form() {
	  $response = new AjaxResponse();
        $modal_form['wrapper'] = [
            '#type' => 'container',
            '#prefix' => '<div class="popups-container"><div class="modal-scroll">',
            '#suffix' => '</div></div>',
        ];
 
        // Get the modal form using the form builder.
        $formBuilder = \Drupal::formBuilder();
        $modal_form['wrapper']['form'] = $formBuilder->getForm('Drupal\ai_myfeed\Form\AIMyfeedSearchForm');
 
        // Add an AJAX command to open a modal dialog with the form as the content.
        $response->addCommand(new InvokeCommand('body', 'addClass', array('modal-pop-up')));
        $response->addCommand(new InvokeCommand('.ui-dialog', 'addClass', array('modal-default modal-animate')));
        $response->addCommand(new InvokeCommand('#drupal-modal', 'addClass', array('ctools-modal-content modal-forms-modal-content')));
        $response->addCommand(new OpenModalDialogCommand('', $modal_form, ['width' => '1020']));
 
        return $response;
  }
}
