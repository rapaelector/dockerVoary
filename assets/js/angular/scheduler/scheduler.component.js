import schedulerTemplate from './template.html';
import EventDetailDialogController from './event-detail.controller';
function SchedulerController(
    $scope, 
    $mdDialog,
    $mdPanel,
    $timeout,
    moment,
    px,
    calendarService,
    resolverService,
    schedulerService, 
    DEFAULT_CELL_WIDTH,
    DEFAULT_DATE_FORMAT,
    SCHEDULER_COLUMN_CLASS,
    SCHEDULER_EVENT_CLASS,
    EVENT_Z_INDEX,
    ROW_HEIGHT,
    DEFAULT_BUBBLE_TIMEOUT,
    SCHEDULER_DATE_CELL_CLASS,
    SCHEDULER_HEADER_WEEK_CLASS,
    SCHEDULER_HEADER_MONTH_CLASS,
    SCHEDULER_HEADER_YEAR_CLASS,
    SCHEDULE_RESOURCE_HEADER_CLASS,
    SCHEDULE_HEADER,
) {
    $scope.weeks = null;
    $scope.months = null;
    $scope.years = null;
    $scope.dates = null;
    $scope.start = null;
    $scope.end = null;
    $scope.options = {};
    $scope.defaultOptions = {};
    $scope.styles = {
        table: {},
    };
    $scope.events = [];
    $scope.mdPanelRef = null;
    $scope.triggerTimeout = null;

    this.$onInit = function () {
        $(function () {
            $('[data-toggle="popover"]').popover()
        });
        $mdPanel.newPanelGroup('bubble', {
            maxOpen: 1,
        });
        $scope.defaultOptions = {
            event: {
                bubbleDelay: DEFAULT_BUBBLE_TIMEOUT,
            },
            cell: {
                width: DEFAULT_CELL_WIDTH,
            },
        }
    };

    $scope.$watch('$ctrl.start', function () {
        var start = moment($scope.$ctrl.start);
        if (!calendarService.isValidDate($scope.$ctrl.start)) {
            throw 'Start date is not a moment valid date';
        }
        $scope.start = start;
        $scope.updateDates();
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.end', function () {
        var end = moment($scope.$ctrl.end);
        if (!calendarService.isValidDate($scope.$ctrl.end)) {
            throw 'End date is not a moment valid date';
        }
        $scope.end = end;
        $scope.updateDates();
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.columns', function () {
        $scope.updateTableStyles();
    });

    $scope.$watch('$ctrl.options', function () {
        $scope.options = $.extend(true, {}, $scope.defaultOptions, $scope.$ctrl.options);
        $scope.updateTableStyles();
    }, true);

    $scope.$watch('$ctrl.events', function () {
        if ($scope.$ctrl.events) {
            $scope.events = $scope.$ctrl.events;
        }
    }, true);

    /**
     * Get resources to loop and display in the table
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getResourceColumn = function(resource, column, index) {
        var res = null;

        if (column.field) {
            res = resource[column.field];
        }

        if (column.formatter) {
            res = column.formatter(res, resource, index);
        }

        return res;
    };

    /**
     * Get resource cell class
     * Each cell have its header columns name as class suffix
     *      e.g: constructionSite(chantier) column
     *              - each constructionSite cell have scheduler-cell-constructionSite class
     * 
     * @param {object} resource 
     * @param {object} column 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getCellClassName = function(resource, column, index) {
        var res = [SCHEDULER_COLUMN_CLASS, SCHEDULER_COLUMN_CLASS + '-' + column.field];

        if (column.className) {
            res.push(column.className);
        }

        if (column.classNameFormatter) {
            var className = column.classNameFormatter(res, resource, index);
            res.push(className);
        }

        return res;
    }

    /**
     * Get resource header column class
     * 
     * @param {object} column 
     * @returns {array} array of string
     */
    $scope.getHeaderCellClassName = function(column) {
        var res = [SCHEDULE_RESOURCE_HEADER_CLASS, SCHEDULE_RESOURCE_HEADER_CLASS + '-' + column.field];

        if (column.headerClassName) {
            res.push(column.headerClassName);
        }

        return res;
    }

    /**
     * 
     * @param {object} column 
     * @returns {object} object of style
     */
    $scope.getColumnWidth = function (column) {
        return column.width ? {
            'width': px(parseInt(column.width)),
            'min-width': px(parseInt(column.width)),
        } : {};
    };

    /**
     * Update the date of scheduler depend on the input date
     * 
     * @returns {object}
     */
    $scope.updateDates = function () {
        if (!$scope.start || !$scope.end) {
            return;
        }
        $scope.dates = calendarService.getDates(moment($scope.start), moment($scope.end));
        $scope.weeks = calendarService.getDatesWeeks($scope.dates);
        $scope.months = calendarService.getDatesMonths($scope.dates);
        $scope.years = calendarService.getDatesYears($scope.dates);
    };

    $scope.getHeaderWeeksStyle = function () {
        return {width: resolverService.resolve([$scope, 'options', 'cell', 'width'], $scope.defaultOptions.cell.width)};
    };

    /**
     * @param {object} resource
     * @param {object} week 
     * @param {number} index
     * @param {number} resourceIndex
     * 
     * @returns {object} object of style
     */
    $scope.getCellStyle = function (resource, week, index, resourceIndex) {
        var rowHeight = ROW_HEIGHT * ($scope.countResourceEventOverlap(resource.id) + 1);
        var width = resolverService.resolve([$scope, 'options', 'cell', 'width'], $scope.defaultOptions.cell.width);

        return {
            width: px(width),
            height: px(rowHeight),
        };
    };

    /**
     * Update table styles
     */
    $scope.updateTableStyles = function () {
        const tableStyles = {};
        let w = 0;
        if (!$scope.weeks || !$scope.$ctrl.columns) {
            return;
        }
        let OK = true;
        for (const i in $scope.$ctrl.columns) {
            const columnWidth = $scope.$ctrl.columns[i].width;
            if (isNaN(parseInt(columnWidth))) {
                OK = false;

                break;
            }
            w += +columnWidth;
        }
        const cellWidth = resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        w += $scope.weeks.length * cellWidth;
        if (OK) {
            tableStyles.width = px(w);
            tableStyles['table-layout'] = 'fixed';
        } else {
            console.warn('NaN table style width');
        }

        $scope.styles.table = tableStyles;
    };

    /**
     * Get column merge style : column above the resource columns
     * 
     * @returns {object} object of style
     */
    $scope.columnMergeStyle = function () {
        var width = 0;
        if (!$scope.$ctrl.columns) {
            return;
        }

        for (const i in $scope.$ctrl.columns) {
            if (isNaN(parseInt($scope.$ctrl.columns[i].width))) {
                return {width: 'auto'};
            }
            width += parseInt($scope.$ctrl.columns[i].width);
        }

        return { width: width };
    };

    /**
     * Get header year style and bind to the year header with ng-style
     * 
     * @param {object} year 
     * @param {number} index 
     * @returns {object} object of style
     */
    $scope.getYearStyles = function (year, index) {
        var width = year.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: px(width)};
    };
    
    /**
     * Get header month style and bind to the month header with ng-style
     * 
     * @param {object} month 
     * @param {number} index 
     * @returns {object} object of style
     */
    $scope.getMonthStyles = function (month, index) {
        var width = month.weeksCount * resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);
        return {width: px(width)};
    };

    /**
     * Get header year class
     * If the year is same as current year add additional class to the year
     *      - scheduler-header-year-current
     *  
     * @param {object} year 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getHeaderYearClassName = function (year, index) {
        var res = [SCHEDULER_HEADER_YEAR_CLASS];

        if ($scope.$ctrl.headerYearClassName) {
            res.push($scope.$ctrl.headerYearClassName);
        }

        if (moment().format('YYYY') == year.name) {
            res.push(SCHEDULE_HEADER + '-year-current');
        }

        return res;
    }
    
    /**
     * Get header month class
     * If the current month and current year is same as current month and current year add additional class to the header
     *      - scheduler-header-month-current
     * 
     * @param {object} month 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getHeaderMonthClassName = function (month, index) {
        var res = [SCHEDULER_HEADER_MONTH_CLASS];

        if ($scope.$ctrl.headerMonthClassName) {
            res.push($scope.$ctrl.headerMonthClassName);
        }
        if (moment(`${month.year}-${month.monthNumber}`, 'YYYY-M').isSame(moment(), 'month')) {
            res.push(SCHEDULE_HEADER + '-month-current');
        }

        return res;
    }

    /**
     * Get week number number class
     * If the current week, month and year is same as week.startDay (moment) then add additional class to the current week
     *      - scheduler-header-week-current
     * 
     * @param {object} week 
     * @param {number} index 
     * @returns {array} array of string
     */
    $scope.getHeaderWeekClassName = function (week, index) {
        var res = [SCHEDULER_HEADER_WEEK_CLASS];

        if ($scope.$ctrl.headerWeekClassName) {
            res.push($scope.$ctrl.headerWeekClassName);
        }

        if (week.startDay.isSame(moment(), 'week')) {
            res.push(SCHEDULE_HEADER + '-week-current');
        }

        if (week.firstWeek) {
            res.push(SCHEDULE_HEADER + '-firstweek');
        };

        if (week.lastWeek) {
            res.push(SCHEDULE_HEADER + '-lastweek')
        }
        return res;
    }

    $scope.getDateCellClassName = function (resource, week, weekIndex, resourceIndex) {
        var res = [SCHEDULER_DATE_CELL_CLASS];

        if (week.firstWeek) {
            res.push(SCHEDULER_DATE_CELL_CLASS + '-firstweek');
        }

        if (week.lastWeek) {
            res.push(SCHEDULER_DATE_CELL_CLASS + '-lastweek');
        }

        return res;
    };

    // SCHEDULER EVENT HANDLER
    /**
     * Handler for row click
     * 
     * @param {object} resource 
     * @param {column} column 
     * @param {number} columnIndex 
     */
    $scope.onRowClick = function (resource, column, columnIndex) {
        if ($scope.$ctrl.onRowClick) {
            $scope.$ctrl.onRowClick(resource, column, columnIndex);
        }
    }

    /**
     * Handler for column header click
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     */
    $scope.onColumnHeaderClick = function (column, columnIndex) {
        if ($scope.$ctrl.onColumnHeaderClick) {
            $scope.$ctrl.onColumnHeaderClick(column, columnIndex);
        }
    }

    /**
     * Handler for header year click
     * 
     * @param {object} yearObject 
     * @param {number} yearIndex
     */
    $scope.onHeaderYearClick = function (yearObject, yearIndex) {
        if ($scope.$ctrl.onHeaderYearClick) {
            $scope.$ctrl.onHeaderYearClick(yearObject, yearIndex);
        }
    }

    /**
     * Header for header month click
     * 
     * @param {object} monthObject 
     * @param {number} monthIndex 
     */
    $scope.onHeaderMonthClick = function (monthObject, monthIndex) {
        if ($scope.$ctrl.onHeaderMonthClick) {
            $scope.$ctrl.onHeaderMonthClick(monthObject, monthIndex);
        }
    }

    /**
     * Handler for header week click
     * 
     * @param {object} weekObject 
     * @param {number} weekIndex 
     */
    $scope.onHeaderWeekClick = function (weekObject, weekIndex) {
        if ($scope.$ctrl.onHeaderWeekClick) {
            $scope.$ctrl.onHeaderWeekClick(weekObject, weekIndex);
        }
    }

    /**
     * Handler for date cell click
     * 
     * @param {object} resource 
     * @param {object} week 
     * @param {number} weekIndex 
     * @param {number} resourceIndex 
     */
    $scope.onCellClick = function (resource, week, weekIndex, resourceIndex) {
        if ($scope.$ctrl.onCellClick) {
            $scope.$ctrl.onCellClick(resource, week, weekIndex, resourceIndex);
        }
    }

    $scope.updateScheduler = function (resource, event, week, resourceIndex, weekIndex) {
    };

    /**
     * 
     * @param {object} resource 
     * @param {week} week 
     * @returns {array}
     */
    $scope.getCellId = function (resource, week) {
        return schedulerService.generateCellId(resource.id, week.year, week.weekNumber);
    }

    /**
     * Get event style
     * 
     * @param {event} event 
     * @param {eventIndex} eventIndex 
     * @returns {object} object of style
     */
    $scope.getEventStyle = function (event, eventIndex) {
        var position = $scope.getEventStyleAndPosition(event, eventIndex);
        var eventStyle = event.style || {};

        return {
            ...eventStyle,
            backgroundColor: event.backgroundColor,
            color: event.color,
            display: 'none',
            position: 'inherit',
            height: 0,
            width: 0,
            zIndex: EVENT_Z_INDEX, 
            ...position,
        };
    }

    /**
     * Get event position from schedule cell position
     * Get startPostion and endPosition of one event
     * 
     * @param {object} resource 
     * @param {week} week 
     * @returns {object} object of style
     */
    $scope.getEventStyleAndPosition = function (event, eventIndex) {
        if (moment(event.start).isAfter($scope.end) || moment(event.end).isBefore($scope.start)) {
            return {
                opacity: 0,
                display: 'none',
            };
        }

        var left = null;
        var right = null;
        var top = null;

        var startId = schedulerService.generateCellId(event.resource, moment(event.start).format('YYYY'), moment(event.start).week());
        var endId = schedulerService.generateCellId(event.resource, moment(event.end).format('YYYY'), moment(event.end).week());
        var $startCell = $('#' + startId);
        var $endCell = $('#' + endId);

        if ($startCell.length == 0) {
            var firstMondayDate = calendarService.nextMonday(moment($scope.start));
            startId = schedulerService.generateCellId(event.resource, firstMondayDate.format('YYYY'), firstMondayDate.week());
            $startCell = $('#' + startId);
        }

        if ($startCell.length > 0) {
            left = $startCell.position().left;
            top = $startCell.position().top;
        }

        if ($endCell.length == 0) {
            endId = schedulerService.generateCellId(event.resource, moment($scope.end).format('YYYY'), moment($scope.end).week());
            $endCell = $('#' + endId);
        }

        if ($endCell.length > 0) {
            top = top ? top : $endCell.position().top;
            right = $endCell.position().left + $endCell.outerWidth();
        }

        var overlaps = $scope.computeResourceEventOverlap(event.resource);
        if (overlaps) {
            var eventOverlaps = Object.values(overlaps).filter(ov => ov.events.findIndex(e => e.id === event.id) > -1);
            if (eventOverlaps.length > 0) {
                var eventOverlap = null;
                for (var i in eventOverlaps) {
                    if (!eventOverlap || (eventOverlaps[i].count > eventOverlap.count)) {
                        eventOverlap = eventOverlaps[i];
                    }
                }
                var index = eventOverlap.events.findIndex(e => e.id === event.id);
                top += index * ROW_HEIGHT;
            }
        }
        
        return {
            top: px(top),
            left: px(left),
            right: px(right),
            display: 'block',
            width: px(((right && left) ? (right - left) : 100)),
            position: 'absolute',
            height: px(ROW_HEIGHT),
            zIndex: $scope.getEventZIndex(event),
        };
    }

    $scope.getEventZIndex = function (event) {
        const events = $scope.events.filter(e => event.group ? event.group === e.group : !e.group);
        const eventIndex = events.findIndex(e => e.id === event.id) ?? 0;
        const eventGroupZIndex = $scope.getOption(`event.zIndex.${event.group ?? '_default'}`, EVENT_Z_INDEX);

        return eventGroupZIndex + eventIndex;
    }
    /**
     * 
     * @param {object} event 
     * @returns {object} meta
     */
    $scope.getEventPositionStatus = function (event) {
        let meta = {
            overflowStart: false,
            overflowEnd: false,
        };
        var startId = schedulerService.generateCellId(event.resource, moment(event.start).format('YYYY'), moment(event.start).week());
        var endId = schedulerService.generateCellId(event.resource, moment(event.end).format('YYYY'), moment(event.end).week());
        var $startCell = $('#' + startId);
        var $endCell = $('#' + endId);

        if (moment(event.start).isAfter($scope.end) || moment(event.end).isBefore($scope.start)) {
            return meta;
        }

        if ($startCell.length == 0) {
            meta.overflowStart = true;
        }

        if ($endCell.length == 0) {
            meta.overflowEnd = true;
        }

        return meta;
    }
    /**
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     */
    $scope.onEventMouseEnter = function (event, eventIndex, jsEvent) {
        $scope.cancelBubblePanel();
        $scope.triggerTimeout = $timeout(function () {
            $scope.showEventDetailDialog(event, jsEvent);
        }, $scope.getOption('event.bubbleDelay'));
    };

    /**
     * Handle click event when clicking event 
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     * @param {angularEvent} jsEvent 
     */
    $scope.onEventClick = function (event, eventIndex, jsEvent) {
        $scope.$ctrl.onEventClick(event, eventIndex, jsEvent);
    };

    /**
     * Handle event when mouse leave schedule event (schedule event lose focus)
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     * @param {} jsEvent 
     */
    $scope.onEventMouseLeave = function (event, eventIndex, jsEvent) {
        $scope.closeBubblePanel();
        $scope.cancelBubblePanel();
        $timeout(function () {
            $scope.closeBubblePanel();
        }, $scope.getOption('event.bubbleDelay'));
    };

    $scope.getOption = function (optionPath, defaultValue) {
        var path = optionPath;
        if (typeof optionPath === 'string') {
            path = optionPath.split('.');
        }

        return resolverService.resolve(
            [$scope.options].concat(path), 
            resolverService.resolve(
                [$scope.defaultOptions].concat(path), 
                defaultValue
            )
        );
    };

    /**
     * 
     * @param {event} event 
     * @param {number} eventIndex 
     */
    $scope.getEventClass = function (event, eventIndex) {
        var res = [SCHEDULER_EVENT_CLASS, SCHEDULER_EVENT_CLASS + '-' + event.id];
        var positionStatus = $scope.getEventPositionStatus(event);

        if (event.className) {
            res.push(event.className);
        }

        if (positionStatus.overflowStart) {
            res.push(SCHEDULER_EVENT_CLASS + '-overflow-start');
        }

        if (positionStatus.overflowEnd) {
            res.push(SCHEDULER_EVENT_CLASS + '-overflow-end');
        }

        if ($scope.$ctrl.onEventClick) {
            res.push(SCHEDULER_EVENT_CLASS + '-clickable');
        }

        if (event.group) {
            res.push(SCHEDULER_EVENT_CLASS + '-' + event.group);
        }

        return res;
    }

    /**
     * @param {object} event 
     * @param {object} jsEvent 
     */
    $scope.showEventDetailDialog = function (event, jsEvent) {
        if (event.bubbleHtml) {
            /**
             * Set bubble z-index to avoid hidden bubble view
             */
            var bubbleIndex = EVENT_Z_INDEX + $scope.events.length + 10;
            var posX = 'center';
            var position = $mdPanel.newPanelPosition()
                .relativeTo(jsEvent.target)
                .addPanelPosition(posX, 'below');
    
            var from = $(jsEvent.target).offset();
            from.top += $(jsEvent.target).outerHeight();
            var panelAnimation = $mdPanel.newPanelAnimation()
                .openFrom(jsEvent.target)
                .duration(200)
                .closeTo(jsEvent.target)
                .withAnimation($mdPanel.animation.FADE);
    
            var config = {
                attachTo: angular.element(document.body),
                controller: EventDetailDialogController,
                controllerAs: 'ctrl',
                templateUrl: 'event-detail-panel.html',
                panelClass: 'event-detail-panel',
                position: position,
                animation: panelAnimation,
                locals: {
                    activeEvent: event,
                    zIndex: bubbleIndex,
                },
                hasBackdrop: false,
                openFrom: jsEvent,
                propagateContainerEvents: true,
                zIndex: bubbleIndex,
                groupName: 'bubble',
            };

            $scope.closeBubblePanel();
            $mdPanel.open(config).then(ref => $scope.mdPanelRef = ref);
        }
	};

    $scope.cancelBubblePanel = function () {
        if ($scope.triggerTimeout) {
            $timeout.cancel($scope.triggerTimeout);
            $scope.triggerTimeout = null;
        }
    };

    $scope.closeBubblePanel = function () {
        if ($scope.mdPanelRef) {
            $scope.mdPanelRef.close();
            $scope.mdPanelRef = null;
        }
    };

    /**
     * @param {number} resourceId 
     * @returns {object} overlaps
     */
    $scope.computeResourceEventOverlap = function (resourceId) {
        var resourceEvents = $scope.events.filter(e => e.resource === resourceId);
        var overlaps = {};

        for (var i = 0; i < resourceEvents.length - 1; i++) {
            var event1 = resourceEvents[i];
            let overlap = {
                count: 0,
                start: moment(event1.start),
                end: moment(event1.end),
                events: [event1],
                group: event1.group,
            };

            for (var j = i + 1; j < resourceEvents.length; j++) {
                var event2 = resourceEvents[j];
                var sameGroup = (!event1.group && !event2.group) || (event1.group === event2.group);
                if (sameGroup && event2.start.isSameOrBefore(moment(overlap.end)) && event2.end.isSameOrAfter(moment(overlap.start))) {
                    if (event2.start.isBefore(moment(overlap.start))) {
                        overlap.start = moment(event2.start);
                    }
                    if (event2.end.isAfter(moment(overlap.end))) {
                        overlap.end = moment(event2.end);
                    }
                    overlap.events.push(event2);
                    overlap.count++;
                }
            }
            overlaps[event1.id] = overlap;
        }

        for (var i in overlaps) {
            if (overlaps[i].count === 0) {
                delete(overlaps[i]);
            }
        }

        return overlaps;
    };

    /**
     * 
     * @param {number} resourceId 
     * @returns {number} factor
     */
    $scope.countResourceEventOverlap = function (resourceId) {
        var factor = 0;
        var overlaps = $scope.computeResourceEventOverlap(resourceId);

        if (Object.keys(overlaps).length > 0) {
            factor = Math.max.apply(null, Object.values(overlaps).map(overlap => overlap.count));
        }

        return factor;
    };
};

