import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap';
import 'overlayscrollbars/js/jquery.overlayScrollbars.js';
import 'admin-lte/dist/js/adminlte.js';
import 'daterangepicker';
import 'daterangepicker/daterangepicker.css';
import 'bootstrap-daterangepicker/daterangepicker.js';
import 'bootstrap-daterangepicker/daterangepicker.css';
import 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js';
import 'select2/dist/js/select2';
import 'bootstrap-select';
import 'bootstrap-select/dist/js/bootstrap-select';
import 'bootstrap-select/dist/js/i18n/defaults-fr_FR.min';

window.$ = $;
window.jQuery = $;

require('datatables.net-bs4');
require('datatables.net-buttons/js/buttons.colVis.js')(); // Column visibility
require('datatables.net-buttons/js/buttons.html5.js')();  // HTML 5 file export
require('datatables.net-buttons/js/buttons.flash.js')();  // Flash file export
require('datatables.net-buttons/js/buttons.print.js')();  // Print view button
require('datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js')();
require('datatables.net-fixedcolumns-bs4');

import './datatable/omines-datatables';
import { selectBuilder } from './common/form/select_form';

$(function () {
    /**
     * Init bootstrap tooltip
     */
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});

    $('.sidebar').overlayScrollbars({
        overflowBehavior: {
            x: "hidden",
            y: "scroll",
        },
    });

    /**
     * Fix dropdown position to avoid overflow/display issue
     */
    $('body').on('shown.bs.dropdown', '.dataTable .dropdown', function () {
        var $menu = $(this).find('.dropdown-menu')
        setTimeout(() => {
            const {top, left} = $menu.get(0).getBoundingClientRect();
            $menu.css({position: 'fixed', ...$menu.offset(), top, transform: 'none'});
        }, 10);
    });

    /**
     * Add/remove "dropup" class to dropdown depending on position
     */
    $('body').on('click', '.dataTable [data-toggle="dropdown"]', function (e) {
        const $dropdown = $(this).parent('.dropdown');
        if ($dropdown) {
            if (($(window).height() / 2) < e.clientY) {
                $dropdown.addClass('dropup');
            } else {
                $dropdown.removeClass('dropup');
            }
        }
    });

    /**
     * Make input with data-type="select" a select input
     */
    $('[data-type="select"]').each(selectBuilder);

    /**
     * Initialization for select2
     * Every select input who have data-type="select2" should become select2 field
     */
    $('select[data-type="select2"]').select2({
        width: '100%',
    });

    /**
     * Initialization for bootstrap-select2
     */
    $('.bootstrap-select').selectpicker('refresh');
});
