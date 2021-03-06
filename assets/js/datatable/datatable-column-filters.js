import { appConsole } from '../utils';
import * as moment from 'moment';
moment.locale('fr');
window.moment = moment;

const RANGE_SLIDER_SELECTOR = '.range-slider';
const RANGE_DROPDOWN_SELECTOR = '.dropdown.range-dropdown';
const RANGE_DROPDOWN_WIDTH = 240;
const CLEAR_BUTTON_CONTAINER = '.clear-filters';
const $APP_FILTER_CONTAINER = $('.app-filters-container')

var filtersInitialized = false;

function initColumnFilters (container, dt, options, debug = false) {
    appConsole('info', debug, $container);
    var timeId = null;
    var $container = $(container);
    $APP_FILTER_CONTAINER.show();
    var defaultOptions = {
        clearFilters: {
            // clear filters button container
            container: 'clear-filters',
            // clear filters button attributes that will be provided to buildElement()
            btn: {
                wrapper: 'button',
                attr: {
                    'class': 'btn clear-filter-button d-inline-block',
                },
                content: 'Annuler tous les filtres',
            },
        },
    };
    var options = $.extend(true, {}, defaultOptions, options);

    $('.app-filters-container table thead tr th').click(function (e) {
        var target = $(this).data('target');
        $(target).get('0').scrollIntoView();
        $(target).focus();
    });

    $($container).find('input, select').on('keydown change', function (e) {
        var $filter = $(this);
        if (timeId) {
            clearTimeout(timeId);
        }
        timeId = filterChangeHandler($filter, dt);
    });
    
    initFilterDropdown($container, options);
    initDateRangepicker(options);
    initClearButton(dt, options);

    filtersInitialized = true;
}

function filterChangeHandler($filter, dt) {
    var index = $filter.data('column-index');
    var val = $filter.val();

    return setTimeout(function () {
        if (index > -1) {
            dt.column(index).search(val).draw();
        }
    }, 1000);
}

export {
    initColumnFilters
};

function initDateRangepicker(options) {
    appConsole('info', true, 'init daterange picker', $('.input-daterange > input').length);
    
    $('.input-daterange > input').each(function () {
        var $input = $(this);
        var customeRangeLabel = (window._locale == 'fr' ? 'Date personnalis??' : 'Custom range');
        var options = {
            autoclose: true,
            locale: {
                format: 'DD/MM/YYYY',
                customRangeLabel: customeRangeLabel,
            },
            autoUpdateInput: false,
            autoApply: true,
            singleDatePicker: false,
        };

        if ($input.data('ranges') == 'weeks') {
            options = {
                autoclose: true,
                locale: {
                    format: 'DD/MM/YYYY',
                    customRangeLabel: customeRangeLabel,
                },
                autoUpdateInput: false,
                autoApply: true,
                singleDatePicker: false,
                ranges: getWeekRange(),
            };
        }
        $input.daterangepicker(options)

    });
    // $('.input-daterange > input').daterangepicker({
    //     autoclose: true,
    //     locale: {
    //         format: 'DD/MM/YYYY',
    //     },
    //     autoUpdateInput: false,
    //     autoApply: true,
    //     singleDatePicker: false,
    //     ranges: getWeekRange(),
    // });

    $('.input-daterange > input').on('apply.daterangepicker', function (ev, picker) {
        $(this).val([picker.startDate.format('DD/MM/YYYY'), picker.endDate.format('DD/MM/YYYY')].join(' - '));
        $(this).trigger('change');
    })
}

function getWeekRange() {
    var res = {};

    function weeksInYear(year) {
        return Math.max(
            moment(new Date(year, 11, 31)).isoWeek()
            , moment(new Date(year, 11, 31-7)).isoWeek()
        );
    }
    var data = new Array(weeksInYear(new Date().getFullYear())).fill([]).map((v, i) => {
        var start = moment().day('Monday').week(i + 1).startOf('week');
        var end = moment().day('Monday').week(i + 1).endOf('week');
        return {key: [`Semaine ${i + 1}`], value: [start, end]};
    });
    data.forEach((item) => res[item.key] = item.value);

    return res;
}

function initClearButton(dt, options) {
    var btnDef = options.clearFilters.btn;
    var $btn = buildElem(btnDef);
    $(options.clearFilters.container).empty().append($btn);

    $btn.on('click', function (e) {
        clearAllFilters(dt)
    });

    var intervalId = setInterval(function () {
        if ($(CLEAR_BUTTON_CONTAINER).length > 0) {
            $(CLEAR_BUTTON_CONTAINER).empty().append($btn);
            clearInterval(intervalId);
        } else {}
    }, 1000);
}

function buildElem(param) {
    var $elem = $('<' + param.wrapper + '>');
    
    for (var attrName in param.attr) {
        $elem.attr(attrName, param.attr[attrName]);
    }
	$elem.html(param.content);
    
	return $elem;
}

function clearAllFilters(dt) {
    $APP_FILTER_CONTAINER.find('input').each(function () {
        var val = '' + $(this).val();
        if (val && (val.length > 0)) {
            $(this).val('');
        }
    });
    $APP_FILTER_CONTAINER.find('select').each(function () {
        var val = $(this).val();
        if ($(this).attr('multiple')) {
            if (val && (val.length > 0)) {
                $(this).val([]);
            }
        } else {
            if (val) {
                $(this).val('');
            }
        }
    });

    dt.search('').columns().search('').draw();
}

function initFilterDropdown ($container) {
    var $dropdownSelector = $container.find(RANGE_DROPDOWN_SELECTOR);
    
    $dropdownSelector.on('show.bs.dropdown', function() {
        var $currentObject = $(this);
        var $target = $($currentObject.data('trigger'));
        var left = $target.offset().left;
        if (($(window).width() - left) < RANGE_DROPDOWN_WIDTH) {
            left = $(window).width() - RANGE_DROPDOWN_WIDTH;
        }
        
        $('body').append($currentObject.css({
            position: 'absolute',
            left: left,
            top: $target.offset().top
        }).detach());
    });
}
