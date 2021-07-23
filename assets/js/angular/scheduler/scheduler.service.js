angular.module('schedulerModule').factory('schedulerService', ['SCHEDULER_COLUMN_CLASS', function(SCHEDULER_COLUMN_CLASS) {
    var _this = this;

    /**
     * 
     * @param {object} resource 
     * @param {object} week 
     * @return {string} id
     */
    _this.generateCellId = function (resourceId, yearNumber, weekNumber) {
        return [SCHEDULER_COLUMN_CLASS, resourceId, yearNumber, weekNumber].join('-');
    };

    return _this;
}]);