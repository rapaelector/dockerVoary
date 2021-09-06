WeekNumberHelperController.$inject = ['$scope', 'mdPanelRef', 'moment', 'options', 'PANEL_ELEVATION_CLASS'];

function WeekNumberHelperController($scope, mdPanelRef, moment, options, PANEL_ELEVATION_CLASS) {
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
            if (options && options.onWeekNumberSave) {
                $scope.loading = true;
                options.onWeekNumberSave($scope.data.week, mdPanelRef).then((response) => {
                    $scope.loading = false;
                    mdPanelRef.close();
                    $('body').trigger('load_plan.redraw-dt');
                }, errors => {
                    mdPanelRef.close();
                    $scope.loading = false;
                })
            }
        }
    }
};

export default WeekNumberHelperController;