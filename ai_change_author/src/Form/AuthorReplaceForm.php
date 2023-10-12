<?php

namespace Drupal\ai_change_author\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormBase;

/**
 * Use Drupal\ai_briefcase\Services\AiBriefcaseService;.
 */
class AuthorReplaceForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_replace_author_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $author_by = NULL) {

    $header = [
      'nodeid' => '',
      'node_title' => 'Title',
      'content_type' => 'Content type',
      'author_name' => 'Author Name',
      'updated_date' => 'Updated',
    ];
    $form['reaplace_auth_by'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Replace Author by'),
      '#target_type' => 'user',
      '#default_value' => User::load($uid),
        // Validation is done by static::entityFormValidate().
      '#validate_reference' => FALSE,
      '#maxlength' => 60,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
    ];
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#tableselect' => FALSE,
      '#empty' => $this->t('There is no content added yet.'),
      '#tabledrag' => [
    [
      'action' => 'order',
      'relationship' => 'sibling',
      'group' => 'table-order-weight',
    ],
      ],
    ];

    $query = db_select('node_field_data', 'AR');
    $query->fields('AR');
    $query->condition('AR.uid', $author_by, '=');
    $query->condition('AR.type', ['use_case_or_accelerator', 'asset'], 'IN');
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $nid = $result->nid;
      // Pass your uid.
      $account = User::load($result->uid);
      $user_name = $account->getDisplayName();
      $form['table'][$nid]['nodeid'] = [
        '#type' => 'hidden',
        '#value' => $nid,
      ];
      $form['table'][$nid]['node_title'] = [
        '#plain_text' => $result->title,
      ];
      $form['table'][$nid]['content_type'] = [
        '#plain_text' => $result->type,
      ];
      $form['table'][$nid]['author_name'] = [
        '#plain_text' => $user_name,
      ];
      $form['table'][$nid]['updated_date'] = [
        '#plain_text' => format_date($result->changed, 'custom', 'm/d/Y-H:i'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $reaplace_auth_by = $values['reaplace_auth_by'];
    if ($reaplace_auth_by == NULL) {
      $form_state->setErrorByName('reaplace_auth_by', 'Please enter the User name.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $replace_auth_by = $values['reaplace_auth_by'];
    foreach ($values['table'] as $key => $value) {
      $nid = $value['nodeid'];
      $node = Node::load($nid);
      $node->set('uid', $replace_auth_by);
      $node->save();
    }

    drupal_set_message($this->t('AuthorName updated Successfully.'));
    $url = Url::fromUri('internal:/admin/nodes/author/' . $replace_auth_by . '/list')->setOption('absolute', TRUE);
    $form_state->setRedirectUrl($url);

  }

}
