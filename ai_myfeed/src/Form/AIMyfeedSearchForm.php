<?php

namespace Drupal\ai_myfeed\Form;
use Drupal\ai_myfeed\AiMyFeedStorage;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\media\OEmbed\ResourceException;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Component\Utility\Html;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Ajax\CloseModalDialogCommand;

/**
 * Class for Search.
 */
class AIMyfeedSearchForm extends FormBase {
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
    return 'ai_my_feed_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
	$search_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term');

	$defult_industry = [];
	$defult_domain = [];
	$defult_offer = [];
	$current_user = \Drupal::currentUser();
	$uid = $current_user->id();
	
	$query = db_select('ai_myfeed_content', 'f');
    $query->fields('f', ['myfeed_termdata'])
      ->condition('f.myfeed_uid', $uid, '=');
    $results = $query->execute()->fetchAll();
	if(!empty($results)) {
		$termvalues =  json_decode($results[0]->myfeed_termdata, true);
		if(isset($termvalues[1])) {
			$defult_industry = $termvalues[1];
		}
		if(isset($termvalues[2])) {
			$defult_domain = $termvalues[2];
		}
		if(isset($termvalues[3])) {
			$defult_offer = $termvalues[3];
		}
	}
    $indus_vid = 'industries';
	$indus_tree= $search_terms->loadTree($indus_vid,0,NULL,TRUE);
	
	// get published industry ids
	$indq = db_select('node_field_data','n');
	$indq->join('node__field_primary_industry','ind','ind.entity_id = n.nid');
	$indq->fields('ind',array('field_primary_industry_target_id'));
	$indq->condition('n.type','use_case_or_accelerator')->condition('moderation_state','published');
	$published_industry_list = $indq->execute()->fetchAll();
	$published_industries = array();
	if(!empty($published_industry_list)){
		foreach($published_industry_list as $pub_ind){
			$published_industries[$pub_ind->field_primary_industry_target_id] = $pub_ind->field_primary_industry_target_id;
		}
	}
	
	$indus_result = [];
	foreach ($indus_tree as $industerm) {
	   if (empty($search_terms->loadChildren($industerm->id())) && isset($published_industries[$industerm->id()])){
		   if($industerm->getName() != 'Business Services' && $industerm->id() !== 268 ){
			$indus_result[$industerm->id()] = $industerm->getName();
		   }
	   }
	}
	//Domain 
	$domain_vid = 'domain';
	$domain_tree= $search_terms->loadTree($domain_vid,0,NULL,TRUE);
	
	// get published domain ids
	$domq = db_select('node_field_data','n');
	$domq->join('node__field_primary_domain','dom','dom.entity_id = n.nid');
	$domq->fields('dom',array('field_primary_domain_target_id'));
	$domq->condition('n.type','use_case_or_accelerator')->condition('moderation_state','published');
	$published_domain_list = $domq->execute()->fetchAll();
	$published_domain = array();
	if(!empty($published_domain_list)){
		foreach($published_domain_list as $pub_dom){
			$published_domain[$pub_dom->field_primary_domain_target_id] = $pub_dom->field_primary_domain_target_id;
		}
	}
	
	$domain_result = [];
	foreach ($domain_tree as $domainterm) {
	   if (empty($search_terms->loadChildren($domainterm->id())) && isset($published_domain[$domainterm->id()])){
			$domain_result[$domainterm->id()] = $domainterm->getName();
	   }
	}
	
	//Offer
	$features_vid = 'ai_features';
	$features_tree= $search_terms->loadTree($features_vid,0,NULL,TRUE);
	
	// get published ai_features ids
	$featureq = db_select('node_field_data','n');
	$featureq->join('node__field_usecase_aifeatures','fea','fea.entity_id = n.nid');
	$featureq->fields('fea',array('field_usecase_aifeatures_target_id'));
	$featureq->condition('n.type','use_case_or_accelerator')->condition('moderation_state','published');
	$published_feature_list = $featureq->execute()->fetchAll();
	$published_features = array();
	if(!empty($published_feature_list)){
		foreach($published_feature_list as $pub_feature){
			$published_features[$pub_feature->field_usecase_aifeatures_target_id] = $pub_feature->field_usecase_aifeatures_target_id;
		}
	}
	
