<?php

/**
 * @file
 * Contains \Drupal\ai_users_report\Controller\userReportController.
 */

namespace Drupal\ai_users_statistics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Controller routines for user routes.
 */
class usersStatisticsController extends ControllerBase {
	protected $database;

  public function __construct() {
    $this->database = \Drupal::database();
  }

  /**
   * Saving time spent on a node.
   * @param $nid
   */
	public function usersStatisticsAjaxgraph() {

		$nid = $_REQUEST['nid'];
		$query = $this->database->query("SELECT node_counter.totalcount AS views_count,field_rate_rating as rating,node_field_data.created as published_on,vote.value as total_votes
									FROM node_field_data node_field_data
									LEFT JOIN node_counter node_counter ON node_field_data.nid = node_counter.nid
									LEFT JOIN node__field_rate node__field_rate ON node_field_data.nid = node__field_rate.entity_id
									LEFT JOIN votingapi_result vote ON node_field_data.nid = vote.entity_id
									WHERE ((node_field_data.nid = {$nid})) 
									AND (node_field_data.type IN ('use_case_or_accelerator')) AND vote.function = 'vote_count'");
		$counts = $query->fetchAll();
				
		$rating = round(($counts[0]->rating/100) * 5, 1);
		$duration = $_REQUEST['duration'];
		$published_on = $counts[0]->published_on;
		$viewsData = $this->commonViewsDurationData($nid,$duration,$published_on);
		//$votesData = $this->commonVotesDurationData($nid,$duration,$published_on);
		return new JsonResponse(array('views_count' => $counts[0]->views_count,'rating'=>$rating,'total_votes' => $counts[0]->total_votes,'published_on' => $published_on,'viewsData' => $viewsData));
	}
  
	
	public function usersStatisticsAjaxDurationgraph() {
		$nid = $_REQUEST['nid'];
		$duration = $_REQUEST['duration'];
		$published_on = $_REQUEST['published_on'];
		if($_REQUEST['isViewActive']){
			// call view count query
			$viewsData = $this->commonViewsDurationData($nid,$duration,$published_on);
			return new JsonResponse(array('viewsData' => $viewsData, 'activeChart' => 'views'));
		}else{
			// call vote count query
			$votesData = $this->commonVotesDurationData($nid,$duration,$published_on);
			return new JsonResponse(array('votesData' => $votesData, 'activeChart' => 'votes'));
		}
		
	}
	
	public function commonViewsDurationData($nid,$duration,$published_on) {
		$viewsData = [];
		switch($duration) {
			case 1: // get last 7 days from current date view count
				$_last7days = strtotime('-7 days');
				$viewQuery = $this->database->query("SELECT DATE(FROM_UNIXTIME(datetime)) as viewDateOnly,count(nid) as total_views FROM nodeviewcount WHERE nid = {$nid} and datetime >= {$_last7days} GROUP BY viewDateOnly");
				$viewsCount = $viewQuery->fetchAll();
				$last_7days_dates = $this->getLastNDays(7, 'Y-m-d');
				foreach($viewsCount as $views) {
					$d_m_Y_format = date("d M", strtotime($views->viewDateOnly));
					$viewsData[strtotime($views->viewDateOnly)] = array(
						'date' => $d_m_Y_format,
						'view' => (int)$views->total_views
					);
					$key = array_search ($views->viewDateOnly, $last_7days_dates);
					unset($last_7days_dates[$key]);
				}
				foreach($last_7days_dates as $day){
					$d_m_Y_format = date("d M", strtotime($day));
					$viewsData[strtotime($day)] = array(
						'date' => $d_m_Y_format,
						'view' => 0
					);
				}
			break;
			
			case 2: // get last 1 month from current date view count
				$last_30days = strtotime('-30 days');
				$query = "SELECT count(nid) as total_views, WEEK(FROM_UNIXTIME(datetime)) AS Week
							FROM nodeviewcount WHERE datetime >= {$last_30days} and nid = {$nid}
							GROUP BY Week";
				$viewQuery = $this->database->query($query);
				$viewsCount = $viewQuery->fetchAll();
				
				$today = strtotime('today');
				$noOfWeeks = $this->getNoOfWeeks($today,$last_30days);
				$startWeek = date('W', $last_30days);
				$endWeek = date('W',$today);
				$week_number = [];
				
				for($i = $startWeek; $i <= $endWeek; $i++){
					$week_number[] = $i;
				}
				
				foreach($viewsCount as $views) {
					$dates = $this->getStartAndEndDateOfWeek($views->Week,date('Y'));
					$viewsData["Week".$views->Week] = array(
						'date' => $dates['start_date']." \n ".$dates['end_date'],
						'view' => (int)$views->total_views
					);
					$key = array_search ($views->Week, $week_number);
					unset($week_number[$key]);
				}
				foreach($week_number as $week){
					$dates = $this->getStartAndEndDateOfWeek($week,date('Y'));
					$viewsData["Week".$week] = array(
						'date' => $dates['start_date']." \n".$dates['end_date'],
						'view' => 0
					);
				}
			break;
			
			case 3: // get last 3 month view count
				$last_3months = strtotime('-3 month');
				$query = "SELECT count(nid) as total_views, MONTH(FROM_UNIXTIME(datetime)) AS month,YEAR(FROM_UNIXTIME(datetime)) AS year
							FROM nodeviewcount WHERE datetime >= {$last_3months} and nid = {$nid}
							GROUP BY year,month";
				$viewQuery = $this->database->query($query);
				$viewsCount = $viewQuery->fetchAll();
				$last3months = $this->lastThreeMonths();
				foreach($viewsCount as $views) {
					$viewsData[$views->month] = array(
						'date' => date('F', mktime(0, 0, 0, $views->month, 10))." ".$views->year,
						'view' => (int)$views->total_views
					);
					unset($last3months[$views->month]);
				}
				foreach($last3months as $month_num => $name){
					$viewsData[$month_num] = array(
						'date' => $name,
						'view' => 0
					);
				}
			break;
			
			case 4: // all time
				$query = "SELECT count(nid) as total_views, YEAR(FROM_UNIXTIME(datetime)) AS year
							FROM nodeviewcount WHERE nid = {$nid}
							GROUP BY year";
				$viewQuery = $this->database->query($query);
				$viewsCount = $viewQuery->fetchAll();
				
				$current_year = date('Y');
				$years = [];
				for($i = $published_on; $i <= $current_year; $i++){
					$years[] = $i;
				}
				
				foreach($viewsCount as $views) {
					$viewsData[$views->year] = array(
						'date' => $views->year,
						'view' => (int)$views->total_views
					);
					$key = array_search ($views->year, $years);
					unset($years[$key]);
				}
				
				foreach($years as $year){
					$viewsData[$year] = array(
						'date' => $year,
						'view' => 0
					);
				}
				
			break;
			default:
		}
		ksort($viewsData);
		return $viewsData;
	}
	
	public function commonVotesDurationData($nid,$duration,$published_on) {
		$votesData = [];
		switch($duration) {
			case 1: // get last 7 days from current date vote count
				$_last7days = strtotime('-7 days');
				$voteQuery = $this->database->query("SELECT DATE(FROM_UNIXTIME(timestamp)) as voteDateOnly,count(entity_id) as total_votes FROM votingapi_vote  vote
				WHERE vote.entity_id = {$nid} AND timestamp >= {$_last7days} GROUP BY voteDateOnly");
				$votesCount = $voteQuery->fetchAll();
				$last_7days_dates = $this->getLastNDays(7, 'Y-m-d');
				foreach($votesCount as $votes) {
					$d_m_Y_format = date("d M", strtotime($votes->voteDateOnly));
					$votesData[strtotime($votes->voteDateOnly)] = array(
						'date' => $d_m_Y_format,
						'vote' => (int)$votes->total_votes
					);
					$key = array_search ($votes->voteDateOnly, $last_7days_dates);
					unset($last_7days_dates[$key]);
				}
				foreach($last_7days_dates as $day){
					$d_m_Y_format = date("d M", strtotime($day));
					$votesData[strtotime($day)] = array(
						'date' => $d_m_Y_format,
						'vote' => 0
					);
				}
			break;
			
			case 2: // get last 1 month from current date view count
				$last_30days = strtotime('-30 days');
				$query = "SELECT count(entity_id) as total_votes, WEEK(FROM_UNIXTIME(timestamp)) AS Week
							FROM votingapi_vote vote WHERE vote.timestamp >= {$last_30days} AND vote.entity_id = {$nid}
							GROUP BY Week";
				$voteQuery = $this->database->query($query);
				$votesCount = $voteQuery->fetchAll();
				
				$today = strtotime('today');
				$noOfWeeks = $this->getNoOfWeeks($today,$last_30days);
				$startWeek = date('W', $last_30days);
				$endWeek = date('W',$today);
				$week_number = [];
				
				for($i = $startWeek; $i <= $endWeek; $i++){
					$week_number[] = $i;
				}
				
				foreach($votesCount as $votes) {
					$dates = $this->getStartAndEndDateOfWeek($votes->Week,date('Y'));
					$votesData["Week".$votes->Week] = array(
						'date' => $dates['start_date']." \n ".$dates['end_date'],
						'vote' => (int)$votes->total_votes
					);
					$key = array_search ($votes->Week, $week_number);
					unset($week_number[$key]);
				}
				foreach($week_number as $week){
					$dates = $this->getStartAndEndDateOfWeek($week,date('Y'));
					$votesData["Week".$week] = array(
						'date' => $dates['start_date']." \n".$dates['end_date'],
						'vote' => 0
					);
				}
			break;
			
			case 3: // get last 3 month vote count
				$last_3months = strtotime('-3 month');
				$query = "SELECT count(entity_id) as total_votes, MONTH(FROM_UNIXTIME(timestamp)) AS month,YEAR(FROM_UNIXTIME(timestamp)) AS year
							FROM votingapi_vote vote WHERE vote.timestamp >= {$last_3months} and vote.entity_id = {$nid}
							GROUP BY year,month";
				$voteQuery = $this->database->query($query);
				$votesCount = $voteQuery->fetchAll();
				$last3months = $this->lastThreeMonths();
				foreach($votesCount as $votes) {
					$votesData[$votes->month] = array(
						'date' => date('F', mktime(0, 0, 0, $votes->month, 10))." ".$votes->year,
						'vote' => (int)$votes->total_votes
					);
					unset($last3months[$votes->month]);
				}
				foreach($last3months as $month_num => $name){
					$votesData[$month_num] = array(
						'date' => $name,
						'vote' => 0
					);
				}
			break;
			
			case 4: // all time
				$query = "SELECT count(entity_id) as total_votes, YEAR(FROM_UNIXTIME(timestamp)) AS year
							FROM votingapi_vote vote WHERE vote.entity_id = {$nid}
							GROUP BY year";
				$voteQuery = $this->database->query($query);
				$votesCount = $voteQuery->fetchAll();
				
				
				$current_year = date('Y');
				$years = [];
				for($i = $published_on; $i <= $current_year; $i++){
					$years[] = $i;
				}
				
				foreach($votesCount as $votes) {
					$votesData[$votes->year] = array(
						'date' => $votes->year,
						'vote' => (int)$votes->total_votes
					);
					$key = array_search ($votes->year, $years);
					unset($years[$key]);
				}
				
				foreach($years as $year){
					$votesData[$year] = array(
						'date' => $year,
						'vote' => 0
					);
				}
				
			break;
			default:
		}
		ksort($votesData);
		return $votesData;
	}
	
	function getLastNDays($days, $format = 'd/m'){
		$m = date("m"); $de= date("d"); $y= date("Y");
		$dateArray = array();
		for($i=0; $i<=$days-1; $i++){
			$dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y)); 
		}
		return array_reverse($dateArray);
	}
	
	/*
	* get number of weeks between 2 days
	* @param date1 and date2
	*/
	function getNoOfWeeks($date1,$date2){
		$HowManyWeeks = date( 'W', $date1 ) - date( 'W', $date2 );
		return $HowManyWeeks;
	}
	
	/**
	* get last 3 months
	*/
	function lastThreeMonths() {
		return array(
			ltrim(date('m'),'0') => date('F Y', time()),
			ltrim(date('m', strtotime('-1 month')),'0') => date('F Y', strtotime('-1 month')),
			ltrim(date('m', strtotime('-2 month')),'0') => date('F Y', strtotime('-2 month'))
		);
	}
	
	/*
	* @param week number and year
	*/
	function getStartAndEndDateOfWeek($week, $year) {
	    $dates['start_date'] = date('d-M', strtotime("$year-W$week-1"));
		$dates['end_date'] = date('d-M', strtotime('+6 days', strtotime($dates['start_date'])));
		return $dates;
	}
}
