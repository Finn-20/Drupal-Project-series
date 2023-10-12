<?php
namespace Drupal\ai_mainterms\Controller;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\Entity\Media;
use Drupal\media\MediaForm;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Provides route responses for the Example module.
 */
class MainTermController {
	
/** Industry **/
	public function industryRelatedContent($term = NULL) {
		
		$current_userid = \Drupal::currentUser()->id();
		$vid = 'industries';
		$parent_tids = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0,1);
		foreach($parent_tids as $key=>$parettids){ 
		   $main_terms[$parettids->tid]['parent_name'] = $parettids->name;
		   $taxonomy_fields = 'field_usecase_industry';
		   $main_terms[$parettids->tid]['industry_related_contents'] = $this->getRelatedMaintermsContent($taxonomy_fields,$parettids->tid);
		   $main_terms[$parettids->tid]['industry_explore_all'] = ['#markup' => '<span><a href="/use-case?use-case[0]=use_case_page_industry:'. $parettids->tid .'">Explore All</a></span>'];
		}
		asort($main_terms);
		return [
		  '#theme' => 'main_industry_detail_page',
		  '#main_terms'=>$main_terms,
		];
		
	}
/** Domain **/
	public function DomainRelatedContent($term = NULL) {
		
		$current_userid = \Drupal::currentUser()->id();
		$vid = 'domain';
		$parent_tids = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0,1);
		foreach($parent_tids as $parettids){
		   $main_terms[$parettids->tid]['parent_name'] = $parettids->name;
		   $taxonomy_fields = 'field_usecase_domain';
		   $main_terms[$parettids->tid]['domain_related_contents'] = $this->getRelatedMaintermsContent($taxonomy_fields,$parettids->tid);
		   $main_terms[$parettids->tid]['domain_explore_all'] = ['#markup' => '<span><a href="/use-case?use-case[0]=use_case_domain:'. $parettids->tid .'" >Explore All</a></span>'];
		}
		asort($main_terms);
		//print_r($main_terms);
		return [
		  '#theme' => 'main_domain_detail_page',
		  '#main_terms'=>$main_terms,
		];
		
	} 
	
/** Offer **/
	public function OfferelatedContent($term = NULL) {
		
		$current_userid = \Drupal::currentUser()->id();
		$vid = 'offer';
		$parent_tids = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid, 0,1);
		foreach($parent_tids as $parettids){
		   $main_terms[$parettids->tid]['parent_name'] = $parettids->name;
		   $taxonomy_fields = 'field_offer';
		   $main_terms[$parettids->tid]['offer_related_contents'] = $this->getRelatedMaintermsContent($taxonomy_fields,$parettids->tid);
		   $main_terms[$parettids->tid]['offer_explore_all'] = ['#markup' => '<span><a href="/use-case?use-case[0]=use_case_offer:'. $parettids->tid .'">Explore All</a></span>'];
		}
		asort($main_terms);
		return [
		  '#theme' => 'main_offer_detail_page',
		  '#main_terms'=>$main_terms,
		];
		
	}
  
  //** Get Term related contents **/
    public function getRelatedMaintermsContent($taxonomy_fields,$parettids) {
    $base_table = 'node__' . $taxonomy_fields;
    $base_field = $taxonomy_fields . '_target_id';
      $query = db_select($base_table, 't');
      $query->fields('t', ['entity_id'])
      ->fields('n', ['nid', 'title']);
      $query->innerJoin('node_field_data', 'n', 'n.nid = t.entity_id');
      $query->condition('t.' . $base_field, $parettids, 'IN');
      $query->condition('n.moderation_state', 'published', '=');
      $query->condition('n.type', 'use_case_or_accelerator', '=');
	  
      $query->orderBy('n.changed', 'DESC');
	  $query->range(0, 3);
      $results = $query->execute()->fetchAll();
	  $num_results = count($results);
	  $view_mode = 'search_result';
	  $entity_type = 'node';
	  $node_content = [];
	  foreach($results as $result){
		$nid = $result->nid;
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($nid);
		$node_content[] = ['#markup' => render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode))];
	  }
	  
    return $node_content;
  }
  
}
