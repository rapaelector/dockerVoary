import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap';
import 'overlayscrollbars/js/jquery.overlayScrollbars.js';
import 'admin-lte/dist/js/adminlte.js';
import 'daterangepicker';

window.$ = $;
window.jQuery = $;
// window.xyz = "abc";

import dt from 'datatables.net';
import 'datatables.net-bs4';
// var buttons = require( 'datatables.net-buttons')();
require('datatables.net-buttons/js/buttons.colVis.js')(); // Column visibility
require('datatables.net-buttons/js/buttons.html5.js')();  // HTML 5 file export
require('datatables.net-buttons/js/buttons.flash.js')();  // Flash file export
require('datatables.net-buttons/js/buttons.print.js')();  // Print view button
require('datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')();

import './datatable/omines-datatables';

$(function () {
    $('.sidebar').overlayScrollbars({
        overflowBehavior: {
            x: "hidden",
            y: "scroll",
        },
    });
});
