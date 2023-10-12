<?php
namespace Drupal\ai_content_sharing;

use Drupal\Component\Serialization\Json;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Drupal\Core\Asset\file_create_url;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;

class exportTrackingDetails {
  public static function getTrackingDetails($data, &$context){
    $context['sandbox']['current_item'] = $data;
    $message = 'Exporting ' . count($data) . ' tracking records';
    $results = array();
    
    if (!empty($data)) {
      $current_timestamp = \Drupal::time()->getCurrentTime();
      $filename = 'content_share_tracking_report_' . $current_timestamp . '.csv';
      $filepath = 'public://tracking_data/' . $filename;
      $fp = fopen($filepath, 'w');
      
      $header = ['Node id', 'Title', 'URL', 'User Id', 'User Name', 'Email Id', 'Is Submitted', 'Date/Time', 'Submission URL'];
      fputcsv($fp, $header);
      
      foreach($data as $line){
        fputcsv($fp, $line);
      }
      fclose($fp);
    }
    
    $context['message'] = $message;
    $context['results']['filepath'] = $filepath;
  }
  function getTrackingDetailsCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      if (isset($results['filepath']) && !empty($results['filepath'])) {
        $message = 'Please <a href="' . file_create_url($results['filepath']) . '">Click here</a> to download the tracking report';
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