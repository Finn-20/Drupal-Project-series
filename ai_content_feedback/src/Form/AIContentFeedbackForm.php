<?php

namespace Drupal\ai_content_feedback\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Component\Utility\Html;

/**
 * @file
 * Form for content feedback.
 */
/**
 * AIContentFeedbackForm class for content feedback.
 */
class AIContentFeedbackForm extends FormBase {
  /**
   * The content feedback config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The rendering service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs AIContentFeedback object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer interface.
   */
  public function __construct(ConfigFactoryInterface $config_factory, RendererInterface $renderer) {
    $this->config = $config_factory->get('content_feedback.settings');
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
        $container->get('config.factory'),
        $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'usecase_feedback_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $node = \Drupal::routeMatch()->getParameter('node');
    $_user = \Drupal::currentUser();
    $current_user = User::load($_user->id());

    $form['#prefix'] = '<div id="content_feedback_form_container">';
    $form['#suffix'] = '</div>';

    if ($node) {
      $node = Node::load($node->id());
      $uniq_id = Html::getUniqueId('vote');

      $form['rating']['content_rating'] = [
        '#type' => 'fivestar',
        '#input' => TRUE,
        '#widget' => [
          'name' => 'basic',
          'css' => drupal_get_path('module', 'fivestar') . '/widgets/basic/basic.css',
        ],
        '#title' => t('Rate this content'),
        '#stars' => 5,
        '#allow_clear' => FALSE,
        '#allow_revote' => TRUE,
        '#allow_ownvote' => TRUE,
        '#default_value' => $node->get('field_rate')->rating,
        '#attributes' => [
          'class' => ['rate'],
        ],
      ];

      $form['name'] = [
        '#type' => 'value',
        '#value' => $current_user->getAccountName(),
      ];

      $form['email'] = [
        '#type' => 'value',
        '#value' => $current_user->getEmail(),
      ];

      $form['nid'] = [
        '#type' => 'value',
        '#value' => $node->id(),
      ];

      $form['path'] = [
        '#type' => 'value',
        '#value' => Request::createFromGlobals()->server->get('HTTP_REFERER'),
      ];

      $form['ipaddress'] = [
        '#type' => 'value',
        '#value' => $this->getRequest()->getClientIp(),
      ];

      $form['usecase_feedback'] = [
        '#type' => 'textarea',
        '#title' => t('Share your feedback'),
        '#placeholder' => t('Some Examples: User Experience, Content, Design...'),
        '#required' => FALSE,
      ];

      $form['actions']['#type'] = 'actions';
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#button_type' => 'primary',
      ];
    }
    else {
      $form = [
        '#type' => 'markup',
        '#markup' => 'This is not a use case.',
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();

    $content_feedback_settings = \Drupal::config('content_feedback.settings');
    $_user = \Drupal::currentUser();
    $current_user = User::load($_user->id());

    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#content_feedback_form_container', $form));
    }
    else {
      $values = $form_state->getValues();

      $name = isset($values['name']) ? $values['name'] : NULL;
      $email = isset($values['email']) ? $values['email'] : NULL;
      $message = $values['usecase_feedback'];
      $path = $values['path'];
      $ipaddress = $values['ipaddress'];
      $timestamp = time();

      $nid = $values['nid'];
      $entity = Node::load($nid);

      if (isset($values['content_rating']) && !empty($values['content_rating'])) {
        $fivestar_field_name = 'field_rate';
        if ($entity->hasField($fivestar_field_name)) {
          /* For votingapi value will be save during save rating value to field storage. */
          $entity->set($fivestar_field_name, $values['content_rating']);
          $entity->save();
        }
      }
      if (isset($message) && !empty($message)) {
        $feedback_id = ai_content_feedback_add($name, $email, $message, $path, $ipaddress, $timestamp);

        $feedback_data = [
          'message' => $message,
          'url' => \Drupal::request()->getSchemeAndHttpHost() . '/admin/content/feedback/edit/' . $feedback_id,
        ];
        $params = ['user' => $current_user, 'feedback' => $feedback_data];

        _ai_content_feedback_notify('feedback_confirmation', $email, $params);
        _ai_content_feedback_notify('feedback_notification', $content_feedback_settings->get('notification_email_to'), $params);
      }
      // this function from user_notification module
      user_notification_all($nid,'Rating');
      $element = [
        '#type' => 'markup',
        '#markup' => 'Thank you for your feedback, we will make our best to improve the Data and AI Gallery to answer your needs and come back to you if required.',
        '#prefix' => '<div id="success-message">',
        '#suffix' => '</div>',
      ];
      $response->addCommand(new ReplaceCommand('#content_feedback_form_container', $element));
    }

    return $response;

  }

}
