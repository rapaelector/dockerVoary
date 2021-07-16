angular.module('schedulerModule').factory('calendarService', ['moment', function(moment) {
    var _this = {};

    _this.isValidDate = function (date) {
        return moment(date).isValid();
    };

    /**
     * Get dates between `start` and `end`
     * Return data structure
     * Array of :
     * {
     *      month: string,
     *      weeks: number[],
     * }
     * 
     * @param {Moment} start 
     * @param {Moment} end 
     * @return {any[]}
     */
    _this.getDates = function (start, end) {
        var dates = {};
        var current = moment(start);
        if (!current.isSame(moment(start).startOf('week'))) {
            current.add(1, 'week').startOf('week');
        }

        while (current.isSameOrBefore(end)) {
            var key = current.format('YYYY-MM');
            if (!dates[key]) {
                dates[key] = {
                    month: current.format('MMMM'),
                    year: moment(current).format('YYYY'),
                    weeks: [],
                };
            }
            dates[key].weeks.push(current.week());

            // increment with one week
            current.add(1, 'weeks');
        }

        return dates;
    }

    _this.getDatesWeeks = (dates) => {
        var weeks = [];
        for (var key in dates) {
            weeks = [...weeks, ...dates[key].weeks];
        }

        return weeks;
    }

    _this.getDatesMonths = (dates) => {
        var months = [];

        for (var key in dates) {
            var dateGroup = dates[key];
            months.push({
                name: dateGroup.month,
                weeksCount: dateGroup.weeks.length,
            });
        }

        return months;
    }

    _this.getDatesYears = (dates) => {
        var years = {};

        for (var key in dates) {
            var dateGroup = dates[key];
            if (!years[dateGroup.year]) {
                years[dateGroup.year] = {
                    name: dateGroup.year,
                    weeksCount: 0,
                    monthsCount: 0,
                };
            }
            years[dateGroup.year].monthsCount++;
            years[dateGroup.year].weeksCount += dateGroup.weeks.length;
        }

        return Object.values(years);
    }

    return _this;
}])