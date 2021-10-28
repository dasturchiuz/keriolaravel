// Load the Visualization API and the piechart package.
google.load('visualization', '1.0', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChart);

/**
 * Draw chart with given data
 *
 * @param	void
 * @return	void
 */
function drawChart() {
	var
		data = new google.visualization.DataTable(),
		chartData = this.chartData;

	//prepare data for the chart
	data.addColumn('string', 'type');
	data.addColumn('number', 'count');

	data.addRows([
 		['Free (' + chartData.free + ' GB)', chartData.free],
 		['Occupied (' + chartData.occupied + ' GB)', chartData.occupied]
	]);

	// Set chart options
	var options = {
  		title: 'Storage occupied (total ' + Math.round(chartData.free + chartData.occupied) + ' GB)',
		width: 700,
		height: 400,
		colors: ['green', 'red'],
		is3D: true
	};

	// Instantiate and draw our chart, passing in some options.
	var chart = new google.visualization.PieChart(document.getElementById('statistics'));
	chart.draw(data, options);
}

/**
 * Store data for chart
 *
 * @param	array   data for chart
 * @return	void
 */
function setData(data) {
	this.chartData = data;
}