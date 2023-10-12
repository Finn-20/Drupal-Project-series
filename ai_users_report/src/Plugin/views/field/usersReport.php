<?php

/**
 * @file
 * Definition of users_report_handler_field_timespent.
 */

namespace Drupal\ai_users_report\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;

/**
 * A handler to provide proper displays for time spent.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("users_report")
 */
class UsersReport extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['users_report'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('User Report'),
    );

    $form['users_report']['users_report_type'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Display type'),
      '#options' => array(
        'month_name' => t('Numeric to text month'),
		'month_argument' => t('Paasing month as arg'),
      ),
      '#default_value' => $this->options['users_report']['users_report_type'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);
	if ($this->options['users_report']['users_report_type'] == 'month_name') {
		$month = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');    
		return $month[$value];
	}
	return $value;
  }

}
