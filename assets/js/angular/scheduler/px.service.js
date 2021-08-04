angular.module('schedulerModule').factory('px', [function() {
    return (value) => {
        if (isNaN(parseInt(value))) {
            return value;
        }

        return parseInt(value) + 'px';
    }
}]);
