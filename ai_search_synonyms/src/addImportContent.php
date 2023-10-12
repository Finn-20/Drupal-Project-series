<?php
namespace Drupal\ai_search_synonyms;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Drupal\Core\Asset\file_create_url;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

class addImportContent {
  public static function addImportSynonymItem($data, &$context){
    $context['sandbox']['current_item'] = $data;
    $message = 'Imported synonyms for ' . count($data) . ' keywords';
    $results = array();
    foreach ($data as $item) {
      $synonym = create_synonyms($item);
      if (isset($synonym) && !empty($synonym)) {
        $results[$item] = $synonym;
      }
    }  
    
    if (!empty($results)) {
      $current_timestamp = \Drupal::time()->getCurrentTime();
      $filename = 'search_keywords_synonyms_' . $current_timestamp . '.csv';
      $filepath = 'public://synonyms_export/' . $filename;
      $fp = fopen($filepath, 'w');
      $header = TRUE;
      foreach($results as $word => $s_values){
        if ($header) {
          $line = ['word', 'synonym', 'type'];
          fputcsv($fp, $line);
          $header = FALSE;
        }
        $line = [$word, implode(',', $s_values), 'synonym'];
        fputcsv($fp, $line);
      }
      fclose($fp);
    
      /* $response = new BinaryFileResponse($filepath);
      $response->setContentDisposition(
          ResponseHeaderBag::DISPOSITION_ATTACHMENT,
          $filename
      );
      $response->send(); */
    }
    
    $context['message'] = $message;
    $context['results']['filepath'] = $filepath;
  }
  function addImportSynonymItemCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      if (isset($results['filepath']) && !empty($results['filepath'])) {
        $message = 'Please <a href="' . file_create_url($results['filepath']) . '">Click here</a> to download the synonyms of new search keywords';
        $rendered_message = Markup::create($message);
        drupal_set_message($rendered_message);
        
      }
    }
    else {
      $message = t('Finished with an error.');
      drupal_set_message($message);
    }
  }
}

// This function actually creates each item as a node as type 'Page'
function create_synonyms($item) {
  $synonyms = [];
  $uri = 'https://api.datamuse.com/words?ml=' . str_replace(' ', '+', $item) . '&_format=json';
  try {
    $response = \Drupal::httpClient()->get($uri);
    $data = (string) $response->getBody();
    if (!empty($data)) {
      $decoded_data = Json::decode($data);
      if (isset($decoded_data) && !empty($decoded_data)) {
        foreach ($decoded_data as $d_item) {
          if (isset($d_item['word']) && isset($d_item['tags']) && is_array($d_item['tags']) && in_array('syn', $d_item['tags'])) {
            $synonyms[] = $d_item['word'];
          }
        }
      }
      if (isset($synonyms) && !empty($synonyms)) {
        $message = t('Synonyms of "@item" are: @synonyms', ['@key' => $item, '@synonyms' => implode(', ', $synonyms)]);
        \Drupal::logger('synonyms')->notice($message);
      }
    }
  }
  catch (RequestException $e) {
    \Drupal::logger('synonyms')->error($e->getMessage());
  }
  
  return $synonyms;
}