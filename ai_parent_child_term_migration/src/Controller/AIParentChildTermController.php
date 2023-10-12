<?php
namespace Drupal\ai_parent_child_term_migration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\flag\Entity\Flagging;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides route responses for the Briefcase module.
 */
class AIParentChildTermController extends ControllerBase {
	
	public function MigrateParentChildTerms() {
		$build['markup_display'] = [
		  '#type' => 'markup',
		  '#markup' => 'parent child terms of industry'
		];
		$term_table_header = array ('Added Term',  'Node(s) To Update');
		
	//	\Drupal::configFactory()->getEditable('ai_parent_child_term_migration.settings')->delete();
		 $run_url = \Drupal::config('ai_parent_child_term_migration.settings')->get('run_url_details') == 1 ? 1 : 0;
	//print $run_url;
		if($run_url == 1) {
			$term_table_rows[]  =  array ('data' => array(array('data' => "Cannot run it again. Already executed!", 'colspan' => 2)), 'class' => 'td_bold_data');
			return array(
			'#theme' => 'table',
			'#header' => $term_table_header,
			'#rows' => $term_table_rows,
			);
		}  
		
		$dropdown_vocab = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('industries');
		
		foreach ($dropdown_vocab as $term) {
			$all_terms[$term->tid] = (object)(array('name' => $term->name, 'tid' => $term->tid));
			if ($term->parents[0] == 0) {
				$parent_terms[$term->tid] =(object)(array('name' => $term->name, 'tid' => $term->tid));
				$parent_terms_new[] = $term->tid;
			}
			else {
				$child_terms[$term->parents[0]][$term->tid] = (object)(array('name' => $term->name, 'tid' => $term->tid));
				$child_term_ids[$term->parents[0]][] =$term->tid;
			}
		}
		//echo '<pre>';print_r($child_term_ids);echo '</pre>';exit;
		$nodes = \Drupal::entityTypeManager()->getStorage('node');
		$query = \Drupal::entityQuery('node')
				->condition('type', 'use_case_or_accelerator', '=');
		$nids = $query->execute();
					
		$term_table_header = array ('Node Title', 'Current terms', 'Updated Term(s)');
		foreach($nids as $revision_id => $node_id) {
		//if($node_id == 140) { 
			$check_node = Node::load($node_id);
			$check_node_title = $check_node->getTitle();
			$current_node_terms = array();
			$terms_added = array();
			$terms_to_be_added = array();
			
			if (isset($check_node) && !empty($check_node)) {

				foreach($check_node->field_usecase_industry as $reference) {
					
					$current_node_terms[] = $reference->target_id; 
					$term_table_current_matches[$reference->target_id] =  $all_terms[$reference->target_id]->name;
					$terms_to_be_added[] = $reference->target_id;
					if(in_array($reference->target_id, $parent_terms_new)) {
						foreach($child_term_ids[$reference->target_id] as $child_mulitple_id) {
							$terms_to_be_added[] = $child_mulitple_id;
						}
					} else if(!in_array($reference->target_id, $parent_terms_new)) {
						$parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($reference->target_id);
						$parent = reset($parent);
						
						//array_push($terms_added, $parent->id());
						
						$term_table_rows_nodes[$check_node_title][] = $parent->id();
						
						$terms_to_be_added[] = $parent->id();
					}
				} 
				
				
				
				$terms_to_be_added_unique = array_unique($terms_to_be_added);
				
				$result  = array_diff($terms_to_be_added_unique, $current_node_terms);
				
				/* echo '<pre>';print_r($terms_to_be_added_unique);echo '</pre>';
				echo '<pre>';print_r($current_node_terms);echo '</pre>';
				echo '<pre>';print_r($result);echo '</pre>';exit; */
			
				if(count($result) > 0) {
						foreach($terms_to_be_added_unique as $updated_tid){
						$term_table_rows_matches[$updated_tid] =  $all_terms[$updated_tid]->name;
						}

					$term_table_rows[] = array ('data' => array($check_node->title->value, implode(', ', $term_table_current_matches), implode(', ', $term_table_rows_matches)));
					
					$check_node->set('field_usecase_industry',$terms_to_be_added_unique);
				    //$check_node->save();
				}
			}
		//}
		}
		$term_table_rows[]  =  array ('data' => array(array('data' => "Total " . count($term_table_rows) . ' Use Case(s) updated.', 'colspan' => 2)), 'class' => 'td_bold_data');
		
		 \Drupal::service('config.factory')
         ->getEditable('ai_parent_child_term_migration.settings')
         ->set('run_url_details', 1)
         ->save(); 
		return array(
		'#theme' => 'table',
		'#header' => $term_table_header,
		'#rows' => $term_table_rows,
		);
/**********END ***********************/		
		
		
		
		//echo '<pre> --> ';print_r($result); echo '</pre>';die();
		/* foreach($term_table_rows_nodes as $node_title => $term_ids){
			if(is_array($term_ids)){
				foreach($term_ids as $updated_tid){
					$term_table_rows_matches[$updated_tid] =  $all_terms[$updated_tid]->name;
				}
				$term_table_rows[] = array ('data' => array($node_title, implode(', ', $term_table_rows_matches)));
				unset($term_table_rows_matchs);
			}
		} */
		//$term_table_rows[]  =  array ('data' => array(array('data' => "Total " . count($term_table_rows) . ' Use Case(s) updated.', 'colspan' => 2)), 'class' => 'td_bold_data');
		
		
	}
}	
	
	
	/*
    private function array_subtract($arr1, $arr2) {
      $result = array();
      foreach ($arr1 as $k => $val)
        $result[] = $val - $arr2[$k];
      return $result;
    }
   public function MigrateParentChildTerms() {
    $build['markup_display'] = [
      '#type' => 'markup',
      '#markup' => 'parent child terms of industry'
    ];
    $term_table_header = array ('Added Term',  'Node(s) To Update');
    $dropdown_vocab = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('industries');

    foreach ($dropdown_vocab as $term) {
      $all_terms[$term->tid] = (object)(array('name' => $term->name, 'tid' => $term->tid));
      if ($term->parents[0] == 0) {
        $parent_terms[$term->tid] =(object)(array('name' => $term->name, 'tid' => $term->tid));
        
      }
      else {
      	$child_terms[$term->parents[0]][$term->tid] = (object)(array('name' => $term->name, 'tid' => $term->tid));
      	$child_term_ids[$term->parents[0]][] =$term->tid;
      }
    }
    foreach ($parent_terms as $term) {
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
        ->latestRevision()
        ->condition('type', 'use_case_or_accelerator', '=')
        ->condition('field_usecase_industry', $term->tid, '=')
        ->execute();
      $parent_term_ids[$term->tid] = $nodes;
      foreach ($child_terms[$term->tid] as $cidterm) {
          $nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
            ->latestRevision()
            ->condition('type', 'use_case_or_accelerator', '=')
            ->condition('field_usecase_industry', $cidterm->tid, '=')
            ->execute();
          $child_term_ids[$term->tid][$cidterm->tid] = $nodes;
          $unique_nodes[$cidterm->tid] = array_diff($this->array_subtract($parent_term_ids[$term->tid], $child_term_ids[$term->tid][$cidterm->tid]), array(0));
          $unique_cnodes[$term->tid] =  array_diff($this->array_subtract($child_term_ids[$term->tid][$cidterm->tid], $parent_term_ids[$term->tid]), array(0));

      }  
    }
    $term_table_rows[] = array ('Child Nodes without parent', '   ');
    foreach($unique_nodes as $child_id => $ul_node_array) {
    	$term_table_rows[] = array ($all_terms[$child_id]->name, '   ');
    	foreach ($ul_node_array as $ul_nodes) {
	    	$check_node = Node::load($ul_nodes);
	    	$term_table_rows[] = array (' ', $check_node->getTitle() );
	    	$check_node->field_usecase_industry[] = ['target_id' => $child_id];
	    	//$check_node->save();
	    }
    }
    $term_table_rows[] = array ('Parent Nodes without all child', '   ');
    foreach($unique_cnodes as $child_id => $ul_node_array) {
    	$term_table_rows[] = array ($all_terms[$child_id]->name, '   ');
    	foreach ($ul_node_array as $ul_nodes) {
	    	$check_node = Node::load($ul_nodes);
	    	$term_table_rows[] = array (' ', $check_node->getTitle() );
	    	$check_node->field_usecase_industry[] = ['target_id' => $child_id];
	    	//$check_node->save();
	    }
    }
    return array(	  '#header' => $term_table_header,
	  '#rows' => $term_table_rows,
	);
  } */
