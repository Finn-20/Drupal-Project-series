<?php

namespace Drupal\ai_briefcase\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Drupal\Component\Utility\Unicode;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "ai_briefcase_header_block",
 *   admin_label = @Translation("AI Briefcase Header Block"),
 * )
 */
class BriefcaseHeaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();

    $member = User::load($uid);

    $name = $member->getUsername();

    $firstName = (NULL != $member->field_first_name->value) ? Unicode::ucfirst(Unicode::strtolower($member->field_first_name->value)) : '';
    $lastName = (NULL != $member->field_las->value) ? Unicode::ucfirst(Unicode::strtolower($member->field_las->value)) : '';

    if ((NULL != $firstName)) {
      $name = t('@first_name', ['@first_name' => $firstName]);
    }
    elseif (NULL != $lastName) {
      $name = t('@last_name', ['@last_name' => $lastName]);
    }

    return [
      '#markup' => '<div class="welcome_user_block">Welcome, ' . $name . '</div>',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

  }

  /**
   * Cache maxage.
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
