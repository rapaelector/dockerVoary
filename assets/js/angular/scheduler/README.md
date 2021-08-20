

# HOW TO USE SCHEDULER MODULE
## Scheduler Configuration
To use the angular `schduler module` import the following files depend on your app location

```JavaScript
import './../angular/scheduler/scheduler';
```
## Scheduler dependecies

```JavaScript
[
    'ngMaterial',
    'sharedModule',
    'ngSanitize',
    'ngMessages',
    'angularMoment',
]
```

## Import scheduler module in your app

```JavaScript
angular.module('myModule', ['schedulerModule']);
```
## Scheduler dependecies import
The import of the following modules should not be in the scheduler modules but in the app who use the scheduler

```JavaScript
import 'angular-material';
import 'angular-messages';
import 'angular-sanitize';
import 'angular-moment/angular-moment';
import './../angular/shared/shared.module';
```
## Use with controller

```JavaScript
// Use mock data in some file to the app-scheduler 
import {resource, event, column, options, buildColumn} from './mock-data';

angular.module('myApp').controller(['$scope', function ($scope) {
    $scope.data = {
        resources: [],
        events: [],
        columns: [],
    };
    $scope.options = {};

    this.$onInit = function () {
        $scope.data.resources = resources;
        $scope.data.events = events;
        /**
         * Here we use the simple column
         * To use the build column use the following and adapte the following code
         * Can pass any number of argument how can be use in the buildColumn
         *  $scope.data.columns = buildColumn(arg1, arg2, ....)
         */
        $scope.data.columns = columns;
        $scope.options = options;
    };
}])
```
## Scheduler tags and bindings
Pass data through bings to the `app-scheduler`
```Html
<app-scheduler
    resources="data.resources"
    columns="data.columns"
    event="data.events"
    options="options"
></app-scheduler>
```

## Mock data

```JavaScript
const resource = [
    {
        id: 1,
        siteCode: null,
        marketType: "Appel d’offre",
        globalAmount: 76000,
        amountSubcontractedWork: 329000,
        amountBBISpecificWork: "32000",
        caseType: [
            "Terrassement",
            "Gros œuvre",
            "CVC / plomberie",
            "Dossier administratif",
            "Serrurerie"
        ],
        prospect: {
            id: 1,
            name: "Microsoft",
            shortName: "Microsoft",
            clientNumber: "PR0001",
            projectDescription: {
                area: "Lorem ipsum"
            }
        }
    },
];

/**
 * If the columns need other service like numberFormat or something else use function and pass dependecies through arguments and
 * Then the function return data structure (array of object) to use as columns
 * 
 * USE WITH OUTSIDE SERVICE
 * Use numberFormat as outside service
 */
const buildColumn = function (numberFormat) {
    return [
        {
            label: 'Chantier',
            field: 'siteCode',
            className: 'chantier-class',
            headerClassName: 'text-uppercase text-center',
            formatter: function(res, resource, index) {
                return res ? '<div class="test" title="'+ res +'">' + res + '</div>' : '';
            },
            headerColumnFormatter: function (column, index) {
                return column ? `
                    <div 
                        class="dynamic-nowrap" 
                        title="` + column.label + `" 
                        data-toggle="tooltip" 
                        data-container="body" 
                        data-placement="top"
                    >` + column.label + `</div>` : '';
            },
            classNameFormatter: function(res, resource, index) {
                return 'dynamic-nowrap';
            },
            width: 150,
            visible: false,
            formatter: function(res, resource, index) {
                return res ? (numberFormat(res, 2, ',', ' ') + ' €') : '';
            },
        },
    ]
};

/**
 * USE WITHOUT OUTSIDE SERVICE
 * Can be just array of object 
 */
const columns = [
    {
        label: 'Chantier',
        field: 'siteCode',
        className: 'chantier-class',
        headerClassName: 'text-uppercase text-center',
        formatter: function(res, resource, index) {
            return res ? '<div class="test" title="'+ res +'">' + res + '</div>' : '';
        },
        headerColumnFormatter: function (column, index) {
            return column ? `
                <div 
                    class="dynamic-nowrap" 
                    title="` + column.label + `" 
                    data-toggle="tooltip" 
                    data-container="body" 
                    data-placement="top"
                >` + column.label + `</div>` : '';
        },
        classNameFormatter: function(res, resource, index) {
            return 'dynamic-nowrap';
        },
        width: 150,
        visible: false,
    },
];

/**
 * Event
 */ 
const event = [
    {
        id: 14,
        start: moment('2020-12-30', 'YYYY-MM-DD'),
        end: moment('2021-01-27', 'YYYY-MM-DD'),
        type: "shade_house",
        project: 3,
        resource: 1,
        backgroundColor: "#1f497d",
        bubbleHtml: "\r\n            <div class=\"text-center\"> 2020/12/30 — 2021/01/27 </div>\r\n            "
    },
];

/**
 * Options
 */ 
const options = {
    defaultCellWidth: 24,
    cell: {
        width: 24,
    },
    event: {
        zIndex: {
            'payment': 9999,
            _default: 100
        },
        bubbleHtml: {
            zIndex: 100,
        },
        titleFormatter: function (title, event) {
            return '';
        },
        bubbleDelay: 2,
        boxShadow: true,
        boxShadowSticky: true,
    },
};

export {resource, column, event, options, buildColumn};
```
## Bindings

