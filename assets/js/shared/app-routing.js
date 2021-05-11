export default {
	generate: function (routeName, routeParams) {
		try {
			return Routing.generate(routeName + '.' + appCurrentLocale, routeParams);
		} catch {}
		routeParams = $.extend({_locale: window.appCurrentLocale}, routeParams);
		
		return Routing.generate(routeName, routeParams);
	}
};