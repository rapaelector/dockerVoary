DateHelperController.$inject = ['$scope', 'mdPanelRef', 'options'];

function DateHelperController($scope, mdPanelRef, options) {
    $scope.data = {
        date: null,
        pageTitle: '',
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
        }
    };

    $scope.saveDate = (ev) => {
        if (options && options.saveDate) {
            options.saveDate($scope.data.date);
        };
    };

    $scope.closePanel = () => {
        mdPanelRef.close();
    };
}

export default DateHelperController;