SchedulerController.$inject = [
    '$scope', 
    '$mdDialog',
    '$mdPanel',
    '$timeout',
    'moment',
    'px',
    'calendarService', 
    'resolverService',
    'schedulerService',
    'DEFAULT_CELL_WIDTH',
    'DEFAULT_DATE_FORMAT',
    'SCHEDULER_COLUMN_CLASS',
    'SCHEDULER_EVENT_CLASS',
    'EVENT_Z_INDEX',
    'ROW_HEIGHT',
    'DEFAULT_BUBBLE_TIMEOUT',
    'SCHEDULER_DATE_CELL_CLASS',
    'SCHEDULER_HEADER_WEEK_CLASS',
    'SCHEDULER_HEADER_MONTH_CLASS',
    'SCHEDULER_HEADER_YEAR_CLASS',
    'SCHEDULE_RESOURCE_HEADER_CLASS',
    'SCHEDULE_HEADER',
];

angular.module('schedulerModule').component('appScheduler', {
    template: schedulerTemplate,
    controller: SchedulerController,
    bindings: {
        /**
         * Array of resources :
         *  - [{...}, {...}, ...]
         * Resource object structure:
         * {
         *      cdtTrx: 'Borisav',
         *      constructionSite: 'ADF 001',
         *      workType: 'Travaux sur existant',
         *      area: '',
         *      turnover: 9786.99,
         *      invoiced: '',
         *      remainsToInvoice: 9786.99,
         * }
         */
        resources: '=',
        /**
         * Data structure: array of object
         *      - [{...}, {...}, ...]
         * Object structure:
         * {
         *      label: 'Chantier',
         *      field: 'constructionSite',
         *      className: 'chantier-class',
         *      headerClassName: 'text-uppercase text-center',
         *      formatter: function(res, resource, index) {
         *          return res ? '<b>' + res + '</b>' : '';
         *      },
         * }
         * Oject argument explaination:
         *      - label: label to show in the table header
         *      - field: resource field to display in each cell
         *      - className: cell data class, bind to each data td cell
         *      - headerClassName: Bind to the column header th
         *      - formatter: function to format the cell value (can be anything)
         *          args:
         *              - res: data of the cell
         *              - resource: resouce object
         *              - i: index of the resource     
         */
        columns: '=',
        /**
         * Moment
         */
        start: '=',
        /**
         * Moment
         */
        end: '=',
        /**
         * Object
         */
        options: '=',
        /**
         * Header class name
         * String
         */
        headerYearClassName: '=',
        /**
         * Header month class name
         */
        headerMonthClassName: '=',
        /**
         * Header week class name
         */
        headerWeekClassName: '=',
        // SCHEDULER EVENTS HANDLER
        /**
         * Callback
         * Args:
         *  - resource (object)
         *      structure:
         *          {
         *              area: string,
         *              cdtTrx: string,
         *              constructionSite: string,
         *              invoiced: string
         *              remainsToInvoice: number,
         *              turnover: number,
         *              workType: number,
         *          }
         *  - column (object)
         *      structure:
         *          {
         *              field: string
         *              label: string
         *              width: string | number
         *          }
         *  - columnIndex (number)
         */
        onRowClick: '=',
        /**
         * Callback 
         * - column
         *      structure:
         *          {
         *              className: string,
         *              classNameFormatter: callback (Object res, Object resource, Number index),
         *              field: string,
         *              formatter: callback (Object res, Object resource, Number index)
         *              headerClass: string
         *              headerColumnFormatter: callback (Object column, Number index)
         *              label: string,
         *              width: string | number,
         *              columnIndex: number
         *          }
         * - columnIndex: number,
         */
        onColumnHeaderClick: '=',
        /**
         * Callback
         * - yearObject: object
         *      structure:
         *          {
         *              monthsCount: number,
         *              name: string,
         *              weeksCount: number,
         *          }
         * 
         * - yearIndex: number
         */
        onHeaderYearClick: '=',
        /**
         * Callback
         * - monthOject: object
         *      structure:
         *          {
         *              monthIndex: number,
         *              monthObject: object {name: string, monthNumber: string, year: string, weeksCount: number}
         *          }
         * - monthIndex: number
         */
        onHeaderMonthClick: '=',
        /**
         * Callback
         * - weekObject: object
         *      structure:
         *          {
         *              endDay: Moment,
         *              firstweek: boolean,
         *              lastweek: boolean,
         *              monthNumber: string
         *              startDay: Moment
         *              weekNumber: number
         *              year: string
         *          }
         * - weekIndex: number
         */
        onHeaderWeekClick: '=',
        /**
         * Callback
         * - resource: object
         *      structure:
         *          {
         *              area: string,
         *              cdtTrx: string,
         *              constructionSite: string,
         *              invoiced: string
         *              remainsToInvoice: number,
         *              turnover: number,
         *              workType: number,
         *          }
         * - week: object
         *      structure:
         *          {
         *              year: string,
         *              monthNumber: string,
         *              firstWeek: boolean,
         *              lastWeek: boolean,
         *              weekNumber: number,
         *              startDay: Moment,
         *              endDay: Moment,
         *          }
         * - weekIndex: number
         * - resourceIndex: number
         */
        onCellClick: '=',
        /**
         * - Event click 
         */
        onEventClick: '=',
        events: '=',
    }
});