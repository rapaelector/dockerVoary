import 'bootstrap/dist/js/bootstrap';
import 'overlayscrollbars/js/jquery.overlayScrollbars.js';
import 'admin-lte/dist/js/adminlte.js';
import '../scss/adminlte.scss';

$(function () {
    $('.sidebar').overlayScrollbars({
        overflowBehavior: {
            x: "hidden",
            y: "scroll",
        },
    });
});
