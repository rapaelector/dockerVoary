
EventDetailDialogController.$inject = ['$scope', '$http', '$mdDialog', 'activeEvent'];

function EventDetailDialogController ($scope, $http, $mdDialog, activeEvent) {
	$scope.event = activeEvent;

	this.$onInit = function () {
		$scope.event = activeEvent;
	};

	$scope.getEventDetailStyle = function () {
		return {};
		// return {
		// 	width: width,
		// 	minWidth: width,
		// 	maxWidth: width,
		// };
	}
}

export default EventDetailDialogController;