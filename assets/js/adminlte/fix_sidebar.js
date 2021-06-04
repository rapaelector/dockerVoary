$(function () {
    var sidebareOnMouseEnter = null;
    var $body = $('body.sidebar-expandable-on-hover');
    var bodyClass = 'sidebar-expanded-on-hover';
    var sidebarCollapseClass = 'sidebar-collapse';
    
    $('.main-sidebar').mouseenter(function (e) { 
        if ($body.hasClass('sidebar-expandable-on-hover')) {
            window.sidebareOnMouseEnter = setTimeout(function () {
                if ($body.hasClass(sidebarCollapseClass) && !$body.hasClass(bodyClass)) {
                    $body.removeClass(sidebarCollapseClass);
                    $body.addClass(bodyClass);
                }
            }, 250);
        }
    });
    $('.main-sidebar').mouseleave(function(e) {
        if ($body.hasClass('sidebar-expandable-on-hover')) {
            if (window.sidebareOnMouseEnter) {
                clearTimeout(window.sidebareOnMouseEnter);
            }
            if ($('body').hasClass(bodyClass) && !$('body').hasClass(sidebarCollapseClass)) {
                $('body').removeClass(bodyClass).addClass(sidebarCollapseClass);
            }
        }
    });
})