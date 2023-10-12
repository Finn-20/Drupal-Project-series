<?php


namespace Drupal\ai_solution_user_report\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routines for user routes.
 */
class solutionUserReportController extends ControllerBase {
	protected $database;

	public function __construct() {
		$this->database = \Drupal::database();
	}

	/**
	* @param $nid
	*/
	public function solutionActivityReport($nid, Request $request) {
		$download = $request->query->get('download');
		// initialization
		$totalViews = []; // total no of views in a month
		$totalVotes = []; // total no of votes in a month
		$uniqueVisiters = []; // most visited node in a month
		// 1. Get node published date
		$query = db_select('node_field_data','n')->fields('n', ['created','published_at','title', 'nid'])
		->condition('nid',$nid, '=');
		$node_details = $query->execute()->fetchAll();
		//$nid = $node_details[0]->nid; 
		
		// 2. Get the views of the node
		$viewQuery = "SELECT count(nid) as total_views, MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year
							FROM nodeviewcount WHERE nid = {$nid}
							GROUP BY year,month";
		$views_details = $this->database->query($viewQuery)->fetchAll();
		
		// 2. 1 Create array of month and year views counts
		foreach($views_details as $views){
			$totalViews[$views->month."-".$views->year] = $views->total_views;
		}
		
		// 3. Get the votes of the node
		$voteQuery = "SELECT count(entity_id) as total_votes, MONTH(FROM_UNIXTIME(timestamp)) AS month,YEAR(FROM_UNIXTIME(timestamp)) AS year
							FROM votingapi_vote vote WHERE vote.entity_id = {$nid}
							GROUP BY year,month";
		$voetes_details = $this->database->query($voteQuery)->fetchAll();
		
		// 3. 1 Create array of month and year votes counts
		foreach($voetes_details as $voetes){
			$totalVotes[$voetes->month."-".$voetes->year] = $voetes->total_votes;
		}
		
		// 4. Get the unique visiters in a month
		$visiterQuery = "SELECT YEAR(FROM_UNIXTIME(datetime)) as year,MONTH(FROM_UNIXTIME(datetime)) as month,uid 
			FROM `nodeviewcount` WHERE nid = {$nid} 
			GROUP BY year,month,uid";
		$visiters_details = $this->database->query($visiterQuery)->fetchAll();
		
		// 4. 1 Create array of month and year votes counts
		foreach($visiters_details as $visiters){
			if(isset($uniqueVisiters[$visiters->month."-".$visiters->year])){
				$uniqueVisiters[$visiters->month."-".$visiters->year] += 1;
			}else{
				$uniqueVisiters[$visiters->month."-".$visiters->year] = 1;
			}
		}
		
		// 4. Get no. of months from published date to till date
		// 4.1 check piblished date is there or not
		$start_date = $node_details[0]->created;
		if(!empty($node_details[0]->published_at)) {
			$start_date = $node_details[0]->published_at;
		}
		$months_asc = $this->getNoOfMonths($start_date,strtotime('today'));
		$months = array_reverse($months_asc, true);
		
		// 5.1 Create headers and rows of table
		$header = ['Year','Month','No. of views','No. of votes','Unique visitors'];
		$rows = [];
		
		foreach($months as $month => $name) {
			$data = [];
			$month_year = explode("-",$name); 
			$data['year'] = $month_year[1]; // year           
			$data['month'] = $month_year[0]; // month name

			// no of views
			if(isset($totalViews[$month])) {
				$data['total_views'] = $totalViews[$month];
			}else{
				$data['total_views'] = 0;
			}
			// no of votes
			if(isset($totalVotes[$month])) {
				$data['total_votes'] = $totalVotes[$month];
			}else{
				$data['total_votes'] = 0;
			}
			// no of unique visiters
			if(isset($uniqueVisiters[$month])) {
				$data['total_unique_visiters'] = $uniqueVisiters[$month];
			}else{
				$data['total_unique_visiters'] = 0;
			}
			$rows[] = $data;
		}
		// 6 Create theme to display records	
		$per_page = 15;
		// Initialize the pager
		$current_page = pager_default_initialize(count($rows), $per_page);
		// Split your list into page sized chunks
		$chunks = array_chunk($rows, $per_page, TRUE);
		
		if(strtolower($download) == "yes"){
			$handle = fopen('php://temp', 'w+');
			fputcsv($handle, $header);
			foreach($rows as $row) {
				fputcsv($handle, $row);
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
		   $response->headers->set('Content-Disposition', 'attachment; filename="solution-activity-report.csv"');

		   // This line physically adds the CSV data we created 
		   $response->setContent($csv_data);

		   return $response;
		}
		
		$title = $node_details[0]->title;
		$published = date('d-M-Y',$start_date);
		
		$build['back']['#markup'] = "<a class='activity_solution_class' href='/admin/reports/assets_report'>Go Back</a>";
		
		$build['nodedetails']['#markup'] = "<div class='activity-report-solution'><div class='solution-activity-summary'>Asset Activity Summary</div>
		<div class='wrapper'>
		<div class='asset-name'>Title : <span>{$title} </span></div>
		<div class='published-date'>Published On : <span>{$published}</span></div>
		</div>
		</div>";
		
		$download_url = "href='/solution-activity/{$nid}?page={$current_page}&download=yes'";
		$build['download']['#markup'] = "<a class='download-csv' {$download_url}>CSV</a>";
		
		
		// Generate the table.
		$build['table'] = array(
		  '#theme' => 'table',
		  '#header' => $header,
		  '#rows' => $chunks[$current_page],
		);
	 
		// Finally add the pager.
		$build['pager'] = array(
		  '#type' => 'pager'
		);
		 
		return $build;
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
