<?php

/**
 * @file
 * Contains \Drupal\ai_users_report\Controller\userReportController.
 */

namespace Drupal\ai_users_report\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routines for user routes.
 */
class usersReportController extends ControllerBase {
	protected $database;

	public function __construct() {
		$this->database = \Drupal::database();
	}

	/**
	* @param $uid
	*/
	public function usersActivityReport($uid,Request $request) {
		$download = $request->query->get('download');
		// initialization
		$totalLogins = []; // no. of login in a month
		$totalNodes = []; // total no of nodes in a month
		$mostVisitedNode = []; // most visited node in a month
		// 1. Get user registered date
		$query = $this->database->select('users_field_data','u')->fields('u', ['created','name','access','mail'])
		->condition('uid',$uid, '=');
		$user_details = $query->execute()->fetchAll();
		
		// 2. Get no of logins 
		$loginsQuery = $this->database->query("SELECT count(uid) as total_login,month,year FROM ai_user_daily_login 
			WHERE uid = {$uid} GROUP BY month,year");
		$logins = $loginsQuery->fetchAll();
					
		// 2. 1 Create array of month and year login counts
		foreach($logins as $login){
			$totalLogins[$login->month."-".$login->year] = $login->total_login;
		}
		
		// 3. Get no of nodes visited
		$nodesQuery = $this->database->query("SELECT count(month) as nodes, month,year FROM (SELECT count(nid) as c,MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year FROM nodeviewcount 
			WHERE uid = {$uid} GROUP BY nid,month,year)  as new GROUP BY month,year");
		$nodes = $nodesQuery->fetchAll();
		// 3. 1 Create array of month and year node counts
		foreach($nodes as $node){
			$totalNodes[$node->month."-".$node->year] = $node->nodes;
		}
		
		// 4. Get Most visisted no
		$mostVisitQuery = $this->database->query("SELECT nid,count(nid) as node_count,MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year FROM nodeviewcount 
			WHERE uid = {$uid} GROUP BY nid,month,year ORDER BY node_count DESC");
		$mostVisits = $mostVisitQuery->fetchAll();
		// 4. 1 Create array of month and year most visited node
		foreach($mostVisits as $mostVisit){
			if(isset($mostVisitedNode[$mostVisit->month."-".$mostVisit->year])){
				// do nothing as we r fetching in in desc order of node count and first row of the month is the max
			}else{
				$mostVisitedNode[$mostVisit->month."-".$mostVisit->year] = $mostVisit->nid;
			}
		}
		
		// 5. Get no. of months from registered date to till date
		$months_asc = $this->getNoOfMonths($user_details[0]->created,strtotime('today'));
		$months = array_reverse($months_asc, true);
		
		// 5.1 Create headers and rows of table
		/*$header= array(
		  array('data' => t("Month"), 'field' => 'month', 'sort' => 'asc', ),
		  array('data' => t("Year"), 'field' => 'year', 'sort' => 'asc', ),
		  array('data' => t("No. Of Logins"), ),
		  array('data' => t("No.of nodes visited"),),
		  array('data' => t("Most visited node"),),
		); */
		$header = ['Year','Month','No. of Logins','No. of Nodes Visited','Most Visited URL'];
		$rows = [];
		$options = ['absolute' => TRUE];
		
		foreach($months as $month => $name) {
			$data = [];
			$month_year = explode("-",$name); 
			$data['year'] = $month_year[1]; // year           
			$data['month'] = $month_year[0]; // month name

			// no of login
			if(isset($totalLogins[$month])) {
				$data['total_login'] = $totalLogins[$month];
			}else{
				$data['total_login'] = 0;
			}
			// no of nodes
			if(isset($totalNodes[$month])) {
				$data['total_nodes'] = $totalNodes[$month];
			}else{
				$data['total_nodes'] = 0;
			}
			// most visited
			if(isset($mostVisitedNode[$month])) {
				//$data['most_visit'] = $mostVisitedNode[$month];
				$url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $mostVisitedNode[$month]], $options);
				$url = $url->toString();
				if(strtolower($download) == "yes"){
					$data['most_visit'] = $url;
				}else{
					$link = \Drupal\Core\Link::createFromRoute($url, 'entity.node.canonical', ['node' => $mostVisitedNode[$month]]);
					$data['most_visit'] = $link;
				}
				
			}else{
				$data['most_visit'] = 0;
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
		   $response->headers->set('Content-Disposition', 'attachment; filename="user-activity-report.csv"');

		   // This line physically adds the CSV data we created 
		   $response->setContent($csv_data);

		   return $response;
		}
		
		$user_name = $user_details[0]->name;
		$reg_date = date('d-M-Y h:m',$user_details[0]->created);
		$last_access = "Not yet login";
		if(!empty($user_details[0]->access)) {
			$last_access = date('d-M-Y h:m',$user_details[0]->access);
		}
		$user_mail = $user_details[0]->mail;
		$build['back']['#markup'] = "<a class='activity_user_calss' href='/user-activity-report'>Go Back</a>";
		$build['userdetails']['#markup'] = "<div class='activity-report-login-details'><div class='activity-summary'>User Activity Summary</div>
		<div class='wrapper'>
		<div class='user-name'>User : <span>{$user_name} ({$user_mail}) </span></div>
		<div class='registration-date'>Registered On : <span>{$reg_date}</span></div>
		<div class='last-login-date'>Last Login : <span>{$last_access}</span></div>
		</div>
	</div>";
		// $build['back']['#markup'] = "<a class='activity_user_calss' href='/user-activity-report'>Go Back</a>";
		$download_url = "href='/user-activity/{$uid}?page={$current_page}&download=yes'";
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


