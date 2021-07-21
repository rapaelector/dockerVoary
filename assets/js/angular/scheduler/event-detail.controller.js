
EventDetailDialogController.$inject = ['$scope', '$http', '$mdDialog', 'activeEvent'];

function EventDetailDialogController ($scope, $http, $mdDialog, activeEvent) {
	$scope.event = activeEvent;

	this.$onInit = function () {};
}

export default EventDetailDialogController;