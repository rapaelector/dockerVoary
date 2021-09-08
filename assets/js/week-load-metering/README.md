# Week load metering (Comptage de charge pour la semaine)

## Week load metering configuration
import file in your angular app
```JavaScript
    import './path/to/the/week-load-metering/week-load-metering.js';
```

import week load metering module
```JavaScript
    angular.module('yourModule', ['weekLoadMeteringModule']);
```

component import
```Html
    <app-week-load-metering load-metering-date="data.loadMeteringDate"></app-week-load-metering>
```

## Bindigs

| Binding             | Type                                | Default value                  | Description
|-------------------- | ----------------------------------- |------------------------------- | ----------------------------------
| loadMeteringDate    | date                                | null                           | Date send to server to fetch study time per week by economist
| 