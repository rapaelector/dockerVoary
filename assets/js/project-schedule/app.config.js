angular.module('projectScheduleApp').constant('DEFAULT_CELL_WIDTH', 24);

angular.module('projectScheduleApp').config(['$mdThemingProvider', function ($mdThemingProvider) {
	$mdThemingProvider.definePalette('primaryPalette', {
        '50': '474bc5',
        '100': '27297a',
        '200': '2d308f',
        '300': '3337a3',
        '400': '3a3eb7',
        '500': '474bc5',
        '600': '595dcb',
        '700': '6c6fd1',
        '800': '7e81d6',
        '900': '9193dc',
        'A100': 'a3a5e2',
        'A200': '474bc5',
        'A400': '474bc5',
        'A700': '474bc5',
    });
	
    $mdThemingProvider.definePalette('secondaryPalette', {
        '50': '474bc5',
        '100': 'b75c09',
        '200': 'd56b0b',
        '300': 'f37b0d',
        '400': 'f48b2c',
        '500': 'f69c4a',
        '600': 'f7a65c',
        '700': 'f8b06e',
        '800': 'f9ba80',
        '900': 'fac492',
        'A100': 'fbcea5',
        'A200': '474bc5',
        'A400': '474bc5',
        'A700': '474bc5',
    });

	$mdThemingProvider.theme('default')
		.primaryPalette('primaryPalette', {
			default: '500',
		})
		.accentPalette('secondaryPalette', {
            default: '500',
        })
    ;
}]);