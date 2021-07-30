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
     *      firstWeek: boolean,
     *      lastWeek: boolean,
     *      year: string | number,
     *      monthNumber: string | number,
     *      startDay: moment,
     *      endDay: moment,
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
            var monthNumber = current.format('M');
            var year = moment(current).format('YYYY');
            if (!dates[key]) {
                dates[key] = {
                    monthNumber,
                    year,
                    month: current.format('MMMM'),
                    weeks: [],
                };
            }
            
            var firstWeek = moment(current);
            var lastWeek = moment(current);
            if (firstWeek.startOf('month').format('d') != 1) {
                if (firstWeek.startOf('month').format('d') == 0) {
                    firstWeek = firstWeek.startOf('month').add(1, 'day').day(1);
                } else {
                    firstWeek = firstWeek.startOf('month').add(1, 'week').day(1);
                }
            }

            if (lastWeek.endOf('month').format('d') != 1) {
                if (lastWeek.endOf('month').format('d') == 0) {
                    lastWeek = lastWeek.endOf('month').add(-1, 'week').day(1);
                } else {
                    lastWeek = lastWeek.endOf('month').day(1);
                }
            }
            dates[key].weeks.push({
                year,
                monthNumber,
                firstWeek: firstWeek.isSame(current),
                lastWeek: lastWeek.isSame(current, 'week'),
                weekNumber: current.week(),
                startDay: moment(current).startOf('week'),
                endDay: moment(current).endOf('week'),
            });

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
                monthNumber: dateGroup.monthNumber,
                year: dateGroup.year,
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

    _this.nextMonday = function (date) {
        if (date.format('d') == 1) {
            return date;
        }

        if (date.format('d') != 1 && date.format('d') == 0) {
            return date.add(1, 'day').day(1);
        }

        return date.add(1, 'week').day(1);
    };

    /**
     * 
     * @param {Moment} date 
     * @returns {Moment} date
     */
    _this.previousMonday = function (date) {
        if (date.format('d') == 1) {
            return date;
        }

        if (date.format('d') != 1 && date.format('d') == 0) {
            return date.day(1);
        }

        return date.add(-1, 'week').day(1);
    };

    return _this;
}])