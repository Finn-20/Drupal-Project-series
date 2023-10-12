<?php

namespace Drupal\ai_briefcase\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\ai_briefcase\Services\AiBriefcaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Mydatamycalss.
 */
class UpdateBriefcaseForm extends CreateBriefcaseForm {

  /**
   * {@inheritdoc}
   */
  private $aiBriefcaseService;

  /**
   * Construct functions.
   */
  public function __construct(AiBriefcaseService $aiBriefcaseService) {
    $this->aiBriefcaseService = $aiBriefcaseService;
  }

  /**
   * Create function.
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
    return 'ai_briefcase_edit_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $briefcase = NULL) {

    $node = Node::load($briefcase);

    // Tell the user if there is nothing to display.
    if (empty($node)) {
      $form['no_values'] = [
        '#markup' => t('<h3>No results found.</h3>'),
      ];
      return $form;
    }

    $form = parent::buildForm($form, $form_state);

    $form['title']['#default_value'] = $node->get('title')->value;
    $form['body']['#default_value'] = $node->get('body')->value;
    $form['nid']['#value'] = $briefcase;

    $form['actions']['#value'] = t('Update Briefcase');

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
   * Ai briefcase ajax submit.
   */
  public function _ai_briefcase_create_ajax_submit(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $values = $form_state->getValues();
    $title = $values['title'];
    $body = $values['body'];
    $destination = '';
    $nid = $values['nid'];

    $briefcase = Node::load($nid);

    $briefcase_values = [
      'type'  => 'briefcase',
      'title' => $title,
      'body' => $body,
    ];

    $briefcase->set('title', $title);
    $briefcase->set('body', $body);
    $briefcase->save();

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $response->setAttachments($form['#attached']);

    $destination = \Drupal::service('path.alias_manager')->getAliasByPath('/my-briefcase');
    $response->addCommand(new RedirectCommand($destination));

    return $response;
  }

}
