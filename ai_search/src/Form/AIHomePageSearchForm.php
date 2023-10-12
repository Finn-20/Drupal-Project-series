<?php

namespace Drupal\ai_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

/**
 * Class for Search.
 */
class AIHomePageSearchForm extends FormBase {
  /**
   * The rendering service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs AIHomePageSearch object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer interface.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
        $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_home_page_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['search']['search_keys'] = [
      '#type' => 'textfield',
      '#title' => '',
      '#default_value' => '',
      '#placeholder' => 'Type keyword',
      '#prefix' => '<div class="form--inline form-inline clearfix">',
    ];
    $form['search']['search_type'] = [
      '#type' => 'select',
      '#options' => [
        'all' => 'Any',
        'usecase' => 'Usecase',
        'accelerator' => 'Accelerator',
      ],
      '#default_value' => 'all',
      '#title' => '',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $keys = $values['search_keys'];
    $search_type = $values['search_type'];

    if ($search_type == 'all') {
      $redirect_url = '/ai-browse-all-search';
    }
    elseif ($search_type == 'usecase') {
      $redirect_url = '/ai-use-case-search';
    }
    elseif ($search_type == 'accelerator') {
      $redirect_url = '/ai-accelerator-search';
    }
    if (isset($keys) && !empty($keys)) {
      $url = Url::fromUserInput($redirect_url,
       [
         'query' => [
           'keys' => $keys,
           'sort_by' => 'search_api_relevance',
         ],
       ]);
    }
    else {
      $url = Url::fromUserInput($redirect_url, ['query' => ['sort_by' => 'search_api_relevance']]);
    }
    // Set redirection after form submission.
    $form_state->setRedirectUrl($url);
  }

}
