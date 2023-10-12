<?php

namespace Drupal\ai_contribute_usecase\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Ajax\InvokeCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\ai_contribute_usecase\AiOtherFieldStorage;


class OtherFieldController extends ControllerBase {
  
  public function ai_other_field_modal_form($other_type = 0,$uniqid = 0) {
    $response = new AjaxResponse();
    $modal_form['wrapper'] = [
        '#type' => 'container',
        '#prefix' => '<div class="popups-container"><div class="modal-scroll">',
        '#suffix' => '</div></div>',
    ];
    switch ($other_type) {
        case 1:
            $other_text = 'Partner';
            break;
        case 2:
            $other_text = 'Feature';
            break;
        case 3:
            $other_text = 'Framework';
            break;
    }
    // Get the modal form using the form builder.
    $formBuilder = \Drupal::formBuilder();
    $modal_form['wrapper']['form'] = $formBuilder->getForm('Drupal\ai_contribute_usecase\Form\OtherFieldForm',$other_type,$uniqid);

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new InvokeCommand('body', 'addClass', array('modal-pop-up')));
    $response->addCommand(new InvokeCommand('.ui-dialog', 'addClass', array('modal-default modal-animate')));
    $response->addCommand(new InvokeCommand('#drupal-modal', 'addClass', array('ctools-modal-content modal-forms-modal-content')));
    $response->addCommand(new OpenModalDialogCommand('', $modal_form, ['width' => '1020']));

    return $response;
  }

  function otherFieldTag(){
    $uniqid_session = \Drupal::service('tempstore.private')->get('ai_contribute_usecase');
    switch ($_REQUEST['type']) {
        case 1:
            if($_REQUEST['checked_status'] == 'true'){
                //delete uncheck flag
                $uniqid_session->delete('partnerUncheckFlag'); 
            }else{
                $uniqid_session->set('partnerUncheckFlag', true);
            }
            break;
        case 2:
            if($_REQUEST['checked_status'] == 'true'){
                $uniqid_session->delete('ai_featureUncheckFlag');
            }else{
                $uniqid_session->set('ai_featureUncheckFlag',true);
            }
            break;
        case 3:
            if($_REQUEST['checked_status'] == 'true'){
                $uniqid_session->delete('frameworksUncheckFlag');
            }else{
                $uniqid_session->set('frameworksUncheckFlag', true);
            }
            break;
    }
    return new JsonResponse(array('status' => $_REQUEST['checked_status']));
  }
}
