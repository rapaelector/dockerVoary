/*
* @Author: stephan <m6ahenina@gmail.com>
* @Date:   2020-06-16 07:08:52
* @Last Modified by:   stephan <m6ahenina@gmail.com>
* @Last Modified time: 2020-06-23 17:18:28
*/

import $ from 'jquery';
import Chart from 'chart.js/dist/Chart.min.js';

function StatisticsChartController ($scope, $filter, statisticsService, resolverService) {
    // var ctrl = this;
    var defaultColors = ['#1976d2', '#fd7a08', '#a6a3ae', '#ffeb3b', '#03a9f4', '#43a047', '#03a9f4'];
    $scope.chart = {
        colors: defaultColors,
    };

    this.$onInit = function () {
        if ($scope.$ctrl.chartColors) {
            $scope.chart.colors = $scope.$ctrl.chartColors;
        }
    }

    $scope.options = {};

    var defaultBarOptions = {
        scales: {
            xAxes: [{
                gridLines: {
                    display: false
                },
            }],
            yAxes: [{
                gridLines: {
                    display: true
                },
                ticks: {
                    beginAtZero: true,
                }
            }]
        }
    };

    $scope.$watch('$ctrl.chartColors', function () {
        if ($scope.$ctrl.chartColors) {
            $scope.chart.colors = $scope.$ctrl.chartColors;
        } else {
            $scope.chart.colors = defaultColors;
        }
    });

    $scope.$watch('$ctrl.chartData', function () {
        $scope.methods.updateOptions();
    });

    $scope.helpers = {};
    
    $scope.helpers.roundToTop = function (val) {
        return val.toString().split('').map(function (val, i) {
            return i == 0 ? parseInt(val) + 1 : 0;
        }).join('');
    };

    $scope.methods = {};

    $scope.methods.updateOptions = function () {
        var defaultOptions = {};
        if ($scope.$ctrl.chartType == 'bar') {
            defaultOptions = $scope.methods.buildBarDefaultsOptions();
        } else {
            defaultOptions = $scope.methods.buildDefaultsOptions();
        }

        $scope.options = $.extend(true, {}, defaultOptions, $scope.$ctrl.chartOptions, {
            borderColors: ['#FF0000', '#00FF00', '#0000FF'],
            borderColor: ['#FF0000', '#00FF00', '#0000FF'],
        });;
    }

    $scope.methods.buildDefaultsOptions = function () {
        if (!$scope.$ctrl.chartData || !$scope.$ctrl.chartLabels) {
            return {};
        }

        var datalabelsOptions = false;
        var total = $scope.$ctrl.chartData.reduce(function (c, v) {
            var val = parseFloat(v);
            if (!isNaN(val)) {
                c += val;
            }
            return c;
        }, 0);

        if ($scope.$ctrl.chartData.length < 6) {
            datalabelsOptions = {
                backgroundColor: function(context) {
                    return context.dataset.backgroundColor;
                },
                borderColor: 'white',
                borderRadius: 25,
                borderWidth: 1,
                color: 'white',
                display: function(context) {
                    var dataset = context.dataset;
                    var count = dataset.data.length;
                    var value = dataset.data[context.dataIndex];
                    return value > count * 1.5;
                },
                font: {
                    size: 8,
                },
                formatter: function (value, ctx) {
                    var sum = 0;
                    var dataArr = $scope.$ctrl.chartData;
                    dataArr.map(function (data) {
                        var val = parseFloat(data);
                        if (!isNaN(val)) {								
                            sum += val;
                        }
                    });
                    var percentage = $filter('number_format')((value*100 / sum).toFixed(2), 0, ',', '.');

                    // return percentage >= 10 ? (percentage+'%') : '';
                    return percentage+'%';
                }
            }
        }

        return {
            plugins: {
                datalabels: datalabelsOptions,
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItems, data) {
                        var index = tooltipItems.index;
                        var val = parseFloat($scope.$ctrl.chartData[index]);
                        var percentage = '';
                        if (!isNaN(val)) {
                            percentage = val / total * 100;
                            percentage = '(' + $filter('number_format')(percentage, 0) + '%)';
                            val = $filter('number_format')(val, 0);
                        }

                        var callback = resolverService.resolve([$scope.$ctrl, 'callbacks', 'tooltips']);
                        if (callback) {
                            return callback($scope.$ctrl.chartLabels[index], val, percentage);
                        }

                        return ' ' + [$scope.$ctrl.chartLabels[index], val, '€', percentage].join(' ');
                    }
                }
            }
        };
    };

    $scope.methods.buildBarDefaultsOptions = function () {
        var res = parseInt($scope.helpers.roundToTop(parseInt(Math.max.apply(this, $scope.$ctrl.chartData))));
        var stepSize = Math.round(res / 4);

        return {
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    barPercentage: 0.5,
                    ticks: {
                        fontSize: 8,
                        callback: resolverService.resolve([$scope.$ctrl, 'callbacks', 'xAxesTicks'], function (tick, index, ticks) {
                            return tick;
                        }),
                    },
                }],
                yAxes: [{
                    gridLines: {
                        display: true
                    },
                    ticks: {
                        beginAtZero: true,
                        max: res,
                        stepSize: stepSize,
                        fontSize: 10,
                        callback: resolverService.resolve([$scope.$ctrl, 'callbacks', 'yAxesTicks'], function (tick, index, ticks) {
                            return $filter('number_format')(+tick, 0, ',', '.') + ' €';
                        }),
                    },
                }]
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: resolverService.resolve([$scope.$ctrl, 'callbacks', 'tooltips'], function(tooltipItems, data) { 
                        return $filter('number_format')(+tooltipItems.yLabel, 0, ',', '.') + ' €';
                    }),
                },
            },
            plugins: {
                datalabels: false
            }
        };
    };
}

StatisticsChartController.$inject = ['$scope', '$filter', 'statisticsService', 'resolverService'];

angular.module('dashboardApp').component('appStatisticsChart', {
    templateUrl: 'statistics-chart.html',
    controller: StatisticsChartController,
    bindings: {
        chartLabels: '=',
        chartData: '=',
        chartType: '=',
        chartOptions: '=',
        chartColors: '=',
        chartDatasetOverride: '=',
        callbacks: '=',
    }
});