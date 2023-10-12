<?php

namespace Drupal\ai_users_report\Commands;

use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class AiUsersReportCommands extends DrushCommands {

  /**
   * Command description here.
   *
   * @param $reporttype
   *   Argument description.ame
   *   Description
   * @usage ai_users_report-createuserreport dailyreport
   *   Usage description
   *
   * @command ai_users_report:createuserreport
   * @aliases dailyreport
   */
  public function createuserreport($reporttype) {
    switch($reporttype){
		case "dailyreport":
			$this->ai_users_report_daily_tracker();
			$this->logger()->success(dt('Daily Report'));
		break;
		case "monthlyreport";
			$this->ai_users_report_monthly_tracker();
			$this->logger()->success(dt('Monthly Report'));
		break;
	}
  }
  /**
  * count the no of daily login users and 
  * store in the ai_user_daily_login table
  */
function ai_users_report_daily_tracker() {
	\Drupal::logger('ai_user_report')->log('debug', 'Daily Cron run time '.time());
	$server_time = $this->ai_users_report_server_time(1);
	$start_time = $server_time['start_time'];
	$end_time = $server_time['end_time'];
	$database = \Drupal::database();
	$query = $database->query("SELECT uid,access  FROM users_field_data WHERE access >= {$start_time} and access <= {$end_time}");
	$login_users = $query->fetchAll();	
	if(count($login_users) > 0){
		$query = $database->insert('ai_user_daily_login')
		->fields(['uid','login_date','year', 'month',]);
		foreach ($login_users as $record) {
		  $query->values(array($record->uid,$record->access,date('Y'),date('m')));
		}
		$query->execute();
	}
}

/**
  * count the no of new logins and 
  * store in the ai_user_report_daily_login table
  */  
function ai_users_report_monthly_tracker() {
	\Drupal::logger('ai_user_report')->log('debug', 'Monthly Cron run time '.time());
	$database = \Drupal::database();
	$transaction = $database->startTransaction();
	try{
		$server_time = $this->ai_users_report_server_time(2);
		$start_month_time = $server_time['start_month_time'];
		$end_month_time = $server_time['end_month_time'];
		// get total registered users in this month
		$query = $database->query("SELECT COUNT(uid) as unique_visit FROM users_field_data WHERE created >= {$start_month_time} and created <= {$end_month_time}");
		$unique_visit = $query->fetchAll();
		
		// get total registered users cumulative_unique_visit
		$query3 = $database->query("SELECT COUNT(uid) as cumulative_unique_visit FROM users_field_data WHERE created >= '1577836800'");
		$cumulative_unique_visit = $query3->fetchAll();
		
		
		// get total visit of the month total_logins
		$query2 = $database->query("SELECT COUNT(*) as all_visit FROM ai_user_daily_login WHERE login_date >= {$start_month_time} and login_date <= {$end_month_time}");
		$all_visit = $query2->fetchAll();
		
		// get total cumulative_visit 
		$query2 = $database->query("SELECT COUNT(*) as cumulative_visit FROM ai_user_daily_login");
		$cumulative_visit = $query2->fetchAll();
		
		// check if record of the month exists
		$month = date('m');
		$year = date('Y');
		$query2 = $database->query("SELECT COUNT(*) as record_exist FROM ai_user_month_report WHERE month = {$month} and year = {$year}");
		$record_exist = $query2->fetchAll();
		
		$values = array(
			'year' => date('Y'),
			'month' => date('m'),
			'unique_visit' => $unique_visit[0]->unique_visit,
			'cumulative_unique_visit' => $cumulative_unique_visit[0]->cumulative_unique_visit,
			'all_visit' => $all_visit[0]->all_visit,
			'cumulative_visit' => $cumulative_visit[0]->cumulative_visit,
		);
		if($record_exist[0]->record_exist > 0) {
			$values['updated_date'] = strtotime('today');
			// upate into ai_user_month_report table
			$database->update('ai_user_month_report')
			->fields($values)->condition('month',$month)->condition('year',$year)->execute();
		}else{
			$values['created_date'] = strtotime('today');
			// insert into ai_user_month_report table
			$database->insert('ai_user_month_report')->fields($values)->execute();
		}
		
	}catch (\Exception $e) {
		$transaction->rollback();
		watchdog_exception($e->getMessage(), $e);
    }
	
}

/**
 * Get start and end of the today server time
 */
 function ai_users_report_server_time($case) {
	$server_time = [];
	switch($case) {
		case 1: // Start of today's server time
			$server_time['start_time'] = strtotime(date("Y-m-d 00:00:01"));
			// End of today's server time
			$server_time['end_time'] = strtotime(date("Y-m-d 23:59:59"));
			break;
			
		case 2: 
			$server_time['start_month_time'] = strtotime(date("Y-m-01 00:00:01"));
			//Last date of current month.
			$server_time['end_month_time'] = strtotime(date("Y-m-t 23:59:59"));
	}
	return $server_time;
	
 }
}
