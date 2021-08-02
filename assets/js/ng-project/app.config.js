// TYPE DE DOSSIER
angular.module('projectApp').constant('CASE_TYPES', window.CASE_TYPES);

// PRIORIZATION DES DOSSIER
angular.module('projectApp').constant('PRIORIZATION_FILE_TYPES', window.PRIORIZATION_FILE_TYPES);

// TYPE DE MARCHE
angular.module('projectApp').constant('MARKET_TYPES', window.MARKET_TYPES);

// A.O PUBLIC, AO PRIVE, MARCHE PUBLIC, MARCHE PRIVE
angular.module('projectApp').constant('SECOND_MARKET_TYPES', window.SECOND_MARKET_TYPES);

// BONHOMME EST-IL
angular.module('projectApp').constant('TYPE_BONHOME', window.TYPE_BONHOME);

// PROJECT ID
angular.module('projectApp').constant('PROJECT_ID', window.PROJECT_ID);

//
angular.module('projectApp').constant('APP_MESSAGES', window.APP_MESSAGES);

angular.module('projectApp').constant('PROJECT_EVENT_TYPES', window.PROJECT_EVENT_TYPES);

angular.module('projectApp').constant('DATE_FORMAT', 'DD/MM/YYYY');

angular.module('projectApp').config(['$mdDateLocaleProvider', 'DATE_FORMAT', function ($mdDateLocaleProvider, DATE_FORMAT) {
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
}])