	$features_result = [];
	foreach ($features_tree as $featuresterm) {
	   if (empty($search_terms->loadChildren($featuresterm->id())) && isset($published_features[$featuresterm->id()])){
		     if($featuresterm->getName() != '.' && $featuresterm->id() !== 439 ){
			$features_result[$featuresterm->id()] = $featuresterm->getName();
			 }
	   }
	}
	$form['#prefix'] = '<div id="my_feed_search_modal">';
    $form['#suffix'] = '</div>';
	$form['error_feed_msg'] = [  
		'#type' => 'markup',
		'#markup' => "<div id='error_msg_feed'></div>",
	];
	$form['industries_feed_term'] = [
	'#type' => 'checkboxes',
      '#title' => t('Industries'),
	  '#default_value' => $defult_industry,
      '#options' => $indus_result,
      '#prefix' => '<div class="myfeed_term_wrapper"><div class="myfeed_industries">',
      '#suffix' => '</div>',
    ];
	$form['domain_feed_term'] = [
	'#type' => 'checkboxes',
      '#title' => t('Domains'),
	  '#default_value' => $defult_domain,
       '#options' => $domain_result,
      '#prefix' => '<div class="myfeed_domains">',
      '#suffix' => '</div>',
    ];
	$form['offer_feed_term'] = [
	'#type' => 'checkboxes',
      '#title' => t('AI Features'),
	  '#default_value' => $defult_offer,
       '#options' => $features_result,
      '#prefix' => '<div class="myfeed_offers">',
      '#suffix' => '</div>',
    ];
	$form['feed_cnt_flag'] = [
        '#type' => 'hidden',
        '#value' => 0,
		'#prefix' => '<div class="myfeed_hiddenval">',
		'#suffix' => '</div><div class="clearfix"></div></div>',
    ];
    
    $form['#attributes']['class'][] = 'ctools-use-modal-processed';
	$form['actions'] = array('#type' => 'actions');
	$form['actions']['search'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'],
        'event' => 'click',
		//'wrapper' => 'my_feed_search_modal',
      ],
    ];
	$form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'modal/modal';
    $form['#attached']['library'][] = 'core/drupal.ajax';
    return $form;
  }

  
  /**
   * {@inheritdoc}
   */
	public function submitForm(array &$form, FormStateInterface $form_state) {}
    /**
	* Validates the Feed Preferances.
	*/
	public function validateForm(array &$form, FormStateInterface $form_state) {
		$industries_feed_term = array_filter($form_state->getValue('industries_feed_term'));
		$domain_feed_term = array_filter($form_state->getValue('domain_feed_term'));
		$offer_feed_term = array_filter($form_state->getValue('offer_feed_term'));
		if(empty($industries_feed_term) && empty($domain_feed_term) && empty($offer_feed_term)) {
			//$form_state->setErrorByName('error_feed_msg', t('Select at least one category'));
		}
	}
   
   /**
   * _ai_feed_save_ajax_submit.
   */
   public function submitModalFormAjax(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $industries_feed_term = array_filter($form_state->getValue('industries_feed_term'));
	$domain_feed_term = array_filter($form_state->getValue('domain_feed_term'));
	$offer_feed_term = array_filter($form_state->getValue('offer_feed_term'));
	if(!empty($industries_feed_term) || !empty($domain_feed_term) || !empty($offer_feed_term)) {
		
		$current_user= \Drupal::currentUser();
		$uid = $current_user->id();
		$feed_values = [];
		if(!empty($industries_feed_term)){
			$feed_values[1] = $industries_feed_term;
		}
		if(!empty($domain_feed_term)){
			$feed_values[2] = $domain_feed_term;
		}
		if(!empty($offer_feed_term)){
			$feed_values[3] = $offer_feed_term;
		}
		$json_feed_value = json_encode($feed_values); 
	   
		$entry = [
			'myfeed_uid'=>$uid,
			'myfeed_termdata' => $json_feed_value,
		  ]; 
		// delete old search before insert
		AiMyFeedStorage::deleteUserSearch("myfeed_uid", $uid, "ai_myfeed_content");
		$return = AiMyFeedStorage::insert($entry, 'ai_myfeed_content');
		if ($return) {
			$url = Url::fromUserInput('/my-feed-search', ['absolute' => TRUE])->toString();
			$response->addCommand(new RedirectCommand($url));
			$response->addCommand(new CloseModalDialogCommand());
		}else{
			\Drupal::logger('ai_myfeed')->log('debug', 'admin');
		}
	}else{
		\Drupal::logger('ai_myfeed')->log('debug', "Form validation");
		$response->addCommand(new HtmlCommand('#error_msg_feed', "Select at least one category"));
	}
	return $response;
  }
}
