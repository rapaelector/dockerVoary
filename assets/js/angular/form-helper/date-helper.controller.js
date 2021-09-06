DateHelperController.$inject = ['$scope', 'mdPanelRef', 'options', 'PANEL_ELEVATION_CLASS'];

function DateHelperController($scope, mdPanelRef, options, PANEL_ELEVATION_CLASS) {
    $scope.data = {
        date: null,
        pageTitle: '',
        additionalTitle: '',
    };
    $scope.loading = false;

    this.$onInit = () => {
        if (options) {
            if (options.pageTitle) {
                $scope.data.pageTitle = options.pageTitle;
            }

            if (options.currentDate) {
                $scope.data.date = new Date(options.currentDate);
            }

            if (options.additionalTitle) {
                $scope.data.additionalTitle = options.additionalTitle;
            }
        }
    };

    $scope.onSave = (ev) => {
        if (options && options.onSave) {
            $scope.loading = true;
            options.onSave($scope.data.date, mdPanelRef).then(() => {
                $scope.loading = false;
            }, () => {
                $scope.loading = false;
            });
        };
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
}

export default DateHelperController;