

# HOW TO USE SCHEDULER COMPONENT
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
## Scheduler dependecies import
The import of the following modules should not be in the scheduler modules but in the app who use the scheduler

```JavaScript
import 'angular-material';
import 'angular-messages';
import 'angular-sanitize';
import 'angular-moment/angular-moment';
import './../angular/shared/shared.module';
```

## Scheduler tags
    <app-scheduler></app-scheduler>

## Scheduler tags with bindings
    <app-scheduler
        resources="resources"
        columns="columns"
        event="events">
    </app-scheduler>

## Import scheduler module in any app

```JavaScript
angular.module('myModule', ['schedulerModule']);
```

## Bindings

| Bindings                  | Type                                                                                          | Description                                               | Default value
|--------------             |------                                                                                         | -----------------------------------------                 | -----------------------------------
| resources                 | Resource[]                                                                                    | Data to display in resource columns                       |
| columns                   | Column[]                                                                                      | Resources columns, List of the column to dispaly          |
| events                    | Event[]                                                                                       | Event to display in the date scheduler                    |
| start                     | Date \| string \| Moment                                                                      | Start of the scheduler date                               |
| end                       | Date \| string \| Moment                                                                      | End of the scheduler date                                 |
| options                   | Object                                                                                        | Option to change event, cell, positionsFix configuration  | 
| headerYearClassName       | string                                                                                        | Class to add to the header year                           | "scheduler-year"
| headerMonthClassName      | string                                                                                        | Class to add to the header month                          | "scheduler-month"
| headerWeekClassName       | string                                                                                        | Class to add to the header week                           | "scheduler-week"
| forceSticky               | boolean                                                                                       | Force the table and column to be sticky                   | false
| minRowCount               | number                                                                                        | Number for empty row to display when loading the scheduler| 14
| onRowClick                | (resource: Resource, column: Column, columnIndex: number) => void                             | function to call when the row is clicked                  |
| onColumnHeaderClick       | (column: Column, columnIndex: object) => void                                                 | function to call when the columnHeader is clicked         |
| onHeaderYearClick         | (yearObject: YearObject, yearIndex: number) => void                                           | function to call when the headerYear is clicked           |
| onHeaderMonthClick        | (monthObject: MonthObject, monthIndex: number) => void                                        | function to call when the headerMonth is clicked          |
| onHeaderWeekClick         | (weekObject: WeekObject, weekIndex: number) => void                                           | function to call when the headerWeek is clicked           |
| onCellClick               | (resource: Resource, week: WeekObject, weekIndex: number, resourceIndex: number) => void      | function to call when the cell is clicked                 |
| onEventClick              | (event: Event, eventIndex: number, jsEvent: object) => void                                   | function to call when the event is clicked                |

### Resource

Any JavaScript object, `{[key: string]: any}`

```JavaScript
{
    id: 10,
    ...
}
```

### @type Column

| Attributes                        | Type                                                        | Madatory             | Description
| ------------------------------    | ------------------------------------------------------------|----------------      | ------------------------------------------------------
| label                             | string                                                      | true                 | Column header title
| field                             | string                                                      | true                 | string could be resolve to get data (separate by dot  |simple string)                                   
| className                         | string                                                      | false                | Class add to the column cell body
| headerClassName                   | string                                                      | false                | Class add to the column header title
| formatter                         | (res: any, resource: Resource, index: number) => any        | false                | Function to format cell data
| sticky                            | boolean                                                     | false                | If the column should be sticky or not
| visible                           | boolean                                                     | false                | If the column should be visible or not
| classNameFormatter                | (res: any, resource: Resource, index: number) => any        | false                | Function to format the Class cell body
| headerColumnFormatter             | (res: any, resource: Resource, index: number) => any        | false                | Function to format header column title


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
### Options
| Attributes            | Type                      | Madatory  | 
| --------------------- | ------------------------  | --------  |
| defaultCellWidth      | number                    | false     |
| cell                  | object {width: number}    | false     |
| event                 | OptionEvent               | false     |

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
### @Type OptionEvent

| Type              | Type                                                      | Madatory  | Description
| ----------------- | -----------------------------------------------------     | --------  | -----------
| zIndex            | object {_default: number, [key: string]: number}          | false     | Zindex to apply to the event + event of the index
| bubbleHtml        | object {zIndex: number}                                   | false     | bubble zIndex
| titleFormatter    | (title: string, event: object) => any                     | false     | Function to format the event title 
| bubbleDelay       | number \| object {[key: string]: number, default: number} | false     | If (number) then its the duration between of show the bubble and hide it esle if (object) bubbleDelay have group 

### onRowClick

```JavaScript
function (resource, column, columnIndex) {
    alert('onRowClick clicked');
}
```
### onColumnHeaderClick

```JavaScript
function (column, columnIndex) {
    alert('onColumnHeaderClick clicked');
}
```
### onHeaderYearClick

```JavaScript
function (yearObject, yearIndex) {
    alert('onHeaderYearClick clicked');
}
```
### onHeaderMonthClick

```JavaScript
function (onthObject, monthIndex) {
    alert('onHeaderMonthClick clicked');
}
```
### onHeaderWeekClick

```JavaScript
function (weekObject, weekIndex) {
    alert('onHeaderWeekClick clicked');
}
```
### onCellClick

```JavaScript
function (resource, week, weekIndex, resourceIndex) {
    alert('onCellClick clicked');
}
```
### onEventClick

```JavaScript
function (event, eventIndex, jsEvent) {
    alert('onEventClick clicked');
}
```

