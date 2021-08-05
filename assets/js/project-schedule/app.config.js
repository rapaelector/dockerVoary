import { primaryPalette, secondaryPalette } from './../angular/shared/config';

angular.module('projectScheduleApp').constant('DEFAULT_CELL_WIDTH', 24);

angular.module('projectScheduleApp').config(['$mdThemingProvider', function ($mdThemingProvider) {
	$mdThemingProvider.definePalette('primaryPalette', primaryPalette);
	
    $mdThemingProvider.definePalette('secondaryPalette', secondaryPalette);

	$mdThemingProvider.theme('default')
		.primaryPalette('primaryPalette', {
			default: '500',
		})
		.accentPalette('secondaryPalette', {
            default: '500',
        })
    ;
}]);