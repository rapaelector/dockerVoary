WeekNumberHelperController.$inject = ['$scope', 'mdPanelRef', 'options', 'loadPlanService', 'PANEL_ELEVATION_CLASS'];

function WeekNumberHelperController($scope, mdPanelRef, options, loadPlanService, PANEL_ELEVATION_CLASS) {
    $scope.data = {
        weeks: {},
        week: null,
        id: null,
        projectName: '',
        pageTitle: '',
        label: '',
        additionalTitle: '',
    };
    $scope.loading = false;

    this.$onInit = () => {
        var weeksNumber = moment(moment().format('YYYY')).weeks();
        $scope.data.additionalTitle = options.additionalTitle;
        $scope.data.weeks = new Array(weeksNumber).fill(0).map((elem, i) => {
            return {weekNumber: i + 1, weekDate: moment().day('Monday').week(i + 1).format('YYYY-MM-DD')};
        });

        $scope.data.id = options.id;
        $scope.data.projectName = options.projectName;
        $scope.data.pageTitle = options.pageTitle;
        $scope.data.label = options.label;
        $scope.data.week = options.currentValue;
    };

    $scope.onWeekChange = () => {
        // $scope.saveWeek();
    };

    $scope.saveWeekNumber = () => {
        $scope.saveWeek();
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };

    $scope.saveWeek = () => {
        if ($scope.data.week) {
            var weekDate = moment($scope.data.week).format('YYYY-MM-DD');
            $scope.loading = true;
            loadPlanService.saveWeekNumber($scope.data.id, {weekDate}).then((response) => {
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
    }
};

export default WeekNumberHelperController;