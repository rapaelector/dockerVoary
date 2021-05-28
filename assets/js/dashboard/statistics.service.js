angular.module('dashboardApp').factory('statisticsService', ['$http', 'fosJsRouting', function ($http, fosJsRouting) {
    var _this = {};

	_this.generateRandomChartData = function (count, max, min) {
		max = max || 1000000;
		min = min || 100000;

		return new Array(count).fill(0).map(function (i) {
			return Math.round(Math.random() * max) + min;
		});
	};
	
	_this.generatePiePlaceholder = function (count) {
		var borderColor = '#FFFFFF';
		return {
			labels: new Array(count).fill('label'),
			data: _this.generateRandomChartData(count),
			colors: new Array(count).fill('#f2f2f2'),
			datasetOverride: {
				borderColor: new Array(count).fill(borderColor),
				hoverBorderColor: new Array(count).fill(borderColor),
				hoverBackgroundColor: new Array(count).fill('#f2f2f2'),
			},
			options: {
				plugins: {
					datalabels: false,
				},
				tooltips: {
					enabled: false,
				},
				borderColor: new Array(count).fill('#eeeeee'),
			},
		}
	};

	_this.generateBarPlaceholder = function (count) {
		var borderColor = '#ff000000';
		return {
			labels: new Array(count).fill('label'),
			data: _this.generateRandomChartData(count),
			colors: new Array(count).fill('#f2f2f2'),
			datasetOverride: {
				borderColor: new Array(count).fill(borderColor),
				hoverBorderColor: new Array(count).fill(borderColor),
				hoverBackgroundColor: new Array(count).fill('#f2f2f2'),
			},
			options: {
				plugins: {
					datalabels: false,
				},
				tooltips: {
					enabled: false,
				},
				scales: {
					xAxes: [{
						gridLines: {
							color: '#6c757d0d',
							zeroLineColor: '#6c757d0d',
						},
						barPercentage: 0.5,
						ticks: {
							display: false,
						}
					}],
					yAxes: [{
						gridLines: {
							color: '#6c757d0d',
							zeroLineColor: '#6c757d0d',
						},
						ticks: {
							display: false,
						}
					}],
				},
			},
		}
	};

    return _this;
}])