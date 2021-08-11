angular.module('loadPlanPlanningApp').controller('loadPlanPlanningController', ['$scope', function($scope) {
    $scope.data = {
        date: {
            startDate: moment().startOf('year'),
            endDate: moment().endOf('year'),
        },
    };
    $scope.options = {
        dateRangePicker: {},
    };

    this.$onInit = () => {
        $scope.options.dateRangePicker = {
            autoApply: true,
            autoClose: true,
            locale: {
                format: 'DD/MM/YYYY'
            },
            ranges: {
                "Année courante": [moment().startOf('year'), moment().endOf('year')],
                "Les 6 dérnier mois": [moment(moment().endOf('month').add(-6, 'month').startOf('month')), moment(moment().endOf('month').add(-1, 'month'))],
                "Année dérniere": [moment(moment().add(-1, 'year').format('YYYY') + '-01-01'), moment(moment().add(-1, 'year').format('YYYY') + '-12-31')],
                "6 prochains mois": [moment(), moment().add(6, 'month')],
                "12 prochains mois": [moment(), moment().add(12, 'month').endOf('month')],
                "12 mois glissant": [moment(), moment().add(1, 'year').endOf('month')],
            },
        };
    };

    $scope.$watch('data.date', () => {
        console.info($scope.data.date);
    }, true)
}]);