angular.module('schedulerModule').factory('schedulerService', ['SCHEDULER_CELL_NAME', function(SCHEDULER_CELL_NAME) {
    var _this = this;

    /**
     * 
     * @param {object} resource 
     * @param {object} week 
     * @return {string} id
     */
    _this.generateCellId = function (resourceId, yearNumber, weekNumber) {
        return [SCHEDULER_CELL_NAME, resourceId, yearNumber, weekNumber].join('-');
    };

    return _this;
}]);