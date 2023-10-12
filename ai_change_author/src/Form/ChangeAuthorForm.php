<?php

namespace Drupal\ai_change_author\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class ChangeAuthorForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_change_author_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $nid = \Drupal::request()->get('nid');

    $form['author_by'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Authored by'),
      '#target_type' => 'user',
      '#default_value' => User::load($uid),
      '#validate_reference' => FALSE,
      '#maxlength' => 60,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('List Content'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $author_by = $values['author_by'];
    if ($author_by == NULL) {
      $form_state->setErrorByName('author_by', 'Please enter the owner name.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $author_by = $values['author_by'];
    $url_options = ['absolute' => TRUE];
    if (isset($author_by)) {
      $url = Url::fromUri('internal:/admin/nodes/' . $author_by . '/list')->setOption('absolute', TRUE);
      $form_state->setRedirectUrl($url);
    }
  }

}