| Bindings                  | Type                                                                                          | Description                                               | Default value
|--------------             |------                                                                                         | -----------------------------------------                 | ------
| resources                 | [Resource](#resource_def)[]                                                                   | Data to display in resource columns                       |
| columns                   | [Column](#column_def)[]                                                                       | Resources columns, List of the column to dispaly          |
| events                    | [Event](#event_def)[]                                                                         | Event to display in the date scheduler                    |
| start                     | Date \| string \| Moment                                                                      | Start of the scheduler date                               |
| end                       | Date \| string \| Moment                                                                      | End of the scheduler date                                 |
| options                   | [OptionObject](#options_def)                                                                  | Option to change event, cell, positionsFix configuration  | 
| headerYearClassName       | string                                                                                        | Class to add to the header year "scheduler-year"          |
| headerMonthClassName      | string                                                                                        | Class to add to the header month "scheduler-month"        |
| headerWeekClassName       | string                                                                                        | Class to add to the header week "scheduler-week"          |
| forceSticky               | boolean                                                                                       | Force the table and column to be sticky                   | false
| minRowCount               | number                                                                                        | Number for empty row to display when loading the scheduler| 14
| totals                    | [array of totals](#totals_def)                                       | Total to dispaly in the table footer                      | []
| [onRowClick](#on_row_click_def)                       | (resource: [Resource](#resource_def), column: [Column](#column_def), columnIndex: number) => void | function to call when the row is clicked              |
| [onColumnHeaderClick](#on_column_header_click_def)    | (column: [Column](#column_def), columnIndex: object) => void                                  | function to call when the columnHeader is clicked           |
| [onHeaderYearClick](#on_header_year_click_def)        | (yearObject: [YearObject](#year_object_def), yearIndex: number) => void                       | function to call when the headerYear is clicked              |
| [onHeaderMonthClick](#on_header_month_click_def)      | (monthObject: [MonthObject](#month_object_def), monthIndex: number) => void                   | function to call when the headerMonth is clicked              |
| [onHeaderWeekClick](#on_header_week_click_def)        | (weekObject: [WeekObject](#week_object_def), weekIndex: number) => void                       | function to call when the headerWeek is clicked              |
| [onCellClick](#on_cell_click_def)                     | (resource: [Resource](#resource_def), week: [WeekObject](#week_object_def), weekIndex: number, resourceIndex: number) => void | function to call when the cell is clicked     |
| [onEventClick](#on_event_click_def)                   | (event: [Event](#event_def), eventIndex: number, jsEvent: jsEvent) => void                     | function to call when the event is clicked              |

<a name="resource_def">

### Resource

Any JavaScript object, `{[key: string]: any}`

```JavaScript
{
    id: 10,
    ...
}
```

<a name="column_def"></a>

### @type Column

| Attributes                        | Type                                                        | Madatory             | Description
| ------------------------------    | ------------------------------------------------------------|----------------      | ------------------------------------------------------
| label                             | string                                                      | true                 | Column header title
| field                             | string                                                      | true                 | string could be resolve to get data (separate by dot  \|simple string)                 
| className                         | string                                                      | false                | Class add to the column cell body
| headerClassName                   | string                                                      | false                | Class add to the column header title
| formatter                         | (res: any, resource: [Resource](#resource_def), index: number) => any | false      | Function to format cell data
| sticky                            | boolean                                                     | false                | If the column should be sticky or not
| visible                           | boolean                                                     | false                | If the column should be visible or not
| classNameFormatter                | (res: any, resource: [Resource](#resource_def), index: number) => any | false      | Function to format the Class cell body
| headerColumnFormatter             | (res: any, resource: [Resource](#resource_def), index: number) => any | false      | Function to format header column title


```JavaScript
{
    label: 'Chantier',
    field: 'constructionSite',
    className: 'chantier-class',
    headerClassName: 'text-uppercase text-center',
    formatter: function(res, resource, index) {
        return res ? '<b>' + res + '</b>' : '';
    },
    sticky: true,
    visible: boolean {true},
    classNameFormatter: function (res, resource, index) {
        return {any};
    },
    headerColumnFormatter: function (res, resource, index) {
        return {any};
    },
}
```

<a name="event_def"></a>

### @type Event

| Attributes            | Type                      | Madatory  | Description
| ----------------------| ------------------------- | --------- | -----------
| id                    | number                    | true      | Id of the event
| resource              | number                    | true      | Id of the resource
| title                 | string                    | false     | String to display in the event
| start                 | Date \| string | Moment   | true      | Start of the event
| end                   | Date \| string \| Moment  | true      | End of the event
| backgroundColor       | string                    | true      | Bacground of the event
| bubbleHtml            | any                       | false     | Tooltip to dispaly when hover the event

```JavaScript
 {
    id: 1,
    resource: 1,
    title: 'Madagascar',
    start: moment('2021-01-01', 'YYYY-MM-DD').startOf('week'),
    end: moment('2021-03', 'YYYY-MM').endOf('month'),
    backgroundColor: '#ff0000',
    bubbleHtml: `<div> lorem </div>`,
 },
```

<a name="totals_def"></a>

### @Type Totals

| Attributes    | Type              | Description
| --------------| ----------------- | ----------------
| amout         | number            |
| month         | string \| number  |
| year          | string \| number  |


<a name="options_def"></a>

### @Type OptionsObject
| Attributes            | Type                                  | Madatory  | 
| --------------------- | ------------------------              | --------  |
| defaultCellWidth      | number                                | false     |
| cell                  | object {width: number}                | false     |
| event                 | [OptionEvent](#option_event_def)      | false     |

```JavaScript
{
    defaultCellWidth: 24,
    cell: {
        width: 24,
    },
    event: {
        zIndex: {
            'payment': 9999, // group z-index
            _default: 100 // default z-index if zIndex group have no z-index
        },
        bubbleHtml: {
            zIndex: 100, // default bubble zIndex
        },
        // Format event title
        /**
         * @param {}
         */
        titleFormatter: function (title, event) {
            return '';
        },
        /**
         * @type {number | object } 
         */
        bubbleDelay: 2
    },
}
```
<a name="option_event_def"></a>

### @Type OptionEvent

| Attributes        | Type                                                      | Madatory  | Description
| ----------------- | -----------------------------------------------------     | --------  | -----------
| zIndex            | object {_default: number, [key: string]: number}          | false     | Zindex to apply to the event + event of the index
| bubbleHtml        | object {zIndex: number}                                   | false     | bubble zIndex
| titleFormatter    | (title: string, event: jsEvent) => any                    | false     | Function to format the event title 
| bubbleDelay       | number \| object {[key: string]: number, default: number} | false     | If (number) then its the duration between of show the bubble and hide it esle if (object) bubbleDelay have group 
| boxShadow         | boolean                                                   | false     | If should add box shadow to the event if not stickyColumns
| boxShadowSticky   | boolean                                                   | false     | If should add box shadow to the event if the table is stickyColumns

```JavaScript
{
    zIndex: {
        'payment': 9999, // group z-index
        _default: 100 // default z-index if zIndex group have no z-index
    },
    bubbleHtml: {
        zIndex: 100, // default bubble zIndex
    },
    titleFormatter: function (title, event) {
        return '';
    },
    bubbleDelay: 2
}
```

<a name="year_object_def"></a>

### @Type YearObject

| Attributes        | Type              | Description
|------------------ | ----------------- | --------------
| monthsCount       | number            | Number of the month in the year depend on the start and end of the scheduler date
| name              | string | number   | The year (eg: 2020, 2019)
| weeksCount        | number            | Weeks count in the year (52) depend on the start and end of the scheduler date

```JavaScript
{
    monthsCount: 12,
    name: "2021",
    weeksCount: 52,
}
```

<a name="month_object_def"></a>

### @Type MonthObject

| Attributes        | Type              | Description
|------------------ | ----------------- | --------------
| monthNumber       | number            | Number of the month
| name              | string            | Name of the month (eg: mars, juin...)
| weeksCount        | number            | Weeks count in the month 
| year              | number | string   | Year where the month is

```JavaScript
{
    monthNumber: "3",
    name: "mars",
    weeksCount: 5,
    year: "2021",
}
```

<a name="week_object_def"></a>

### @Type WeekObject

| Attributes        | Type              | Description
|------------------ | ----------------- | -----------------------
| endDay            | Moment            | 
| firstWeek         | boolean           | Is the week object is first of week in the month
| lastWeek          | boolean           | Is the week object is the last week in the month
| monthNumber       | number            | Month number of the week
| startDay          | Moment            | 
| weekNumber        | number            | Number of the week in the entiry year 
| year              | string            | Year where the week is

```JavaScript
{
    endDay: Moment,
    firstWeek: false,
    lastWeek: false,
    monthNumber: "1",
    startDay: Moment,
    weekNumber: 3,
    year: "2021",
}
```

<a name="on_row_click_def"></a>

### onRowClick

```JavaScript
function (resource, column, columnIndex) {
    alert('onRowClick clicked');
}
```

<a name="on_column_header_click_def"></a>

### onColumnHeaderClick

```JavaScript
function (column, columnIndex) {
    alert('onColumnHeaderClick clicked');
}
```

<a name="on_header_year_click_def"></a>

### onHeaderYearClick

```JavaScript
function (yearObject, yearIndex) {
    alert('onHeaderYearClick clicked');
}
```

<a name="on_header_month_click_def"></a>

### onHeaderMonthClick

```JavaScript
function (onthObject, monthIndex) {
    alert('onHeaderMonthClick clicked');
}
```

<a name="on_header_week_click_def"></a>

### onHeaderWeekClick

```JavaScript
function (weekObject, weekIndex) {
    alert('onHeaderWeekClick clicked');
}
```

<a name="on_cell_click_def"></a>

### onCellClick

```JavaScript
function (resource, week, weekIndex, resourceIndex) {
    alert('onCellClick clicked');
}
```

<a name="on_event_click_def"></a>

### onEventClick

```JavaScript
function (event, eventIndex, jsEvent) {
    alert('onEventClick clicked');
}
```