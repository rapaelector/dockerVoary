export function SymfonyCollections(options) {
    var collection = this || {};
	var protoClass = randomString();

	collection.defaults = {
		label: 'Entry #',
		add: {
			'wrapper': 'button',
			'html': true,
			'content': 'Add',
			'attr': {
				'type': 'button',
				'class': 'btn btn-primary',
			},
		},
		up: {
			'wrapper': 'a',
			'html': true,
			'content': '<i class="fa fa-chevron-up fa-lg"></i>',
			'attr': {
				'href': '#',
				'type': 'button',
				'class': 'up',
				'data-toggle': 'tooltip',
				'title': 'up',
			},
		},
		down: {
			'wrapper': 'a',
			'html': true,
			'content': '<i class="fa fa-chevron-down fa-lg"></i>',
			'attr': {
				'href': '#',
				'type': 'button',
				'class': 'down',
				'data-toggle': 'tooltip',
				'title': 'down',
			},
		},
		remove: {
			'wrapper': 'button',
			'html': true,
			'content': '<span aria-hidden="true">&times;</span>',
			'attr': {
				'class': 'app-close',
				'aria-label': 'close',
			}
		},
		prototypeAttr: {},
		protoWrapper: 'div',
		addNav: false,
		allow_delete: true,
	};
	
    collection.initElements = function () {
        collection.$elements = {
        	container: $(collection.param.selector),
        	addLink: collection.buildElement(collection.param.add),
        };
    };

    collection.initCollection = function () {
        collection.index = collection.$elements.container.children(collection.param.protoWrapper).length;
		if (collection.param.add.container) {
			collection.$elements.addLink.appendTo(collection.param.add.container);
		} else {
			collection.$elements.container.append(collection.$elements.addLink);
		}
		if (collection.index == 0) {
			collection.$elements.addLink.click();
		} else {
			collection.$elements.container.children(collection.param.protoWrapper).each(function() {
				if (collection.param.allow_delete) {
					collection.addDeleteLink($(this));
				}
				if (collection.param.add.callback) {
					collection.param.add.callback($(this), true);
				}
			});
		}

		collection.$elements.container.trigger('collection.init', []);
    }

    collection.bindElements = function () {
    	collection.$elements.addLink.on('click', function (e) {
    		e.preventDefault();
    		collection.addEntry();
			if (collection.param.add.container) {
				$(this).appendTo(collection.param.add.container);
			} else {
				collection.$elements.container.append($(this));
			}

			return false;
    	});
    }

    collection.addEntry = function (addOptions) {
    	collection.$elements.container.trigger('collection.adding');
    	var defaultPrototype = collection.param.prototype || collection.$elements.container.attr('data-prototype');
    	addOptions = $.extend(true, {
    		entryPrototype: defaultPrototype,
    		method: 'append',
    		container: collection.$elements.container,
    		inversed: false,
    		addNav: true,
    	}, addOptions);
    	var prototype = addOptions.entryPrototype;
		var $prototype = $(prototype.replace(/__name__label__/g, collection.param.label + (collection.index+1)).replace(/__name__/g, collection.index));
		collection.addDeleteLink($prototype, addOptions);
		if (addOptions.inversed)
			$prototype[addOptions.method].call($prototype, addOptions.container);
		else
			addOptions.container[addOptions.method].call(addOptions.container, $prototype);
		collection.index++;
		collection.$elements.container.trigger('collection.add', [$prototype]);
		$prototype.attr(collection.param.prototypeAttr);
		if (collection.param.add.callback)
			collection.param.add.callback($prototype, false);

		return $prototype;
    }

    collection.addDeleteLink = function ($prototype, options) {
    	if (!options) options = {addNav: true};
		$prototype.addClass(protoClass);
		var $deleteLink = collection.buildElement(collection.param.remove);

		if (collection.param.addNav && options.addNav)
			collection.addUpLink($prototype);
		if (collection.param.remove.container) {
			$prototype.find(collection.param.remove.container).append($deleteLink);
		} else {
			$prototype.append($deleteLink);
		}
		if (collection.param.addNav && options.addNav)
			collection.addDownLink($prototype);
		
		$deleteLink.click(function (e) {
			e.preventDefault();
			var remove = true;
			if (collection.param.remove.onRemove) {
				confirm = collection.param.remove.onRemove($prototype);
				if (typeof confirm == "boolean") {
					if (confirm === true) {
						executeRemove();
					}
				} else {				
					$.when(confirm).then(function(OK, $cmodal) {
						executeRemove();
						if ($cmodal) {
							$cmodal.modal('hide');
						}
					}, function(cancel) {
					});
				}
			} else {
				executeRemove();
			}
			function executeRemove() {
				$prototype.trigger('collection.remove', [$prototype]);
				$prototype.remove();
				collection.$elements.container.trigger('collection.remove', [collection.index]);
				if (collection.param.remove.onRemoved) {
					collection.param.remove.onRemoved();
				}
			}

			return false;
		});

    }

    collection.addNav = function ($prototype) {
    	var handler = function() {
	        var row = $(this).parents('.'+protoClass);
	        $prototype.trigger('collection.move', [$prototype]);
	        if ($(this).is(".up")) {
	            row.insertBefore(row.prev());
	        } else if ($(this).is(".down")) {
	            row.insertAfter(row.next());
	        } else if ($(this).is(".top")) {
	            row.insertBefore($("table tr:first"));
	        } else {
	            row.insertAfter($("table tr:last"));
	        }
	        $prototype.trigger('collection.moved', [$prototype]);
	        collection.$elem.container.trigger('collection.moved', [$prototype]);

	        return false;
  		};
    	var $up = collection.buildElement(collection.param.up);
    	var $down = collection.buildElement(collection.param.down);
    	$up.click(handler);
    	$down.click(handler);
    	collection.param.up.container ? $prototype.find(collection.param.up.container).append($up) : $prototype.append($up);
    	collection.param.down.container ? $prototype.find(collection.param.down.container).append($down) : $prototype.append($down);
    }

    collection.addUpLink = function ($prototype) {
    	var $up = collection.buildElement(collection.param.up);
    	$up.click(function(e) {
			e.preventDefault();
			var move = true;
	        var row = $(this).parents('.'+protoClass);
	        if (collection.param.up.onMove) {
	        	move = collection.param.up.onMove($prototype);
	        }
	        if (move) {
		        $prototype.trigger('collection.move', [$prototype]);
		        if (typeof move != "boolean") {
		        	move.before(row);
		        } else {
		        	row.insertBefore(row.prev());
		        }
		        if (collection.param.up.onMoved) {
		        	collection.param.up.onMoved($prototype);
		        }
		        $prototype.trigger('collection.moved', [$prototype]);
	        }

	        return false;
  		});
    	collection.param.up.container ? $prototype.find(collection.param.up.container).append($up) : $prototype.append($up);
    }

    collection.addDownLink = function ($prototype) {
    	var $down = collection.buildElement(collection.param.down);
    	$down.click(function(e) {
			e.preventDefault();
			var move = true;
	        var row = $(this).parents('.'+protoClass);
	        if (collection.param.down.onMove) {
	        	move = collection.param.down.onMove($prototype);
	        }
	        if (move) {        	
		        $prototype.trigger('collection.move', [$prototype]);
		        if (typeof move != "boolean") {
		        	move.after(row);
		        } else {
		            row.insertAfter(row.next());
		        }
		        if (collection.param.down.onMoved) {
		        	collection.param.down.onMoved($prototype);
		        }
		        $prototype.trigger('collection.moved', [$prototype]);
	        }

	        return false;
  		});
    	collection.param.down.container ? $prototype.find(collection.param.down.container).append($down) : $prototype.append($down);
    }

	function moveHandler () {
        var row = $(this).parents('.'+protoClass);
        if ($(this).is(".up")) {
            row.insertBefore(row.prev());
        } else if ($(this).is(".down")) {
            row.insertAfter(row.next());
        } else if ($(this).is(".top")) {
            row.insertBefore($("table tr:first"));
        }else {
            row.insertAfter($("table tr:last"));
        }

        return false;
	}

    function fixIndex () {
		var formName = collection.$elements.container.parents('form').attr('name');
		var pattern = new RegExp('^(' + formName + "\\[\\w*\\]\\[)(\\d+)(\\].*)");
    	collection.$elements.container.find('.' + protoClass).each(function (index) {
    		$(this).find('input, select').each(function () {
    			var name = $(this).attr('name');
    			name = name.replace(pattern, function (a, b, c, d) {
    				return b + index + d;
    			});
    			$(this).attr('name', name);
    		});
    	});
    } 

	collection.buildAttr = function (attrs) {
		var attributes = [];
		for (var attr in attrs) {
			attributes.push(attr + '="' + attrs[attr] + '"');
		}

		return attributes.join(' ');
	}

	collection.buildElement = function (param) {
		return $('<' + param.wrapper + ' ' + collection.buildAttr(param.attr) + '>' + param.content + '</' + param.wrapper + '>');
	}

	collection.getProtoClass = function () {
		return protoClass;
	}

    collection.init = function (param) {
        collection = this;
        collection.param = jQuery.extend(true, collection.defaults, param);
        collection.initElements();
        collection.bindElements();
        collection.initCollection();

        return collection;
    }

    if (options) {
		collection.init(options);
    }
    // return collection;
}

function randomString (length) {
	length = length||5;
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var possibleStart = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    for(var i = 0; i < length; i++) {
    	if (i == 0)
	        text += possibleStart.charAt(Math.floor(Math.random() * possibleStart.length));
	    else 
	        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
}
