
(function ($, Drupal) {
  Drupal.behaviors.time_spent = {
    attach: function(context, settings) {
		
		/**Statistics select filter changes submit function**/
		$("#views-exposed-form-my-content-statistics-page-1 .form-item-field-usecase-or-accelerator-value .select-wrapper select").change(function() {
			$('form#views-exposed-form-my-content-statistics-page-1').submit(); 
		});
		
		// find the active div tab on click
		$(".find-active").unbind().click(function(){
			var active_div_tab  = $(this).data('activetab');
			$("#active-div-tab").val(active_div_tab);
			// this is to fix the design issue of vote graph on page load
			getTabChartData();
		});
		// call user assests statistics from ajax
		$("#ai-users-statistics-id .left-card-sub-container-one").unbind().click(function(){ 
			
			var nid = $(this).attr("id");
			$("#selected_nid").val(nid);
			var chartDuration = $("#chart-duration-select").val();
			
			getChartData(nid,chartDuration);
		});
		
		
		$("#chart-duration-select").unbind().change(function() {
			getTabChartData();
		});
		// common to function to load graph data
		function getTabChartData() {
			var isViewActive = 1;
			var active_tab = $("#active-div-tab").val();
			if( active_tab == "votes"){
				isViewActive = 0;
			}
			var nid = $("#selected_nid").val();
			var chartDuration = $("#chart-duration-select").val();
			var published_on = $('#published_on').val()
			$.ajax({
				type: "POST",
				url: drupalSettings.ai_users_statistics_ajax_duration_graph.callbackUrlDuration,
				data: {'nid':nid,'duration':chartDuration,'published_on':published_on,'isViewActive':isViewActive},
				dataType: 'json',
				success: function(data){ 
					if(active_tab == "votes"){
						// $("#statistics-ratings-graph").show();
						// $("#statistics-views-graph").hide();
						// $(".right-card-ratings-wrapper").removeClass("rating-not-selected");
						// $(".right-card-views-wrapper").addClass("rating-not-selected");
						$("#statistics-ratings-graph").show();
						$("#statistics-views-graph").hide();
						$('.right-card-views-wrapper').addClass('view-not-selected');
						$('.right-card-ratings-wrapper').removeClass('rating-not-selected');
					
					}else if(active_tab == "views"){
						// $("#statistics-views-graph").show();
						// $("#statistics-ratings-graph").hide();
						// $(".right-card-views-wrapper").removeClass("rating-not-selected");
						// $(".right-card-ratings-wrapper").addClass("rating-not-selected");
						$("#statistics-views-graph").show();
						$("#statistics-ratings-graph").hide();
						$('.right-card-views-wrapper').removeClass('view-not-selected');
						$('.right-card-ratings-wrapper').addClass('rating-not-selected');
					}
					ContentColumnChart(data,chartDuration,data.activeChart);
				},
				beforeSend: function(xhr){
					$("#load_only_graph").show();
					$("#ai-users-statistics-counts .right-card-section-two, #chart-duration-select").addClass("blur");
				},
				complete: function(data) {
					$("#load_only_graph").hide();
					$("#ai-users-statistics-counts .right-card-section-two, #chart-duration-select").removeClass("blur");
				}
			});
		}
		function getChartData(nid,chartDuration) {
			$.ajax({
				type: "POST",
				url: drupalSettings.ai_users_statistics_graph.callbackUrl,
				data: {'nid':nid,'duration':chartDuration},
				dataType: 'json',
				success: function(data){ 
					$("#ai-users-view-number").html(data.views_count);
					$("#ai-users-ratings").html("( "+data.rating+" Ratings )");
					$("#ai-users-vote").html(data.total_votes);
					$('#published_on').val(data.published_on);
					$('#ai-users-statistics-id .views-row.view-content.featured-content div.left-card-sub-container-one').removeClass('statisticsSelectedContent');
					$('#'+nid).addClass('statisticsSelectedContent');
					ContentColumnChart(data,chartDuration,'views');
				},
				beforeSend: function(xhr){
					$("#ai-users-statistics-counts").addClass("blur");
					$("#load_full_right").show();
				},
				complete: function(data) {
					// remove hide class and add show class
					$("#ai-users-statistics-counts").removeClass("hide").addClass("show");
					$("#ai-users-statistics-counts").removeClass("blur");
					$("#load_full_right").hide();
				}
			});
		}
		function ContentColumnChart(chartData,optionsValue,activeChart) {
			google.charts.load('current', {'packages':['corechart', 'bar']});
			switch(activeChart) {
				case 'views':
					google.charts.setOnLoadCallback(function () {
						drawViewCountChart(chartData.viewsData,optionsValue);
					});
					
				break;
				case 'votes':
				/*
					$(window).resize(function(){
					  drawVoteCountChart(chartData.votesData,optionsValue);
					}); */
					google.charts.setOnLoadCallback(function () {
						drawVoteCountChart(chartData.votesData,optionsValue);
					});
				break;
				default:
					google.charts.setOnLoadCallback(function () {
						drawViewCountChart(chartData.viewsData,optionsValue);
					});
					google.charts.setOnLoadCallback(function () {
						drawVoteCountChart(chartData.votesData,optionsValue);
					});
				break;
			}
			return false;  
		}
		function drawViewCountChart(chartData,optionsValue) {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Date');
			data.addColumn('number', 'Views');
			$.each(chartData, function(i, chartData){
				var date = chartData.date;
				var view = chartData.view;
				data.addRows([[date, view]]);
			});
			var options = {
				hAxis: {
					title: charthAxisTitle(optionsValue)
				},
				vAxis: {
					title: 'Views',
					viewWindow: { min:0 }
				}
			};
			var chart = new google.visualization.ColumnChart(document.getElementById('view_container'));
			chart.draw(data, options);  
		}
		function drawVoteCountChart(chartData,optionsValue) {
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Date');
			data.addColumn('number', 'Votes');
			$.each(chartData, function(i, chartData){
				var date = chartData.date;
				var vote = chartData.vote;
				data.addRows([[date, vote]]);
			});
			var options = {
				hAxis: {
					title: charthAxisTitle(optionsValue)
				},
				vAxis: {
					title: 'Votes',
					viewWindow: { min:0 }
				}
			};
			var chart = new google.visualization.ColumnChart(document.getElementById('vote_container'));
			chart.draw(data, options);  
		}
		
		function charthAxisTitle(optionsValue) {
			var hAxis_title = '';
			switch(optionsValue){
				case '1' : hAxis_title = 'Date';
				break;
				case '2' : hAxis_title = 'Week';
				break;
				case '3' : hAxis_title = 'Month';
				break;
				case '4' : hAxis_title = 'Year';
				break;
			}
			return hAxis_title;
		}
    }
  };
})(jQuery, Drupal);
