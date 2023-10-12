<?php

namespace Drupal\ai_myidea\Services;

/**
 * Class AiBriefcaseService.
 *
 * @package Drupal\ai_briefcase\Services
 *
 * This service is to provide helper methods for BI tagging Framework.
 */
class AIMyideaNotifyFavoriteService {

  /**
   * Get the base url of site.
   */
  public static function getSiteBaseUrl() {
    return \Drupal::requestStack()->getCurrentRequest()->getScheme() . '://' . \Drupal::requestStack()->getCurrentRequest()->getHost();
  }

  /**
   * Notify term fav account.
   */
  public static function notifyIdeaTermFavorite($key, $params = []) {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'ai_myidea';
    $langcode = 'en';
    $send = TRUE;
    $from = 'aigallerycentralteam.fr@capgemini.com';
    // $to = 'aigallerycentralteam.fr@capgemini.com';
    $to = 'priya.gautam@capgemini.com';
    $bcc_email = '';

    if (!empty($params['users'])) {
      $user_list = [];
      foreach ($params['users'] as $subscribed_user) {
        if (isset($subscribed_user['user_email']) && !empty($subscribed_user['user_email']) && \Drupal::service('email.validator')->isValid($subscribed_user['user_email'])) {
          $user_list[] = $subscribed_user['user_email'];
        }
      }
      $bcc_email = implode(',', $user_list);
    }

    if (!empty($bcc_email)) {
      $params['bcc'] = $bcc_email;
    }

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);

    if ($result['result'] != TRUE) {
      $message = t('There was a problem sending your email notification to @email.', ['@email' => $to]);
      \Drupal::logger('mail-log')->error($message);
      return;
    }

    $message = t('An email notification has been sent to @email and bcc-ed to @bcc  ', ['@email' => $to, '@bcc' => $bcc_email]);
    \Drupal::logger('mail-log')->notice($message);
  }

}
