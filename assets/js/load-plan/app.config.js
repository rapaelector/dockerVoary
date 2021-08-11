import angular from 'angular';
import { primaryPalette, secondaryPalette } from './../angular/shared/config';

angular.module('loadPlanApp').constant('DATE_FORMAT', 'DD/MM/YYYY');

angular.module('loadPlanApp').constant('MESSAGES', window.MESSAGES);

angular.module('loadPlanApp').config(['$mdDateLocaleProvider', 'DATE_FORMAT', function ($mdDateLocaleProvider, DATE_FORMAT) {
    /**
     * @param date {Date}
     * @returns {string} string representation of the provided date
     */
    $mdDateLocaleProvider.formatDate = function(date) {
        // return date ? moment(date).format('L') : '';
        return date ? moment(date).format(DATE_FORMAT) : '';
    };

    /**
     * @param dateString {string} string that can be converted to a Date
     * @returns {Date} JavaScript Date object created from the provided dateString
     */
    $mdDateLocaleProvider.parseDate = function(dateString) {
        // var m = moment(dateString, 'L', true);
        var m = moment(dateString, DATE_FORMAT, true);
        return m.isValid() ? m.toDate() : new Date(NaN);
    };
}]);

angular.module('loadPlanApp').config(['$mdThemingProvider', 'moment', function ($mdThemingProvider, moment) {
    moment.locale('fr');
    
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