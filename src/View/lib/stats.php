<?php
/*
 * Copyright (C) 2004-2017 Soner Tari
 *
 * This file is part of UTMFW.
 *
 * UTMFW is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * UTMFW is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with UTMFW.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Prints pie chart on a modal.
 */
function PrintModalPieChart()
{
	?>
	<!-- The Modal -->
	<div id="modalPieChart" class="modal">
		<!-- The Close Button -->
		<span class="close" onclick="document.getElementById('modalPieChart').style.display='none'">&times;</span>
	</div>

	<div id="slicetip" class="hidden">
		<p><strong><span id="title"></span></strong></p>
		<p><span id="key">200</span></p>
		<p><span id="value">200</span></p>
	</div>

	<script type="text/javascript">
		// Get the modal
		var modal = document.getElementById('modalPieChart');

		function generateChart(dataset, title) {
			modal.style.display = 'block';

			var keys = d3.keys(dataset);
			var values = d3.values(dataset);

			var dataSum = d3.sum(values);

			var modifiedDataset = JSON.parse('{}');
			var curSum = 0;
			for (var j = 0; j < keys.length; ++j) {
				if (j > 10) {
					modifiedDataset['others'] = dataSum - curSum;
					break;
				}
				modifiedDataset[keys[j]] = values[j];
				curSum += values[j];
			}

			var pie = d3.layout.pie();
			var w = 400;
			var h = 400;
			var outerRadius = w / 2;
			var innerRadius = 0;

			var arc = d3.svg.arc()
				.innerRadius(innerRadius)
				.outerRadius(outerRadius);

			// Create SVG element
			var svg = d3.select('#modalPieChart')
				.append('svg')
				.attr('id', 'pieChart')
				.attr('width', w)
				.attr('height', h)
				.attr('class', 'modal-content');

			// Set up groups
			var arcs = svg.selectAll('g.arc')
				.data(pie(d3.values(modifiedDataset)))
				.enter()
				.append('g')
				.attr('class', 'arc')
				.attr('transform', 'translate(' + outerRadius + ', ' + outerRadius + ')');

			var color = d3.scale.category10();

			// Draw arc paths
			arcs.append('path')
				.attr('fill', function (d, i) {
					return color(i);
				})
				.attr('d', arc)
				.on('mouseover', function(d, i) {
					d3.select(this)
						.attr('fill', 'gray');
					//Get this bar's x/y values, then augment for the tooltip
					var xPosition = d3.mouse(document.body)[0] + 25;
					var yPosition = d3.mouse(document.body)[1] + 25;
					//Update the tooltip position and value
					d3.select('#slicetip')
						.style('left', xPosition + 'px')
						.style('top', yPosition + 'px')
						.style('z-index', 1)
						.select('#title')
						.text(title);
					d3.select('#slicetip')
						.select('#key')
						.text(d3.keys(modifiedDataset)[i]);
					d3.select('#slicetip')
						.select('#value')
						.text(d3.round(100 * d.value / dataSum) + '%: ' + d.value + '/' + dataSum);
					//Show the tooltip
					d3.select('#slicetip').classed('hidden', false);
				})
				.on('mouseout', function(d, i) {
					d3.select(this)
					.transition()
					.duration(500)
					.attr('fill', color(i));
					//Hide the tooltip
					d3.select('#slicetip').classed('hidden', true);
				});

			arcs.append('text')
				.attr('transform', function (d) {
					return 'translate(' + arc.centroid(d) + ')';
				})
				.attr('text-anchor', 'middle')
				.attr('font-weight', 'bold')
				.text(function (d, i) {
					return d3.keys(modifiedDataset)[i];
				});
		};

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName('close')[0];

		// When the user clicks on <span> (x), close the modal
		function close() {
			var chart = document.getElementById('pieChart');
			chart.parentNode.removeChild(chart);
			d3.select('#slicetip').classed('hidden', true);
			modal.style.display = 'none';
		}
		span.onclick = close;
		modal.onclick = close;
	</script>
	<?php
}

/**
 * Displays chart trigger images.
 * 
 * These images are initially hidden, we enable them using this JavaScript code.
 * So that if JavaScript is disabled, we don't show the triggers at all.
 * Pie charts need JavaScript being enabled.
 * 
 * @attention This function should be called at the end of the page, after the PHP code inserts the hidden images.
 */
function DisplayChartTriggers()
{
	?>
	<script>
		// Get the chart triggers
		var trigs = document.getElementsByClassName('chart-trigger');
		for (j = 0; j < trigs.length; ++j) {
			trigs[j].style.display = 'inline';
		}
	</script>
	<?php
}

/// Stats page warning message.
$StatsWarningMsg= _NOTICE('Analysis of statistical data may take a long time to process. Please be patient. Also note that if you refresh this page frequently, CPU load may increase considerably.');

/// Main help box used on all statistics pages.
$StatsHelpMsg= _HELPWINDOW('This page displays statistical data collected over the log files of this module.

You can change the date of statistics using drop-down boxes. An empty value means match-all. For example, if you choose 3 for month and empty value for day fields, the charts and lists display statistics for all the days in March. Choosing empty value for month means empty value for day field as well.

For single dates, Horizontal chart direction is assumed. For date ranges, default graph style is Daily, and direction is Vertical. Graph style can be changed to Hourly for date ranges, where cumulative hourly statistics are shown. In Daily style, horizontal direction is not possible.');

$Submenu= SetSubmenu('general');
require_once("../lib/stats.$Submenu.php");
?>
