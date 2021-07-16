import { resources, buildColumns } from './mock-data';
import numberFormat from './../utils/number_format';

angular.module('projectScheduleApp').controller('projectScheduleController', ['$scope', '$mdDialog', 'moment', function($scope, $mdDialog, moment) {

    $scope.data = {
        resources: resources,
        columns: buildColumns(numberFormat),
        start: null,
        end: null,
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
    };
    $scope.options = {
        dateRangePicker: {},
    };

    this.$onInit = function() {
        $scope.options.dateRangePicker = {
            autoApply: true,
            autoClose: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: {
                "Aujourd'hui": [moment(), moment()],
                "Hier": [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                "Les 7 derniers jours": [moment().subtract(6, 'days'), moment()],
                "Les 30 derniers jours": [moment().subtract(29, 'days'), moment()],
                "Ce mois": [moment().startOf('month'), moment().endOf('month')],
                "Le mois dernier": [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
        };
    };

    $scope.$watch('data.date', function () {}, true);
}]);