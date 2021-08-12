import { primaryPalette, secondaryPalette } from './../angular/shared/config';

angular.module('loadPlanPlanningApp').config(['$mdThemingProvider', function ($mdThemingProvider) {
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
}])