/*
* @Author: stephan <m6ahenina@gmail.com>
* @Date:   2020-06-23 17:12:54
* @Last Modified by:   stephan <m6ahenina@gmail.com>
* @Last Modified time: 2020-06-23 17:58:13
*/
function StatisticsChartPlaceholderController ($scope, $filter, statisticsService) {

	$scope.options = {};

	this.$onInit = function () {
		if ($scope.$ctrl.chartType == 'bar') {
			$scope.options = statisticsService.generateBarPlaceholder(5);
		} else {
			$scope.options = statisticsService.generatePiePlaceholder(3);
		}
	};
};

StatisticsChartPlaceholderController.$inject = ['$scope', '$filter', 'statisticsService'];

angular.module('dashboardApp').component('appStatisticsChartPlaceholder', {
    templateUrl: 'statistics-chart-placeholder.html',
    controller: StatisticsChartPlaceholderController,
    bindings: {
		chartType: '=',
    }
});