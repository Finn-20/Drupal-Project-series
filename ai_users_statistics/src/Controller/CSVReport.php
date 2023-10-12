<?php

namespace Drupal\ai_users_statistics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

/**
* Class CSVReport.
*
* @package Drupal\ai_users_statistics\Controller
*/
class CSVReport extends ControllerBase {
	protected $database;

	public function __construct() {
		$this->database = \Drupal::database();
	}


	/**
	* Export a CSV of data.
	*/
	public function build($asset_type = 0) {
	   // Start using PHP's built in file handler functions to create a temporary file.
	   $handle = fopen('php://temp', 'w+');
	   
		$current_user = \Drupal::currentUser();
		$uid = $current_user->id();
		// 1. get node title and published date
		$query = $this->database->query("SELECT node_field_data.created as published_on,node_field_data.title,nid
			FROM node_field_data node_field_data
			WHERE ((node_field_data.uid = {$uid})) 
			AND (node_field_data.moderation_state = 'published') ORDER BY created ASC");
		$totalAssets = $query->fetchAll();
		// Set up the header that will be displayed as the first line of the CSV file.
		$header = ['Asset Name'];
		$publishedDate = ['Published On'];
		$month = ['Month'];
		$nids = [];
		$first_assets_published_date = 0;
		$totalViewsNids = [];
		$totalVotesNids = [];
		$i = 0;
		   foreach($totalAssets as $totalAsset) {
			   $header[] = $totalAsset->title;
			   $header[] = ' ';
			   $publishedDate[] = date('d-m-Y',$totalAsset->published_on);
			   $publishedDate[] = ' ';
			   $month[] = 'Views';
			   $month[] = 'Votes';
			   if($i == 0) {
				   $first_assets_published_date = $totalAsset->published_on;
			   }
			   $nids[] = $totalAsset->nid;
			   $i++;
		   }
	   // Add the header as the 1st line of the CSV.
	   fputcsv($handle, $header);
	   // Add the publishedDate as the 2nd line of the CSV.
	   fputcsv($handle, $publishedDate);
	   // Add the Month,views and votes name as the 3rd line of the CSV.
	   fputcsv($handle, $month);
	   // 2. get the all time views of current user
	   $viewQueryText = $voteQueryText = '';
	   if($asset_type != 0) {
		   $type = ($asset_type == 1)?"usecase":"accelerator";
		   $viewQueryText = "SELECT node_field_data.nid,count(node_field_data.nid) as total_views, MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year
			FROM node_field_data node_field_data
			INNER JOIN nodeviewcount nodeviewcount ON nodeviewcount.nid = node_field_data.nid 
			INNER JOIN node__field_usecase_or_accelerator type ON node_field_data.nid = type.entity_id
			WHERE node_field_data.uid = {{$uid}} AND (type.field_usecase_or_accelerator_value = '{$type}') 
			AND (node_field_data.moderation_state = 'published')
			GROUP BY year,month,node_field_data.nid";
			
		   $voteQueryText = "SELECT node_field_data.nid,count(votingapi_vote.entity_id) as total_votes, MONTH(FROM_UNIXTIME(timestamp)) AS month,YEAR(FROM_UNIXTIME(timestamp)) AS year
			FROM node_field_data node_field_data
			INNER JOIN votingapi_vote votingapi_vote ON votingapi_vote.entity_id = node_field_data.nid 
			INNER JOIN node__field_usecase_or_accelerator type ON node_field_data.nid = type.entity_id
			WHERE node_field_data.uid = {{$uid}} AND (type.field_usecase_or_accelerator_value = '{$type}') 
			AND (node_field_data.moderation_state = 'published')
			GROUP BY year,month,node_field_data.nid";
	   }else{
		   $viewQueryText = "
			SELECT node_field_data.nid,count(node_field_data.nid) as total_views, MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year
			FROM node_field_data node_field_data
			INNER JOIN nodeviewcount nodeviewcount ON nodeviewcount.nid = node_field_data.nid 
			WHERE node_field_data.uid = {{$uid}} 
			AND (node_field_data.moderation_state = 'published')
			GROUP BY year,month,node_field_data.nid";
			
		   $voteQueryText = "
			SELECT node_field_data.nid,count(entity_id) as total_votes, MONTH(FROM_UNIXTIME(timestamp)) AS month,YEAR(FROM_UNIXTIME(timestamp)) AS year
			FROM node_field_data node_field_data
			INNER JOIN votingapi_vote votingapi_vote ON votingapi_vote.entity_id = node_field_data.nid 
			WHERE node_field_data.uid = {{$uid}} 
			AND (node_field_data.moderation_state = 'published')
			GROUP BY year,month,node_field_data.nid";
	   }
		$viewQuery = $this->database->query($viewQueryText);
		$viewsCounts = $viewQuery->fetchAll();
		// 2.1 seperate each nid views 
		foreach($viewsCounts as $viewsCount) {
			$totalViewsNids[$viewsCount->nid][$viewsCount->month."-".$viewsCount->year] = $viewsCount->total_views;
		}
		// 3. get the all time votes of current user
		$voteQuery = $this->database->query($voteQueryText);
		$votesCounts = $voteQuery->fetchAll();
		// 3.1 seperate each nid votes 
		foreach($votesCounts as $votesCount) {
			$totalVotesNids[$votesCount->nid][$votesCount->month."-".$votesCount->year] = $votesCount->total_votes;
		}
		// 4. create the no of rows for current user from 1st published assets to till date
		
		$totalMonths = $this->getNoOfMonths($first_assets_published_date,strtotime('today'));
		foreach($totalMonths as $month => $name) {
			$rows = [];
			$rows[] = $name;
			foreach($nids as $k => $nid) {
				if(isset($totalViewsNids[$nid][$month])) {
					$rows[] = $totalViewsNids[$nid][$month];
				}else {
					$rows[] = 0;
				}
				if(isset($totalVotesNids[$nid][$month])) {
					$rows[] = $totalVotesNids[$nid][$month];
				}else {
					$rows[] = 0;
				}
			}
			fputcsv($handle, $rows);
		} 
	   // Reset where we are in the CSV.
	   rewind($handle);
	   
	   // Retrieve the data from the file handler.
	   $csv_data = stream_get_contents($handle);

	   // Close the file handler since we don't need it anymore.  We are not storing
	   // this file anywhere in the filesystem.
	   fclose($handle);
		
	   // This is the "magic" part of the code.  Once the data is built, we can
	   // return it as a response.
	   $response = new Response();

	   // By setting these 2 header options, the browser will see the URL
	   // used by this Controller to return a CSV file called "statistics-report.csv".
	   $response->headers->set('Content-Type', 'text/csv');
	   $response->headers->set('Content-Disposition', 'attachment; filename="statistics-report.csv"');

	   // This line physically adds the CSV data we created 
	   $response->setContent($csv_data);

	   return $response;
	}
	
	/**
	* generate single node statistics csv report
	*/
	public function singleAssetBuild($nid = 0) {
		$totalViewsNids = $totalVotesNids = [];
		// 1. get node published date and title
		$query = $this->database->query("
			SELECT title,node_field_data.created as published_on
			FROM node_field_data node_field_data
			WHERE ((node_field_data.nid = {$nid}))");
		$singleAsset = $query->fetchAll();
		
		$handle = fopen('php://temp', 'w+');
		// Add the header as the 1st line of the CSV.
		fputcsv($handle, array('Asset Name',$singleAsset[0]->title));
		// Add the publishedDate as the 2nd line of the CSV.
		fputcsv($handle, array('Published On',date('d-m-Y',$singleAsset[0]->published_on)));
		// Add the Month,views and votes name as the 3rd line of the CSV.
		fputcsv($handle, array('Date','Views','Votes'));
		
		// 2. get views 
		$viewQuery = $this->database->query("
			SELECT DATE(FROM_UNIXTIME(datetime)) as viewDateOnly,count(nid) as total_views 
			FROM nodeviewcount 
			WHERE nid = {$nid} GROUP BY viewDateOnly");
		$viewsCounts = $viewQuery->fetchAll();
		
		foreach($viewsCounts as $viewsCount) {
			$totalViewsNids[$viewsCount->viewDateOnly] = $viewsCount->total_views;
		}
		//\Drupal::Logger('ai_users_statistics')->notice("views => ".print_r($totalViewsNids,true));
		// 3. get votes 
		$voteQuery = $this->database->query("
			SELECT DATE(FROM_UNIXTIME(timestamp)) as voteDateOnly,count(entity_id) as total_votes 
			FROM votingapi_vote  vote
			WHERE vote.entity_id = {$nid} GROUP BY voteDateOnly");
		$votesCounts = $voteQuery->fetchAll();
		
		foreach($votesCounts as $votesCount) {
			$totalVotesNids[$votesCount->voteDateOnly] = $votesCount->total_votes;
		}
		//\Drupal::Logger('ai_users_statistics')->notice("votesCounts => ".print_r($totalVotesNids,true));
		// 4. create the no of rows for current user from 1st published assets to till date
		$totalDays = $this->getNoOfDays($singleAsset[0]->published_on,strtotime('today'));
		foreach($totalDays as $key => $date) {
			$rows = [];
			$rows[] = $date;
			if(isset($totalViewsNids[$key])) {
				$rows[] = $totalViewsNids[$key];
			}else {
				$rows[] = 0;
			}
			if(isset($totalVotesNids[$key])) {
				$rows[] = $totalVotesNids[$key];
			}else {
				$rows[] = 0;
			}
			fputcsv($handle, $rows);
		} 
		// Reset where we are in the CSV.
	   rewind($handle);
	   $csv_data = stream_get_contents($handle);

	   fclose($handle);
	   
	   $response = new Response();
	   $response->headers->set('Content-Type', 'text/csv');
	   $response->headers->set('Content-Disposition', 'attachment; filename="asset-statistics-report.csv"');

	   // This line physically adds the CSV data we created 
	   $response->setContent($csv_data);

	   return $response;
	}
	/*
	* get number of months between 2 days
	* @param date1 and date2
	*/
	function getNoOfDays($current,$last){
		$days = [];
		while( $current <= $last ) {
			$days[date("Y-m-d", $current)] = date("d-n-Y", $current); 
			$current = strtotime("+1 day", $current);
		}
		if(empty($days)){
			$days[date("Y-m-d", $current)] = date("d-n-Y", $current); 
		}
		return $days;
	}
	/*
	* get number of months between 2 days
	* @param date1 and date2
	*/
	function getNoOfMonths($date1,$date2){
		$monthName = [];
		$time   = $date1;
		$last   = date('M-Y', $date2);

		do {
			$month = date('M-Y', $time);
			$monthName[date('n-Y', $time)] = $month;
			$time = strtotime('+1 month', $time);
		} while ($month != $last);

		return $monthName;
	}
}