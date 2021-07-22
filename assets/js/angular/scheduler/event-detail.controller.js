
EventDetailDialogController.$inject = ['$scope', '$http', '$mdDialog', 'activeEvent', 'zIndex'];

function EventDetailDialogController ($scope, $http, $mdDialog, activeEvent, zIndex) {
	$scope.event = activeEvent;
	$scope.zIndex = zIndex;

	this.$onInit = function () {
		$scope.event = activeEvent;
		$scope.zIndex = zIndex;
	};

	$scope.$watch('zIndex', function () {
		$scope.getEventDetailStyle();
	});

	$scope.getEventDetailStyle = function () {
		return {
			zIndex: $scope.zIndex,
		};
	}
}

export default EventDetailDialogController;