<?php

namespace Drupal\ai_collaborate\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\user\PrivateTempStoreFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TribesAssetsMappingFormBase extends FormBase {

  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * @var \Drupal\database\Connection
   */
  /**
   * Constructs a \Drupal\demo\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user, Connection $database) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;
    $this->store = $this->tempStoreFactory->get('multistep_data');
    $this->database = $database;
  }

	
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user'),
      $container->get('database')  
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Start a manual session for anonymous users.
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['multistep_form_holds_session'])) {
      $_SESSION['multistep_form_holds_session'] = true;
      $this->sessionManager->start();
    }

    $form = array();
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
  }

  /**
   * Saves the data from the multistep form.
   */
  protected function saveData($values) {
    $submitted_tribes_values = $values;
    try {

      $transaction = $this->database->startTransaction();

      foreach ($submitted_tribes_values as $nid => $tribes_categories) {
        $node = [];
          $node = node::load($nid);
          if ($node instanceof NodeInterface) {
            $getIds = $node->field_tribes_related_assets->getValue();
            if (!empty($getIds)) {
              if (!empty($submitted_tribes_values['mapped_assets'][$nid])) {
                $node->set('field_tribes_related_assets', []);
                $node->save();
              }
            }
            foreach ($tribes_categories as $cid => $select_status) {
              if (!empty($select_status)) {
                if (empty($node->field_tribes_related_assets->getValue()[0]['target_id'])) {
                  $node->set('field_tribes_related_assets', $cid);
                }
                else {
                  $node->get('field_tribes_related_assets')->appendItem($cid);
                }
              }
            }
            $node->save();
          }
      }

      $this->deleteStore();
      drupal_set_message($this->t('The form has been saved.'));
    }
    catch (Exception $ex) {
      $transaction = $this->database->rollBack();
      drupal_set_message($this->t('System encountered some error while saving the data.'));
    }
  }
  
  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {
    $keys = ['tribes_category'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
  
  //protected function remove
}