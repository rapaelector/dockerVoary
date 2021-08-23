import schedulerTemplate from './template.html';
import get from 'lodash.get';
import EventDetailDialogController from './event-detail.controller';
import numberFormat from "./../../utils/number_format";

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
    BUBBLE_DEFAULT_WIDTH,
    BUBBLE_CLASS,
    SCHEDULER_COLUMN_HEADER,
    BACKGROUND_COLOR,
    SCHEDULER_COLUMN_STICKY_CLASS,
    SCHEDULER_BORDER_COLOR,
    SCHEDULER_COLUMN_VERTICAL_DIVIDER,
    SCHEDULER_LAST_COLUMN,

    POSITION_FIX_STICKY_COLUMNS_LEFT,
    POSITION_FIX_STICKY_COLUMNS_EXTRA_WIDTH,
    POSITION_FIX_STICKY_COLUMNS_MINUS_LEFT,
    POSITION_FIX_STICKY_COLUMNS_WIDTH,
    POSITION_FIX_STICKY_COLUMNS_END_FIRST_WEEK_WIDTH,
    POSITION_FIX_STICKY_COLUMNS_END_FIRST_WEEK_MINUS_LEFT,
    POSITION_FIX_END_LAST_WEEK_EXTRA_WIDTH,
    POSITION_FIX_END_FIRST_WEEK_EXTRA_WIDTH,
    POSITION_FIX_EXTRA_WIDTH,
    POSITION_FIX_STICKY_COLUMNS_FIRST_WEEK_WIDTH,
    POSITION_FIX_MINUS_LEFT,
    POSITION_FIX_START_FIRST_WEEK_MINUS_LEFT,
    POSITION_FIX_START_FIRST_WEEK_WIDTH,
    POSITION_FIX_END_LAST_WEEK_WIDTH,
    POSITIONS_FIX,
    // Event box shadow
    BOX_SHADOW,
    BOX_SHADOW_STICKY,
    // Footer className
    FOOTER_BLANK_CELL_CLASS_NAME,
    FOOTER_TITLE_CLASS_NAME,
    FOOTER_TOTAL_CLASS_NAME,
    FOOTER_TITLE,
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
        columnHeaders: [],
        columns: [],
    };
    $scope.events = [];
    $scope.resources = [];
    $scope.columns = [];
    $scope.totals = [];
    $scope.mdPanelRef = null;
    $scope.triggerTimeout = null;
    $scope.bubbleDefaultConfig = {};
    $scope.stickyColumns = false;
    $scope.forceSticky = false;

    this.$onInit = function () {
        $(function () {
            $('[data-toggle="popover"]').popover()
        });
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $mdPanel.newPanelGroup('bubble', {
            maxOpen: 1,
        });
        $scope.defaultOptions = {
            event: {
                bubbleDelay: DEFAULT_BUBBLE_TIMEOUT,
                bubbleHtml: {
                    zIndex: EVENT_Z_INDEX,
                    width: px(BUBBLE_DEFAULT_WIDTH),
                },
                boxShadow: BOX_SHADOW,
                boxShadowSticky: BOX_SHADOW_STICKY,
            },
            cell: {
                width: DEFAULT_CELL_WIDTH,
            },
            backgroundColor: BACKGROUND_COLOR,
            positionsFix: POSITIONS_FIX,
            footerTitle: FOOTER_TITLE,
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

    $scope.$watch('$ctrl.positionsFix', function () {
        $scope.positionsFix = $scope.$ctrl.positionsFix;
    }, true);

    $scope.buildPlaceholderResource = function (index) {
        return {
            id: 1000000000 + Math.random() * 100000000,
        };
    }

    $scope.$watch('$ctrl.resources', function () {
        $scope.resources = $scope.$ctrl.resources ? $scope.$ctrl.resources : [];
        if ($scope.$ctrl.minRowCount && $scope.resources.length < $scope.$ctrl.minRowCount) {
            for (var i = 0; i < $scope.$ctrl.minRowCount - $scope.resources.length; i++) {
                $scope.resources.push($scope.buildPlaceholderResource(i));
            }
        }
    }, true);

    $scope.$watch('$ctrl.columns', function () {
        $scope.columns = Array.isArray($scope.$ctrl.columns) ? $scope.$ctrl.columns.filter(c => c.visible === undefined || c.visible) : [];
        $scope.stickyColumns = $scope.columns ? $scope.columns.filter(c => c.sticky).length > 0 : false;
        // $scope.updateTableStyles();
        $scope.columns.forEach((column, columnIndex) => {
            $timeout(() => {
                $scope.styles.columnHeaders[columnIndex] = $scope.getResourceHeaderColumnStyle(column, columnIndex);
                $scope.styles.columns[columnIndex] = $scope.getResourceColumnStyle(column, columnIndex);
                if (columnIndex === ($scope.columns.length - 1)) {
                    $timeout(() => {
                        $scope.fixStickyColumns();
                    }, 100);
                }
            }, 100 * columnIndex);
        });
    }, true);

    $scope.$watch('$ctrl.totals', function () {
        if ($scope.$ctrl.totals) {
            $scope.totals = [...$scope.$ctrl.totals];
        }
    }, true);

    $scope.fixStickyColumns = function () {
        if (!$scope.stickyColumns || 
            !$scope.columns || 
            $scope.columns.length === 0 || 
            $scope.columns[$scope.columns.length - 1].sticky ||
            $scope.columns.filter(c => c.sticky).length === 0
        ) {
            return;
        }
        var lastStickyIndex = 0;
        $scope.columns.forEach((c, index) => lastStickyIndex = c.sticky ? index : lastStickyIndex);
        var lastStickyWidth = $scope.styles.columnHeaders[lastStickyIndex].width;
        var lastColumnWidth = $scope.styles.columnHeaders[$scope.columns.length - 1].width;
        var updateIndex = lastStickyWidth < lastColumnWidth ? lastStickyIndex : ($scope.columns.length - 1);
        var newWidth = lastStickyWidth < lastColumnWidth ? lastColumnWidth : lastStickyWidth;
        $scope.styles.columnHeaders[updateIndex].width = newWidth;
        $scope.styles.columnHeaders[updateIndex].minWidth = newWidth;
        $scope.updateTableStyles();
    };

    $scope.$watch('$ctrl.options', function () {
        $scope.options = $.extend(true, {}, $scope.defaultOptions, $scope.$ctrl.options);
        $scope.updateTableStyles();
    }, true);

    $scope.$watch('$ctrl.forceSticky', function () {
        $scope.forceSticky = $scope.$ctrl.forceSticky;
    });
    
    /**
     * Add additional attributes into the event
     * added attributes
     *      - isStartFirstWeek {boolean} to know if the event have start first week
     *      - isStartLastWeek {boolean} to know if the event have start last week 
     *      - isEndFirstWeek {boolean} to know if the event have end first week
     *      - isEndLastWeek {boolean} to know if the event have end last week
     */
    $scope.$watch('$ctrl.events', function () {
        if ($scope.$ctrl.events) {
            $scope.events = [...$scope.$ctrl.events].map(event => {
                var schedulerMeta = {
                    isStartFirstWeek: calendarService.isFirstWeek(event.start),
                    isStartLastWeek: calendarService.isLastWeek(event.start),
                    isEndFirstWeek: calendarService.isFirstWeek(event.end),
                    isEndLastWeek: calendarService.isLastWeek(event.end),
                };

                return {
                    ...event,
                    schedulerMeta,
                };
            });
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
            res = get(resource, column.field);
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
    $scope.getCellClassName = function(resource, column, index, columnIndex) {
        var cellClass = column.field.replaceAll('.', '-');
        var res = [SCHEDULER_COLUMN_CLASS, SCHEDULER_COLUMN_CLASS + '-' + cellClass];
        var isLastColumn =  ($scope.columns.length - 1) === columnIndex;

        if (column.className) {
            res.push(column.className);
        }

        if (column.classNameFormatter) {
            var className = column.classNameFormatter(res, resource, index);
            res.push(className);
        }

        if (column.sticky) {
            res.push(SCHEDULER_COLUMN_STICKY_CLASS);
        }

        if (isLastColumn) {
            res.push(SCHEDULER_LAST_COLUMN);
        }

        return res;
    }
    /**
     * Get resource header column class
     * 
     * @param {object} column 
     * @returns {array} array of string
     */
    $scope.getHeaderCellClassName = function(column, columnIndex) {
        var columnClass = column.field.replace('.', '-');
        var res = [SCHEDULE_RESOURCE_HEADER_CLASS, SCHEDULE_RESOURCE_HEADER_CLASS + '-' + columnClass];
        var isLastColumn =  ($scope.columns.length - 1) === columnIndex;

        if (column.headerClassName) {
            res.push(column.headerClassName);
        }

        if (column.sticky) {
            res.push(SCHEDULER_COLUMN_STICKY_CLASS);
        }

        if (isLastColumn) {
            res.push(SCHEDULER_LAST_COLUMN);
        }

        return res;
    }

    /**
     * Get resource header column style
     * 
     * @param {object} column 
     * @returns {object} object of style
     */
    $scope.getResourceHeaderColumnStyle = function (column, columnIndex) {
        var style = {};
        var stickynessStyle = $scope.getSticknessStyle(column, columnIndex, false);

        if (column.width) {
            style.width = px(parseInt(column.width));
            style.minWidth = px(parseInt(column.width));
            style.maxWidth = px(parseInt(column.width));
        }

        return {...style, ...stickynessStyle};
    };
    /**
     * Get resource column style
     * 
     * @param {object} column 
     * @param {number} columnIndex
     * @returns {object} style
     */
    $scope.getResourceColumnStyle = function (column, columnIndex) {
        var style = {};
        var stickynessStyle = $scope.getSticknessStyle(column, columnIndex, true);

        return {...style, ...stickynessStyle};
    };

    $scope.getResourceColumnDivStyle = function (column, columnIndex) {
        return {
            width: column.width,
            minWidth: column.width,
            maxWidth: column.width,
        };
    }
    /**
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     * @returns {object} style
     */
    $scope.getSticknessStyle = function (column, columnIndex, debug) {
        var style = {};
        var left = 0;
        var zIndexes = $scope.getOption(`event.zIndex`) ?? EVENT_Z_INDEX;
        var zIndex = typeof zIndexes === 'object' ? Math.max(...Object.values(zIndexes)) : zIndexes;
        var stick = false;
        if (column.sticky) {
            $scope.columns.forEach((c, index) => {
                if (c.sticky && (index < columnIndex)) {
                    const header$ = $('#' + $scope.getResourceHeaderId(c, index));
                    if (header$.length) {
                        left += c.width;
                    }
                }
            });
            stick = true;
        }
        
        if (stick) {
            style.position = 'sticky';
            style.left = left;
            style.zIndex = zIndex + 10;
        }

        return style;
    };

    /**
     * 
     * @param {object} column 
     * @param {number} columnIndex 
     * @returns {string}
     */
    $scope.getResourceHeaderId = function (column, columnIndex) {
        return [SCHEDULER_COLUMN_HEADER, column.field.replaceAll('.', '-'), columnIndex].join('-');
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
            minWidth: px(width),
            height: px(rowHeight + (resolverService.resolve([$scope.$ctrl, 'resources', 'length'], 0) === (resourceIndex + 1) ? 0 : 0)),
        };
    };

    /**
     * Update table styles
     */
    $scope.updateTableStyles = function () {
        const tableStyles = {};
        let w = 0;
        if (!$scope.weeks || !$scope.columns) {
            return;
        }
        let OK = true;
        for (const i in $scope.styles.columnHeaders) {
            const columnWidth = $scope.styles.columnHeaders[i].width;
            if (isNaN(parseInt(columnWidth))) {
                console.warn('Invalid width at index', i);
                OK = false;

                break;
            }
            w += parseInt(columnWidth);
        }
        const cellWidth = resolverService.resolve([$scope, 'options', 'cell', 'width'], DEFAULT_CELL_WIDTH);

        w += $scope.weeks.length * cellWidth;
        if (OK && !isNaN(w)) {
            tableStyles.width = px(w);
            // tableStyles['table-layout'] = 'fixed';
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
    $scope.getResourceHeaderEmptyColumnStyle = function (column, columnIndex) {
        var columnMergeSticky = $scope.getSticknessStyle(column, columnIndex);
        var styles = {
            width: column.width,
            maxWidth: column.width,
            minWidth: column.width,
            backgroundColor: $scope.getOption('backgroundColor'),
            borderTopColor: $scope.getOption('backgroundColor'),
            borderLeftColor: $scope.getOption('backgroundColor'),
            borderRightColor: $scope.getOption('backgroundColor'),
        };

        var stickyStyle = {};
        var zIndexes = $scope.getOption(`event.zIndex`) ?? EVENT_Z_INDEX;
        var zIndex = typeof zIndexes === 'object' ? Math.max(...Object.values(zIndexes)) : zIndexes;

        if ((columnIndex === ($scope.columns.length - 1))) {
            var columns =$scope.columns;
            var stickyColumns = columns.filter(c => c.sticky);
            if (stickyColumns.length > 0) {
                var left = 0;
                $scope.columns.forEach((c, index) => {
                    if (c.sticky && (stickyColumns.indexOf(c) < stickyColumns.length - 1) && (index < columns.length - 1)) {
                        const header$ = $('#' + $scope.getResourceHeaderId(c, index));
                        if (header$.length) {
                            left += c.width;
                        }
                    }
                });
                stickyStyle = {
                    position: 'sticky',
                    left: left + 1,
                    zIndex: zIndex + 10,
                }
            }
        }

        return {...styles, ...columnMergeSticky, ...stickyStyle};
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

        return {
            width: px(width),
        };
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
     * 
     * @param {object} month 
     * @param {number} monthIndex 
     * @returns {array} res
     */
    $scope.getFooterMonthClassName = function (month, monthIndex) {
        var res = [SCHEDULER_HEADER_MONTH_CLASS, 'text-right pr-2'];
        
        if (moment(`${month.year}-${month.monthNumber}`, 'YYYY-M').isSame(moment(), 'month')) {
            res.push(SCHEDULE_HEADER + '-month-current');
        }

        return res;
    };

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

    /**
     * 
     * @param {object} resource 
     * @param {object} week 
     * @param {number} weekIndex 
     * @param {number} resourceIndex 
     * @returns {array} res
     */
    $scope.getDateCellClassName = function (resource, week, weekIndex, resourceIndex) {
        return $scope.getWeekCellClassName(week, weekIndex);
    };

    /**
     * 
     * @param {object} week 
     * @param {number} weekIndex 
     * @returns {array} res
     */
    $scope.getDateCellFooterClassName = function (week, weekIndex) {
        return $scope.getWeekCellClassName(week, weekIndex);
    }

    $scope.getWeekCellClassName = function (week, weekIndex) {
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
        var boxShadow = '';
        var boxShadowValue = `-2px 0px 0px ` + event.backgroundColor + `aa`;
        
        if ($scope.stickyColumns && $scope.getOption('event.boxShadowSticky')) {
            boxShadow = boxShadowValue;
        }

        if (!$scope.stickyColumns && $scope.getOption('event.boxShadow')) {
            boxShadow = boxShadowValue;
        }

        return {
            ...eventStyle,
            backgroundColor: event.backgroundColor,
            boxShadow: boxShadow,
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
        const hideStyle = {
            opacity: 0,
            display: 'none',
        };
        if (moment(event.start).isAfter($scope.end) || moment(event.end).isBefore($scope.start)) {
            return hideStyle;
        }

        var width = 100;
        var startId = schedulerService.generateCellId(event.resource, moment(event.start).format('YYYY'), moment(event.start).week());
        var endId = schedulerService.generateCellId(event.resource, moment(event.end).format('YYYY'), moment(event.end).week());
        var $startCell = $('#' + startId);
        var $endCell = $('#' + endId);
        var extraWidth = 0;

        if ($startCell.length == 0) {
            var firstMondayDate = calendarService.nextMonday(moment($scope.start));
            startId = schedulerService.generateCellId(event.resource, firstMondayDate.format('YYYY'), firstMondayDate.week());
            $startCell = $('#' + startId);
        }

        if ($endCell.length == 0) {
            endId = schedulerService.generateCellId(event.resource, moment($scope.end).format('YYYY'), moment($scope.end).week());
            $endCell = $('#' + endId);
        }

        if ($endCell.length <= 0 || $startCell.length <= 0) {
            return hideStyle;
        }

        var { left, top, right } = $scope.getEventPosition($startCell, $endCell);
        var overlaps = $scope.computeResourceEventOverlap(event.resource);
        var overlapCount = 0;

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
                overlapCount = index;
            }
        }

        if (overlapCount < 1) {
            top += 0.5;
        }

        return {
            top: px(top),
            left: px(left),
            right: px(right),
            display: 'block',
            width: px(right - left),
            position: 'absolute',
            height: px(ROW_HEIGHT - 1),
            zIndex: $scope.getEventZIndex(event),
        };
    }

    /**
     * 
     * @param {jQueryObject} elem$ 
     * @returns {object} {left, top}
     */
    $scope.getCellPosition = function (elem$, isStart) {
        var leftFix =  parseInt(elem$.css("border-right-width").replace('px', '')) - parseInt(elem$.css("border-left-width").replace('px', ''));
        var borderLeftWidth = parseInt(elem$.css("border-left-width").replace('px', ''));

        var res = {
            left: elem$.position().left + (borderLeftWidth <= 2 ? 0 : borderLeftWidth) + 1,
            top: elem$.position().top
        };

        return res;
    };

    /**
     * Get the event position depent on the startCell element and endCell element
     * 
     * @param {jQueryObject} startCell$
     * @param {jQueryObject} endCell$ 
     * @return {object} {left, top, right}
     */
    $scope.getEventPosition = function (startCell$, endCell$) {
        var left = 0;
        var top = 0;
        var right = 0;

        if (startCell$.length > 0) {
            var startCellPosition = $scope.getCellPosition(startCell$);
            left = startCellPosition.left;
            top = startCellPosition.top;
        }

        if (endCell$.length > 0) {
            var endCellPosition = $scope.getCellPosition(endCell$);
            var borderLeft = endCellPosition.left + endCell$.innerWidth() + parseInt(endCell$.css("border-left-width").replace('px', ''));
            top = top ? top : endCellPosition.top;
            right = borderLeft;
        }

        return {
            left,
            top,
            right,
        };
    };

    /**
     * 
     * @param {object} event 
     * @returns {number}
     */
    $scope.getEventZIndex = function (event) {
        const eventGroupZIndex = $scope.getOption(`event.zIndex.${event.group ?? '_default'}`, EVENT_Z_INDEX);

        return eventGroupZIndex;
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
        var bubbleDelay = $scope.getOption('event.bubbleDelay');
        if (typeof bubbleDelay === 'object') {
            bubbleDelay = resolverService.resolve([bubbleDelay, event.group], resolverService.resolve([bubbleDelay, 'default'], DEFAULT_BUBBLE_TIMEOUT));
        }

        $scope.cancelBubblePanel();
        $scope.triggerTimeout = $timeout(function () {
            $scope.showEventDetailDialog(event, jsEvent);
        }, bubbleDelay);
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
        var res = [SCHEDULER_EVENT_CLASS, SCHEDULER_EVENT_CLASS + '-' + event.id, SCHEDULER_EVENT_CLASS, SCHEDULER_EVENT_CLASS + '-' + event.resource];
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
            res.push(SCHEDULER_EVENT_CLASS + '-group-' + event.group);
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
            var panelClass = [BUBBLE_CLASS];
            if (event.group) {
                panelClass.push(BUBBLE_CLASS + '-group-' + event.group);
            }

            var posX = 'center';
            var position = $mdPanel.newPanelPosition()
                .relativeTo(jsEvent.target)
                .addPanelPosition(posX, 'below');
    
            var from = $(jsEvent.target).offset();
            from.top += $(jsEvent.target).outerHeight();

            var config = $scope.buildBubbleConfig({
                bubbleIndex: resolverService.resolve([$scope, 'options', 'event', 'bubbleHtml', 'zIndex'], $scope.defaultOptions.event.bubbleHtml.zIndex),
                panelClass: panelClass.join(' '),
                position: position,
                jsEvent: jsEvent,
                groupName: event.group,
                locals: {
                    activeEvent: event,
                },
            });

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

    /**
     * 
     * @param {object} options
     *  - structure
     *      {
     *          panelClass: string
     *          zIndex: number
     *          locals: object {activeEvent}
     *          jsEvent: object,
     *          group: string,
     *          position: 
     *      } 
     */
    $scope.buildBubbleConfig = function (options) {
        var panelAnimation = $mdPanel.newPanelAnimation()
                .openFrom(options.jsEvent.target)
                .duration(200)
                .closeTo(options.jsEvent.target)
                .withAnimation($mdPanel.animation.FADE)
            ;

        return {
            attachTo: angular.element(document.body),
            controller: EventDetailDialogController,
            controllerAs: 'ctrl',
            templateUrl: 'event-detail-panel.html',
            panelClass: [BUBBLE_CLASS, resolverService.resolve([options, 'panelClass'], '')].join(' '),
            position: resolverService.resolve([options, 'position'], {}),
            animation: panelAnimation,
            locals: resolverService.resolve([options, 'locals'], {}),
            hasBackdrop: false,
            openFrom: resolverService.resolve([options, 'jsEvent']),
            propagateContainerEvents: true,
            zIndex: resolverService.resolve([options, 'bubbleIndex'], $scope.defaultOptions.event.bubbleHtml.zIndex),
            groupName: resolverService.resolve([options, 'group'], ''),
        };
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

    /**
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     */
    $scope.onEventHover = function (event, eventIndex, jsEvent) {
        $scope.showEventDetailDialog(event, jsEvent);
    };

    $scope.onEventClick = function (event, eventIndex, jsEvent) {
    };

    /**
     * @param {object} event 
     * @param {object} jsEvent 
     */
    $scope.showEventDetailDialog = function (event, jsEvent) {
        if (event.bubbleHtml) {
            var panelClass = [BUBBLE_CLASS];
            if (event.group) {
                panelClass.push(BUBBLE_CLASS + '-group-' + event.group);
            }

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
                .withAnimation($mdPanel.animation.FADE)
            ;
            var config = $scope.buildBubbleConfig({
                bubbleIndex: resolverService.resolve([$scope, 'options', 'event', 'bubbleHtml', 'zIndex'], $scope.defaultOptions.event.bubbleHtml.zIndex),
                panelClass: panelClass.join(' '),
                position: position,
                jsEvent: jsEvent,
                groupName: event.group,
                locals: {
                    activeEvent: event,
                },
            });

            if ($scope.mdPanelRef) {
                $scope.mdPanelRef.close();
                $scope.mdPanelRef = null;
            }
            $mdPanel.open(config).then(ref => $scope.mdPanelRef = ref);
        }
	};

    /**
     * Handle event when mouse leave schedule event (schedule event lose focus)
     * 
     * @param {object} event 
     * @param {number} eventIndex 
     * @param {object} jsEvent 
     */
    $scope.onEventBlur = function (event, eventIndex, jsEvent) {
        if ($scope.mdPanelRef) {
            $scope.mdPanelRef.close();
            $scope.mdPanelRef = null;
        }
    };

    /**
     * Format title of the event if the event have titleFormatter
     * 
     * @param {object} event
     * @return {any}  
     */
    $scope.formatTitle = function (event) {
        if (resolverService.resolve([$scope, 'options', 'event', 'titleFormatter'], null)) {
            return $scope.options.event.titleFormatter(event.title, event);
        }

        return event.title;
    }

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
     * Get resource header empty column class
     * 
     * @param {object} column
     * @param {number} columnIndex
     * @return {array} res 
     */
    $scope.getResourceHeaderEmptyColumnClass = function (column, columnIndex) {
        var res = [SCHEDULER_COLUMN_HEADER + '-top-cell'];
        var isLastIndex = ($scope.columns.length - 1) == columnIndex;
        if (isLastIndex) {
            res.push(SCHEDULER_LAST_COLUMN);
        }

        return res;
    };

    $scope.getEmptyStickyColumnStyle = function () {
        var left = 0;
        $scope.columns.forEach((c, index) => {
            if (c.sticky) {
                const header$ = $('#' + $scope.getResourceHeaderId(c, index));
                if (header$.length) {
                    left += header$.outerWidth();
                } else {
                    console.warn( $scope.getResourceHeaderId(c, index) + ' not found');
                }
            }
        });
        var zIndexes = $scope.getOption(`event.zIndex`) ?? EVENT_Z_INDEX;
        var zIndex = typeof zIndexes === 'object' ? (Math.max(...Object.values(zIndexes)) + 100) : zIndexes;

        var styles = {
            left: px(left - 2),
            zIndex: zIndex,
        };

        return styles;
    };

    $scope.getEmptyStickyColumnClass = function () {
        var classes = [SCHEDULER_COLUMN_VERTICAL_DIVIDER];

        return classes;
    };

    /**
     * 
     * @param {object} month 
     * @param {number} monthIndex 
     * @return {any}
     */
    $scope.displayTotal = function (month, totalIndex, monthIndex) {
        /**
         * Use in month
         * - monthNumber
         * - year
         */
        var total = $scope.$ctrl.totals[totalIndex].find((total) => {
            return (+total.year == +month.year) && (+total.month == +month.monthNumber);
        });

        return total && total.amount ? numberFormat(total.amount, 0, '.', ' ') : '';
    };

    function visibleColumnFilter(column) {
        return column.visible === undefined || column.visible;
    }

    $scope.getTotalBlankColspan = function () {
        const visibleCount = $scope.columns.filter(visibleColumnFilter).length;

        return  Math.max(0, visibleCount - 3);
    };
    
    $scope.getTotalTitleColspan = function () {
        var visibleCount = $scope.columns.filter(visibleColumnFilter).length;

        return visibleCount < 2 ? 0 : (visibleCount === 2 ? 1 : 2);
    };

    $scope.getTotalColspan = function () {
        return $scope.columns.filter(visibleColumnFilter).length < 1 ? 0 : 1;
    };

    /**
     * 
     * @param {number} totalIndex 
     * @returns 
     */
    $scope.getTotal = function (totalIndex) {
        var totalColumn = $scope.columns.find(c => c.isTotal);
        if (totalColumn) {
            return numberFormat(resolverService.resolve([totalColumn, 'totals', totalIndex], 0), 2, ',', ' ') + ' €';
        }

        return '';
    };

    $scope.getFooterBlankCellClassName = function () {
        var res = [FOOTER_BLANK_CELL_CLASS_NAME];

        return res;
    };

    $scope.getFooterTitleClassName = function () {
        var res = [FOOTER_TITLE_CLASS_NAME];

        return res;
    };

    $scope.getFooterTotalClassName = function () {
        var res = [FOOTER_TOTAL_CLASS_NAME];

        return res;
    };

    $scope.getFooterTitle = function () {
        return $scope.getOption('footerTitle');
    };
}

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
    'BUBBLE_DEFAULT_WIDTH',
    'BUBBLE_CLASS',
    'SCHEDULER_COLUMN_HEADER',
    'BACKGROUND_COLOR',
    'SCHEDULER_COLUMN_STICKY_CLASS',
    'SCHEDULER_BORDER_COLOR',
    'SCHEDULER_COLUMN_VERTICAL_DIVIDER',
    'SCHEDULER_LAST_COLUMN',

    'POSITION_FIX_STICKY_COLUMNS_LEFT',
    'POSITION_FIX_STICKY_COLUMNS_EXTRA_WIDTH',
    'POSITION_FIX_STICKY_COLUMNS_MINUS_LEFT',
    'POSITION_FIX_STICKY_COLUMNS_WIDTH',
    'POSITION_FIX_STICKY_COLUMNS_END_FIRST_WEEK_WIDTH',
    'POSITION_FIX_STICKY_COLUMNS_END_FIRST_WEEK_MINUS_LEFT',
    'POSITION_FIX_END_LAST_WEEK_EXTRA_WIDTH',
    'POSITION_FIX_END_FIRST_WEEK_EXTRA_WIDTH',
    'POSITION_FIX_EXTRA_WIDTH',
    'POSITION_FIX_STICKY_COLUMNS_FIRST_WEEK_WIDTH',
    'POSITION_FIX_MINUS_LEFT',
    'POSITION_FIX_START_FIRST_WEEK_MINUS_LEFT',
    'POSITION_FIX_START_FIRST_WEEK_WIDTH',
    'POSITION_FIX_END_LAST_WEEK_WIDTH',
    'POSITIONS_FIX',
    // Event box shadow
    'BOX_SHADOW',
    'BOX_SHADOW_STICKY',
    // Footer className
    'FOOTER_BLANK_CELL_CLASS_NAME',
    'FOOTER_TITLE_CLASS_NAME',
    'FOOTER_TOTAL_CLASS_NAME',
    // Footer title
    'FOOTER_TITLE',
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
         *      sticky: boolean,
         *      visible: boolean {true}
         * }
         * Oject argument explaination:
         *      - label: label to show in the table header
         *      - field: resource field to display in each cell
         *      - className: cell data class, bind to each data td cell
         *      - headerClassName: Bind to the column header th
         *      - sticky: boolean if the column should sticky
         *      - visible: boolean default valeu true; Make all columns visible
         *      - formatter: function to format the cell value (can be anything)
         *          args:
         *              - res: data of the cell
         *              - resource: resouce object
         *              - i: index of the resource
         * 
         *      @param {Object} res 
         *      @param {Object} resource 
         *      @param {any} index 
         *      @returns {string}
         *      - classNameFormatter: function to format the class
         *      
         *  
         *      Change resource header content to html
         *  
         *      @param {Object} column 
         *      @param {any} index 
         *      @returns {string} part of html
         *      - headerColumnFormatter: function (column, index) {
         */
        columns: '=',
        /**
         * Data structure: array of object
         *      structure:
         *          [
         *              {
         *                  backgroundColor: "#1f497d"
         *                  end: "2021-01-23T00:00:00+00:00"
         *                  id: 14
         *                  project: {id: 3}
         *                  resource: 3
         *                  start: "2020-12-31T00:00:00+00:00"
         *                  type: "shade_house"
         *              },
         *              {...}
         *          ]
         */
        events: '=',
        /**
         * Array of array
         *      Structure:[[...], [....]]
         *      [
         *          [
         *              {
         *                  "amount": 9666.666666666666,
         *                  "year": "2021",
         *                  "month": "01",
         *              },
         *              {....}
         *          ],
         *          [],
         *      ]
         */
        totals: '=',
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
         * Structure with default config
         *  {
         *       defaultCellWidth: DEFAULT_CELL_WIDTH, 24
         *       cell: {
         *           width: 24,
         *       },
         *       event: {
         *           zIndex: {
         *               'payment': 9999,
         *               _default: number // default z-index if zIndex group have no z-index
         *           },
         *           bubbleHtml: {
         *               zIndex: 9999999,
         *          },
         *          titleFormatter: function (title, event) {},
         *          bubbleDelay: number | object {group: number, default: number},
         *          boxShadow: boolean (true),
         *          boxShadowSticky: boolean (true)
         *       },
         *       backgroundColor: string "#f4f6f9",
         *       positionsFix: {
         *          stickyColumnsLeft: number
         *          stickyColumnsExtraWidth: number
         *          left: number,
         *          width: number,
         *          stickyColumnsEndFirstWeekWidth: number,
         *          stickyColumnsEndFirstWeekMinusLeft: number,
         *          endLastWeekExtraWidth: number
         *          endFirstWeekExtraWidth: number
         *          extraWidth: number
         *      },
         *      footerTitle: string(Total)
         *   }
         */
        options: '=',
        /**
         * Header class name
         * String
         */
        headerYearClassName: '=',
        /**
         * Header month class name
         * String
         */
        headerMonthClassName: '=',
        /**
         * Header week class name
         * String
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
         * - Event click callback
         */
        onEventClick: '=',
        /**
         * Force the width of the last resource column width like the width of last sticky column
         * If the last column width is bigger than the last sticky column 
         *      then take the last column width and change the last sticky column width like the last column width
         * 
         * - boolean
         * - default value false
         * 
         */
        forceSticky: '=',
        minRowCount: '=',
    }
});