<?php

namespace Drupal\ai_contribute_usecase\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure usecase/credential user settings for this site.
 */
class UserSettingsForm extends ConfigFormBase
{

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'ai_contribute_usecase.display.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'user_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config(static::SETTINGS);
$form['tab'] = array(
  '#type' => 'details',
  '#title' => $this->t('Tab Display'),
  );
for ($counter = 1; $counter<=7; $counter++) {
     $form['tab']['user_name_'.$counter] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter User'.'  '.$counter),
      '#prefix' => $this->t('Tab Display'),
      '#default_value' => $config->get('user_name_'.$counter),
      '#weight'=>'-1',
    ];

  }
$form['flip'] = array(
  '#type' => 'details',
  '#title' => $this->t('Flip Display'),
  '#weight'=>'0',
  );
  for ($counter = 1; $counter<=7; $counter++) {
     $form['flip']['user_name_tab_'.$counter] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter User'.'  '.$counter),
      '#prefix' => $this->t('Flip Display'),
      '#default_value' => $config->get('user_name_tab_'.$counter)
    ];

  }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // Retrieve the configuration.
    for($count=1; $count<=7; $count++) {
      $tab_d[] = $form_state->getValue('user_name_'.$count);
      $flp_d[] = $form_state->getValue('user_name_tab_'.$count);
    }
    foreach ($tab_d as  $t_value) {

      foreach ($flp_d as $f_value) {
        if(!empty($t_value) && !empty($f_value)) {
        $val = strcasecmp($t_value, $f_value);
        if($val == 0){

          drupal_set_message(t('User name must be unique in both display!'), 'error');
        }
      }
      }

    }

    for ($counter = 1; $counter<=7; $counter++) {
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.

      ->set('user_name_'.$counter, $form_state->getValue('user_name_'.$counter))
      ->set('user_name_tab_'.$counter, $form_state->getValue('user_name_tab_'.$counter))
      ->save();
    }

    parent::submitForm($form, $form_state);
  }
}
