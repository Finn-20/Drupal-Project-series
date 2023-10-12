<?php

namespace Drupal\ai_contribute_usecase\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Component\Utility\Html;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\ai_contribute_usecase\AiOtherFieldStorage;

/**
 * Class for Search.
 */
class OtherFieldForm extends FormBase {
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
  public static function create(ContainerInterface $container){
    // Instantiates this form class.
    return new static(
        $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ai_other_field_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$other_type = 0,$uniqid = 0) {
	$form['error_msg'] = [  
		'#type' => 'markup',
		'#markup' => "<div id='error_msg'></div>",
	];
	$form['other_type'] = [
      '#type' => 'hidden',
      '#value' => $other_type,
    ];
    $form['uniqid'] = [
      '#type' => 'hidden',
      '#value' => $uniqid,
    ];
    $other_text = '';
    switch ($other_type) {
        case 1:
            $other_text = 'Partner';
            break;
        case 2:
            $other_text = 'Feature';
            break;
        case 3:
            $other_text = 'Framework';
            break;
    }
    $form['other_text'] = [
      '#type' => 'hidden',
      '#value' => $other_text,
    ];
    $uniqid_session = \Drupal::service('tempstore.private')->get('ai_contribute_usecase');
    $tag_action = $uniqid_session->get('allow_add_tag');
    $form['tags'] = [
      '#type' => 'details',
      '#description' => $other_text,
      '#open' => TRUE,
    ];
    if(!empty($tag_action) && $tag_action != "not allowed"){
    $form['tags']['addtag'] = [
      '#type' => 'submit',
      '#value' => t('Add one more'),
      '#submit' => ['::addOneTag'],
      '#weight' => 100,
      '#ajax' => [
        'callback' => '::updateTagCallback',
        'wrapper' => 'tagfields-wrapper',
        'method' => 'replace',
      ],
    ];
    $form['tags']['remtag'] = [
      '#type' => 'submit',
      '#value' => t('Remove the last'),
      '#submit' => ['::remOneTag'],
      '#weight' => 100,
      '#ajax' => [
        'callback' => '::updateTagCallback',
        'wrapper' => 'tagfields-wrapper',
        'method' => 'replace',
      ],
    ];
    $form['tags']['tag_values'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => '<div id="tagfields-wrapper">',
      '#suffix' => '</div>',
    ];
  }
	
    // get already added tags
    $results = AiOtherFieldStorage::load($uniqid,$other_type);
    
    $number_of_tags = $form_state->get('number_of_tags');
    $is_tag_exist = $form_state->get('is_tag_exist');
    if(!empty($results) && !($is_tag_exist)){
    	$number_of_tags = count($results);
      	$form_state->set('number_of_tags', $number_of_tags);
      	$form_state->set('is_tag_exist', 1);
    }else if(empty($number_of_tags)) {
	    $number_of_tags = 1;
	    $form_state->set('number_of_tags', $number_of_tags);
    }
    for ($i = 0; $i < $number_of_tags; $i++) {
      $form['tags']['tag_values'][$i] = [
        '#type' => 'textfield',
        '#default_value' => isset($results[$i])?$results[$i]->other_tag:'',
      ];
    }
    if(!empty($tag_action) && $tag_action != 'not allowed'){
    $form['#attributes']['class'][] = 'ctools-use-modal-processed';
  	$form['actions'] = array('#type' => 'actions');
  	$form['actions']['search'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#attributes' => [
          'class' => [
            'use-ajax',
          ],
        ],
        '#ajax' => [
          'callback' => [$this, 'submitModalFormAjax'],
          'event' => 'click',
        ],
      ];
  }
	 $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'modal/modal';
    $form['#attached']['library'][] = 'core/drupal.ajax';
    return $form;
  }

/**
* Add or Increment number of tags.
*/
public function addOneTag(array &$form, FormStateInterface $form_state) {
    $number_of_tags = $form_state->get('number_of_tags');
    $form_state->set('number_of_tags', $number_of_tags + 1);
    $form_state->setRebuild(TRUE);
}
/**
  * Remove or Decrement number of tags.
  */
public function remOneTag(array &$form, FormStateInterface $form_state) {
    $number_of_tags = $form_state->get('number_of_tags');
    $form_state->set('number_of_tags', $number_of_tags - 1);
    $form_state->setRebuild(TRUE);
}
/**
  * Return the tag list (Form).
  */
public function updateTagCallback(array &$form, FormStateInterface $form_state) {
    return $form['tags']['tag_values'];
}
  /**
   * {@inheritdoc}
   */
	public function submitForm(array &$form, FormStateInterface $form_state) {}
    /**
	* Validates the Feed Preferances.
	*/
	public function validateForm(array &$form, FormStateInterface $form_state) {
		
	}
   
   /**
   * _ai_other_field_save_ajax_submit.
   */
   public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
   	$tag_values = $form_state->getValue('tag_values');
   	$uniqid = $form_state->getValue('uniqid');
   	$other_type = $form_state->getValue('other_type');
    $other_text = $form_state->getValue('other_text');
   	$other_tags = [];
   	if(!empty($tag_values)){
   		$is_field_empty = false;
   		for ($i=0; $i < count($tag_values); $i++) { 
   			if(empty($tag_values[$i])){
   				$is_field_empty = true;
   				break;
   			}else{
   				$other_tags[] = [
   					'uniqid' => $uniqid,
   					'other_type' => $other_type,
   					'other_tag' => $tag_values[$i],
   				];
   			}
   		}
   	}else{
   		$is_field_empty = true;
   	}
    $response = new AjaxResponse();
    if($is_field_empty){
    	$response->addCommand(new HtmlCommand('#error_msg', "<span style='color:red'>Enter the {$other_text} tag</span>"));
    }else{
    	// store into db
    	if(!empty($other_tags)){
    		// deleted old records
    		AiOtherFieldStorage::deleteTag($uniqid,$other_type);
    		AiOtherFieldStorage::bulkInsert($other_tags);
    	}
    	$response->addCommand(new CloseModalDialogCommand());
    }
	return $response;
  }
}
