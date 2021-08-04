ColumnsVisibilityController.$inject = ['$scope', '$mdPanel', '$http', '$mdToast', 'fosJsRouting', 'mdPanelRef', 'columns'];

function ColumnsVisibilityController($scope, $mdPanel, $http, $mdToast, fosJsRouting, mdPanelRef, columns) {
    this.$onInit = () => {
        var _columns = Array.isArray(columns) ? columns : [];
        $scope.tempColumns = _columns.map(c => ({ ...c, visible: c.visible !== false }));
    }

    $scope.saveColumnsVisibility = () => {
        mdPanelRef.close($scope.tempColumns);
    }

    $scope.cancel = () => {
        mdPanelRef.close();
    }

    $scope.onVisibilityChange = (column, columnIndex) => {
        if (column.visible === false) {
            column.sticky = false;
        }
    }
}

export default ColumnsVisibilityController;