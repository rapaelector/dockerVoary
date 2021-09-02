WeekNumberController.$inject = ['$scope', 'mdPanelRef', 'options', 'loadPlanService'];

function WeekNumberController($scope, mdPanelRef, options, loadPlanService) {
    $scope.data = {
        weeks: {},
        week: null,
        loadPlanId: null,
        projectName: '',
    };
    $scope.loading = false;

    this.$onInit = () => {
        var weeksNumber = moment(moment().format('YYYY')).weeks();
        $scope.data.weeks = new Array(weeksNumber).fill(0).map((elem, i) => {
            return {weekNumber: i + 1, weekDate: moment().day('Monday').week(i + 1).format('YYYY-MM-DD')};
        });

        $scope.data.loadPlanId = options.loadPlanId;
        $scope.data.projectName = options.projectName;
    };

    $scope.onWeekChange = () => {
        if ($scope.data.week) {
            $scope.loading = true;
            loadPlanService.saveWeekNumber($scope.data.loadPlanId, $scope.data.week).then((response) => {
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
                $scope.loading = false;
                loadPlanService.showNotification(response.data.message, 'toast-success');
            }, errors => {
                mdPanelRef.close();
                $('body').trigger('load_plan.redraw-dt');
                $scope.loading = false;
                loadPlanService.showNotification(errors.data.message, 'toast-error');
            });
        }
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
};

export default WeekNumberController;