// import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap';
import 'overlayscrollbars/js/jquery.overlayScrollbars.js';
import 'admin-lte/dist/js/adminlte.js';
import '../scss/adminlte.scss';

// window.$ = $;
// window.jQuery = $;
// window.xyz = "abc";

$(function () {
    $('.sidebar').overlayScrollbars({
        overflowBehavior: {
            x: "hidden",
            y: "scroll",
        },
    });
});
