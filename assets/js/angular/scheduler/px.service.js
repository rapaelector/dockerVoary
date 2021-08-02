angular.module('schedulerModule').factory('px', [function() {
    return (value) => {
        if (isNaN(parseFloat(value))) {
            return value;
        }

        return parseFloat(value) + 'px';
    }
}]);
