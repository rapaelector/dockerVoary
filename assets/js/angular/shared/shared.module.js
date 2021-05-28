angular.module('sharedModule', []);
import jQuery from 'jquery';

angular.module('sharedModule').filter('number_format', ['$filter', function($filter) {
    return function(numberExpression, fractionSize, decimalSign, thousandSign) {
        if (numberExpression == undefined) {
            return '';
        }

        decimalSign = decimalSign === null || decimalSign === undefined ? ',' : decimalSign;
        thousandSign = thousandSign === null || thousandSign === undefined ? '.' : thousandSign;
        fractionSize = fractionSize === null || fractionSize === undefined ? 3 : fractionSize;

        var formatted = $filter('number')(numberExpression, fractionSize);
        var tmp = 'xxx';

        var res;
        if (true) { // TODO : check locale \s is the metacharacter of space ( )
            res = formatted.replace(/\s/g, tmp).replace(/,/g, decimalSign).replace(new RegExp(tmp, 'g'), thousandSign);
        } else {
            res = formatted.replace(/,/g, tmp).replace(/\./g, decimalSign).replace(new RegExp(tmp, 'g'), thousandSign);
        }

        return res;
    };
}]);

angular.module('sharedModule').filter('html_text', ['$filter', function($filter) {
    return function(htmlContent) {
        try {
            return htmlContent ? jQuery(htmlContent).text() : '';
        } catch (e) { console.dir(e)}
        return htmlContent;
    };
}]);

angular.module('sharedModule').filter('hours_to_days', ['$filter', function($filter) {
    return function (input) {
        return parseFloat(input) / 24;
    };
}]);

angular.module('sharedModule').factory('fosJsRouting', [function () {
    return {
        generate: function (routeName, routeParams) {
            try {
                routeParams = $.extend({_locale: window._locale}, routeParams);
            } catch (e) {}
            
            return Routing.generate(routeName, routeParams);
        }
    };
}]);

angular.module('sharedModule').constant('sharedMinimalEditorConfig', {
    // setup: function (editor) {
    //     editor.on('change', function () {
    //         editor.save();
    //     });
    // },
    relative_urls: false,
    remove_script_host: false,
    forced_root_block: 'div',
    convert_urls: true,
    menubar: false,
    statusbar: false,
    plugins: "link lists paste table",
    toolbar: 'bold italic | bullist | alignleft aligncenter alignright alignjustify',
    paste_as_text: true,
    branding: false,
    // formats: {
    //     underline: { inline: 'u', exact : true },
    //     bold: { inline: 'b' },
    //     italic: { inline: 'i' },
    // },
    valid_elements: "b,i,b/strong,i/em",
});

angular.module('sharedModule').directive('appTimeOnly', ['$filter', '$interpolate', function ($filter, $interpolate) {
    return {
        // require: 'ngModel', 
        scope: false, 
        restrict: 'A', 
        link: function (scope, elem, attrs, ctrl) {
            elem.val(elem.val().replace(',', '.').replace(' ', ''));
            elem.bind('keypress', function (e) {
                var c = String.fromCharCode(e.which);
                var val = $(this).val() + '';

                if (e.which == 8) { // delete key
                    return true;
                } else if (c == '-' && val == '') {
                    return true;
                } else if ([',', '.'].indexOf(c) != -1) {
                    c = '.';
                    if (val.indexOf(c) == -1 && val != "") {
                        elem.val(val + c);
                    }
                    e.preventDefault();
                } else if (/\d/.test(c)) {
                    if (/^\d+\.\d$/.test(val)) {
                        e.preventDefault();
                    } else if (/^\d+\.$/.test(val)) {                        
                        if (c != '5') {
                            e.preventDefault();
                        }
                    }
                } else if (!/\d/.test(c)) {
                    e.preventDefault();
                }
            });
        }
    };
}]);

angular.module('sharedModule').directive('appNumberOnly', ['$filter', '$interpolate', function ($filter, $interpolate) {
    return {
        // require: 'ngModel', 
        scope: false, 
        restrict: 'A', 
        link: function (scope, elem, attrs, ctrl) {
            elem.val(elem.val().replace(',', '.').replace(' ', ''));
            elem.bind('keypress', function (e) {
                var c = String.fromCharCode(e.which);
                var val = $(this).val() + '';

                if (e.which == 8) { // delete key
                    return true;
                } else if (c == '-' && val == '') {
                    return true;
                } else if ([',', '.'].indexOf(c) != -1) {
                    c = '.';
                    if (val.indexOf(c) == -1 && val != "")
                        elem.val(val + c);
                    e.preventDefault();
                } else if (!/\d/.test(c)) {
                    e.preventDefault();
                }
            });
        }
    };
}]);

angular.module('sharedModule').factory('resolverService', function () {
    var _this = {};

    _this.resolve = function resolve(path, defaultVal) {
        defaultVal = defaultVal != undefined ? defaultVal : null;

        if (!angular.isArray(path) || path.length == 0) {
            return defaultVal;
        }

        var crnt = path[0];
        for (var i = 1; i < path.length; i++) {
            try {
                if (crnt[path[i]] != undefined) {
                    crnt = crnt[path[i]];
                } else {
                    crnt = defaultVal;
                    break;
                }
            } catch (e) {
                crnt = defaultVal;
                break;
            }
        }

        return crnt;
    };

    return _this;
});
