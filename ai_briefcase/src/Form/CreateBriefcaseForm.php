<?php

namespace Drupal\ai_briefcase\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\ai_briefcase\Services\AiBriefcaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\flag\Entity\Flagging;

/**
 * CreateBriefcaseForm.
 */
class CreateBriefcaseForm extends FormBase {

  /**
   * Aiservice Drupal\ai_briefcase\Services\AiBriefcaseService.
   */
  private $aiBriefcaseService;

  /**
   * AiBriefcaseService construct.
   */
  public function __construct(AiBriefcaseService $aiBriefcaseService) {
    $this->aiBriefcaseService = $aiBriefcaseService;
  }

  /**
   * AiBriefcaseService static function.
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('ai_briefcase.aiBriefcaseService')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_briefcase_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $destination = '';
    $nid = \Drupal::request()->get('nid');
    $search_path = \Drupal::request()->get('searchpath');

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result_message"></div>',
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Briefcase Name'),
      '#required' => TRUE,
      '#description' => t('Give the name of your briefcase.'),
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => t('Briefcase Description'),
      '#description' => t('Give short description of your briefcase.'),
    ];

    $form['nid'] = [
      '#type' => 'value',
      '#value' => $nid,
    ];
    if (!empty($search_path)) {
      $form['search_path'] = [
        '#type' => 'value',
        '#value' => $search_path,
      ];
    }
    $form['actions'] = [
      '#type' => 'button',
      '#value' => t('Create Briefcase'),
      '#ajax' => [
        'callback' => '::_ai_briefcase_create_ajax_submit',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Ai_briefcase_create_ajax_submit.
   */
  public function _ai_briefcase_create_ajax_submit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $values = $form_state->getValues();
    $title = $values['title'];
    $body = $values['body'];
    $destination = '';
    $nid = $values['nid'];
    $search_path = $values['search_path'];

    if (isset($nid) && !empty($nid)) {
      if (!empty($search_path)) {
        $destination = \Drupal::service('path.alias_manager')->getAliasByPath('/' . $search_path);
      }
      else {
        $destination = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid);
      }
    }

    $briefcase_values = [
      'type'  => 'briefcase',
      'title' => $title,
      'body' => $body,
    ];

    $node = Node::create($briefcase_values);
    if (isset($nid) && !empty($nid)) {
      $current_user = \Drupal::currentUser();
      $uid = $current_user->id();

      $isFlagged = $this->aiBriefcaseService->isContentFlagged($nid, $uid);
      if (!$isFlagged) {
        $flagging = Flagging::create([
          'uid' => $uid,
          'session_id' => NULL,
          'flag_id' => 'favourites',
          'entity_id' => $nid,
          'entity_type' => 'node',
          'global' => 0,
        ]);

        $flagging->save();
      }
      $favorites[] = ['target_id' => $nid];
      $node->set('field_favorites', $favorites);
    }

    $node->save();

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $response->setAttachments($form['#attached']);
    $content = '<div class="test-popup-content">Briefcase - "' . $title . '" created successfully!</div>';
    $options = [
      'dialogClass' => 'popup-dialog-class',
      'width' => '20%',
    ];
    if (isset($destination) && !empty($destination)) {
      $response->addCommand(new RedirectCommand($destination));
      $modal = new OpenModalDialogCommand('Create New Briefcase', $content, $options);
      $response->addCommand($modal);
    }
    else {
      $default_theme = \Drupal::configFactory()->getEditable('system.theme')->get('default');
      if ($default_theme == 'aitheme') {
        $modal = new OpenModalDialogCommand('Create New Briefcase', $content, $options);
        $response->addCommand($modal);
      }
      else {
        $destination = \Drupal::service('path.alias_manager')->getAliasByPath('/my-briefcase');
        $response->addCommand(new RedirectCommand($destination));
      }
    }
    return $response;
  }

}
