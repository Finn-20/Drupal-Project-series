<?php
namespace Drupal\ai_partners\Controller;

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
class AiPartnerController {
  public function termsContents($glossary = NULL) {  
    $current_userid = \Drupal::currentUser()->id();
	$vid = 'tech_stacks';
	$terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
	$term_data_value['have_termval'] = '';
	asort($terms);
	$gloss_arr = [];
	$current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);
	$arg_letters = $path_args[2];
	
	foreach ($terms as $term) {
	 $term_id = $term->tid;
	 $term_name = $term->name;
	 
	 if(!in_array($gloss_arr , strtolower(substr($term_name, 0, 1)))){ 
		 //$gloss_arr[strtolower(substr($term_name, 0, 1))] = ['#markup' => '<span><a href="/partnersdetails/' . strtolower(substr($term_name, 0, 1)) . '" class="'. $active .'"  >' .  strtoupper(substr($term_name, 0, 1)) . '</a></span>']; 
	    $gloss_arr[strtolower(substr($term_name, 0, 1))]['term_alphabets'] = strtolower(substr($term_name, 0, 1));
	 }

	 $gloss_arr[strtolower(substr($term_name, 0, 1))]['arg_letters'] = (isset($arg_letters)) ? $arg_letters : '';
	 
	 if(isset($glossary) && !empty($glossary)){
		if(strtolower(substr($term_name, 0, 1)) != strtolower($glossary)){	
			continue;
		}
	 }
	 $term_names[$term->tid]['termname_title']= $term_name;
	 $term_values = \Drupal\taxonomy\Entity\Term::load($term_id);
	 $term_names[$term->tid]['deck_descprition'] = $term_values->get('field_description_deck')->value;
	 $taxonomy_fields = 'field_usecase_technology';
	 $term_names[$term->tid]['terms_related_featuredcontent'] = $this->getRelatedTermByNodes($taxonomy_fields,$term_id);
	
		if (isset($term_values->get('field_playbook')->target_id) && !empty($term_values->get('field_playbook')->target_id)) {
		    $playbook_tid = $term_values->get('field_playbook')->target_id;
			$playbook_media_id = Media::load($playbook_tid);
			$playbook_script_targetid = $playbook_media_id->field_media_file->target_id;
			$playbook_script_file = \Drupal\file\Entity\File::load($playbook_script_targetid);
			$playbook_script_fileSRC = file_create_url($playbook_script_file->getFileUri());
			$playbook_script_fileName = $playbook_script_file->getFilename();
			$term_names[$term->tid]['playbook_script_fileName'] =(strlen($playbook_script_fileName) > 25) ? substr($playbook_script_fileName, 0, 25) . '...' : $playbook_script_fileName;
			$term_names[$term->tid]['playbook_script_fileSRC'] = $playbook_script_fileSRC;
			$playbook_split_filename = explode('.', $playbook_script_fileName);
			$term_names[$term->tid]['playbook_script_fileext'] = strtolower($playbook_split_filename[count($playbook_split_filename)-1]);
		}
		if (isset($term_values->get('field_webinar')->target_id) && !empty($term_values->get('field_webinar')->target_id)) {
		    $webinar_tid = $term_values->get('field_webinar')->target_id;
			$webinar_media_id = Media::load($webinar_tid);
			$webinar_script_targetid = $webinar_media_id->field_media_video_file->target_id;
			$webinar_script_file = \Drupal\file\Entity\File::load($webinar_script_targetid);
			$webinar_script_fileSRC = file_create_url($webinar_script_file->getFileUri());
			$webinar_script_fileName = $webinar_script_file->getFilename();
			$term_names[$term->tid]['webinar_script_fileName'] = (strlen($webinar_script_fileName) > 25) ? substr($webinar_script_fileName, 0, 25) . '...' : $webinar_script_fileName;
			$term_names[$term->tid]['webinar_script_fileSRC'] = $webinar_script_fileSRC;
			$webinar_split_filename = explode('.', $webinar_script_fileName);
			$term_names[$term->tid]['webinar_script_fileext'] = strtolower($webinar_split_filename[count($webinar_split_filename)-1]);
			
		}
		//capability deck
		if (isset($term_values->get('field_capability_deck')->target_id) && !empty($term_values->get('field_capability_deck')->target_id)) {
		    $capability_deck_tid = $term_values->get('field_capability_deck')->target_id;
			$cap_media_id = Media::load($capability_deck_tid);
			$cap_script_targetid = $cap_media_id->field_media_file->target_id;
			$cap_script_file = \Drupal\file\Entity\File::load($cap_script_targetid);
			$cap_script_fileSRC = file_create_url($cap_script_file->getFileUri());
			$cap_script_fileName = $cap_script_file->getFilename();
			$term_names[$term->tid]['cap_script_fileName'] = (strlen($cap_script_fileName) > 25) ? substr($cap_script_fileName, 0, 25) . '...' : $cap_script_fileName;
			$term_names[$term->tid]['cap_script_fileSRC'] = $cap_script_fileSRC;
			$cap_split_filename = explode('.', $cap_script_fileName);
			$term_names[$term->tid]['cap_script_fileext'] = strtolower($cap_split_filename[count($cap_split_filename)-1]);
			
		}
		//sales decl
		 if (isset($term_values->get('field_sales_deck')->target_id) && !empty($term_values->get('field_sales_deck')->target_id)) {
		    $sales_deck_tid = $term_values->get('field_sales_deck')->target_id;
			$sales_media_id = Media::load($sales_deck_tid);
			$sales_script_targetid = $sales_media_id->field_media_file->target_id;
			$sales_script_file = \Drupal\file\Entity\File::load($sales_script_targetid);
			$sales_script_fileSRC = file_create_url($sales_script_file->getFileUri());
			$sales_script_fileName = $sales_script_file->getFilename();
			$term_names[$term->tid]['sales_script_fileName'] = (strlen($sales_script_fileName) > 25) ? substr($sales_script_fileName, 0, 25) . '...' : $sales_script_fileName;
			$term_names[$term->tid]['sales_script_fileSRC'] = $sales_script_fileSRC;
			$sales_split_filename = explode('.', $sales_script_fileName);
			$term_names[$term->tid]['sales_script_fileext'] = strtolower($sales_split_filename[count($sales_split_filename)-1]);
			
		}
	}
	asort($gloss_arr);
	//print_r($gloss_arr);
	 return [
      '#theme' => 'ai_partners_page',
	  '#glossary' => $gloss_arr,
	  '#term_names'=>$term_names,
      '#term_field_values' => $term_field_values,
    ];
  }
  
  public function getRelatedTermByNodes($taxonomy_fields,$term_id) {
    $base_table = 'paragraph__' . $taxonomy_fields;
    $base_field = $taxonomy_fields . '_target_id';
      $query = db_select($base_table, 't');
      $query->fields('t', ['entity_id'])
      ->fields('p', ['parent_id'])
      ->fields('n', ['nid', 'title']);
      $query->innerJoin('paragraphs_item_field_data', 'p', 'p.id = t.entity_id');
      $query->innerJoin('node_field_data', 'n', 'n.nid = p.parent_id');
      $query->innerJoin('node__field_featured_usecase', 's', 's.entity_id = n.nid');
      $query->condition('t.' . $base_field, $term_id, 'IN');
	  $query->condition('s.field_featured_usecase_value', '1', '=');
      $query->condition('n.moderation_state', 'published', '=');
	  
      $query->orderBy('n.changed', 'DESC');
      //print $query; print "<br/>";
      $results = $query->execute()->fetchAll();
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