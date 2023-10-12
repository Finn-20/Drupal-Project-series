<?php

namespace Drupal\ai_attachment_media\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\media\OEmbed\ResourceException;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Drupal\media_library\MediaLibraryUiBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ai_attachment_media\Plugin\media\Source\ExternalLinkInterface;
use Drupal\media_library\Form\AddFormBase;

/**
 * Creates a form to create media entities from External URLs.
 *
 */
class ExternalLinkForm extends AddFormBase {

  /**
   * Constructs a new ExternalLinkForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\media_library\MediaLibraryUiBuilder $library_ui_builder
   *   The media library UI builder.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MediaLibraryUiBuilder $library_ui_builder) {
    parent::__construct($entity_type_manager, $library_ui_builder);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('media_library.ui_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getMediaType(FormStateInterface $form_state) {
    if ($this->mediaType) {
      return $this->mediaType;
    }

    $state = $this->getMediaLibraryState($form_state);
    $selected_type_id = $state->getSelectedTypeId();
    $this->mediaType = $this->entityTypeManager->getStorage('media_type')->load($selected_type_id);

    if (!$this->mediaType) {
      throw new \InvalidArgumentException("The '$selected_type_id' media type does not exist.");
    }

    return $this->mediaType;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildInputElement(array $form, FormStateInterface $form_state) {
    $form['#attributes']['class'][] = 'media-library-add-form--link';

    $media_type = $this->getMediaType($form_state);
    
    // Add a container to group the input elements for styling purposes.
    $form['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['media-library-add-form__input-wrapper'],
      ],
    ];

    $form['container']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Add @type', [
        '@type' => $this->getMediaType($form_state)->label(),
      ]),
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'https://',
        'class' => ['media-library-add-form-external-url'],
      ],
    ];

    $form['container']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
      '#validate' => ['::validateUrl'],
      '#submit' => ['::addButtonSubmit'],
      // @todo Move validation in https://www.drupal.org/node/2988215
      '#ajax' => [
        'callback' => '::updateFormCallback',
        'wrapper' => 'media-library-wrapper',
        // Add a fixed URL to post the form since AJAX forms are automatically
        // posted to <current> instead of $form['#action'].
        // @todo Remove when https://www.drupal.org/project/drupal/issues/2504115
        //   is fixed.
        'url' => Url::fromRoute('media_library.ui'),
        'options' => [
          'query' => $this->getMediaLibraryState($form_state)->all() + [
            FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          ],
        ],
      ],
      '#attributes' => [
        'class' => ['media-library-add-form-oembed-submit'],
      ],
    ];
    return $form;
  }

  /**
   * Validates the External URL.
   *
   * @param array $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  public function validateUrl(array &$form, FormStateInterface $form_state) {
    $url = $form_state->getValue('url');
    if (empty($url)) {
      $form_state->setErrorByName('url', $this->t('Field can not be empty'));
    }
  }

  /**
   * Submit handler for the add button.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function addButtonSubmit(array $form, FormStateInterface $form_state) {
    $this->processInputValues([$form_state->getValue('url')], $form, $form_state);
  }

}
