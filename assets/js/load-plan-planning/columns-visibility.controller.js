ColumnsVisibilityController.$inject = ['$scope', 'mdPanelRef', 'columns'];

function ColumnsVisibilityController($scope, mdPanelRef, columns) {
    this.$onInit = () => {
        console.info('Columns visibility controller');
        var _columns = Array.isArray(columns) ? columns : [];
        $scope.columns = _columns.map(c => ({ ...c, visible: c.visible !== false }));
    }

    $scope.saveColumnsVisibility = () => {
        mdPanelRef.close($scope.columns);
    }

    $scope.onVisibilityChange = (column, columnIndex) => {
        if (column.visible === false) {
            column.sticky = false;
        }
    }

    $scope.cancel = () => {
        mdPanelRef.close();
    }
};


export default ColumnsVisibilityController;