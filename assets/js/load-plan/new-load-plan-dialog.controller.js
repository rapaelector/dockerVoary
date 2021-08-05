NewLoadPlanDialogController.$inject = ['$scope', '$mdDialog'];

function NewLoadPlanDialogController ($scope, $mdDialog) {
    this.$onInit = () => {
        console.info('new load plan dialog shwo');
    };

};

export default NewLoadPlanDialogController;