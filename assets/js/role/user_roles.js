/*
* @Author: Stephan<srandriamahenina@bocasay.com>
* @Date:   2017-07-28 14:28:35
* @Last Modified by:   Stephan
* @Last Modified time: 2017-12-18 17:21:54
*/

var jQuery = $;

$(function () {

    var UserRoles = function ($) {
        var _this = {};
        var activeClass = "active";
        var userRoles = {};
        var user = {};
    
        _this.defaults = {
            search: "#user_roles_search",
            users: "#user_roles_users",
            rolesContainer: "#user_roles_roles",
            roles: '[name="appbundle_user_roles[]"]',
            user: "#appbundle_user_id",
            activeUser: '.user-roles-active-user',
            userId: window.roleActiveUserId,
        };
    
        _this.initElems = function () {
            _this.$elems = {
                search: $(_this.options.search),
                usersContainer: $(_this.options.users),
                rolesContainer: $(_this.options.rolesContainer),
                roles: $(_this.options.roles),
                user: $(_this.options.user),
                activeUser: $(_this.options.activeUser),
            };
            _this.$elems.users = _this.$elems.usersContainer.find('li');
            extractRoles();
        };
    
        function extractRoles () {
            var id, roles;
            _this.$elems.users.each(function () {
                id = $(this).data('id');
                roles = $(this).data('roles').split('-');
                userRoles[id] = roles;
            });
        }
    
        function checkUserRoles () {
            var role, $elem, checked;
            _this.$elems.roles.each(function () {
                role = $(this).val();
                $elem = $(this);
                checked = hasRole(role);
                $elem.prop('checked', checked);
            });
            _this.$elems.roles.filter(function () {
                return $(this).attr('parent') == $(this).val();
            }).each(function () {
                var parentRole = $(this).attr('parent'), active = true, count = 0;
                _this.$elems.roles.filter(function () {
                    return $(this).attr('parent') == parentRole && $(this).val() != parentRole;
                }).each(function () {
                    count++;
                    if (!$(this).prop('checked')) {
                        active = false;
                    }
                });
                if (count > 0) {
                    $(this).prop('checked', active);
                }
            });
        }
    
        function hasRole (role) {
            return userHasRole(user.id, role);
        }
    
        function userHasRole (userId, role) {
            return userRoles[userId] && (userRoles[userId].indexOf(role) !== -1);
            // for (var i = 0; i < userRoles[userId]; i++) {
            // 	if (role == userRoles[userId][i]) return true;
            // }
    
            // return false;
        }
    
        _this.bindElems = function () {
            _this.$elems.search.keyup(searchHandler);
            _this.$elems.users.click(userSelectHandler);
            _this.$elems.roles.on('change', roleChangeHandler);
            $(_this.$elems.users[0]).click();
    
            if (_this.options.userId > 0) {
                $(buildAttrSelector('data-id', UserRoles.options.userId)).click();
            }
        }
        
        function buildAttrSelector (attr, value) {
            var res = '[' + attr;
            if (value) {
                res += '="' + value + '"'; 
            }
            res += ']';
        
            return res;
        }

        function roleChangeHandler (e) {
            if (!user) return true;
            var $check = $(this);
            var role = $check.val(), parentRole = $check.attr('parent');
            var all = true;
            var checked = $check.prop('checked');
            if (checked) {
                addRole(role);
            } else {
                removeRole(role);
            }
    
            var childSelector = buildAttrSelector('parent', parentRole);
            var $childRoles = $(childSelector).filter(function (i, elem) {
                return $(this).attr('value') != parentRole;
            });
            var $parentRole = $(childSelector).filter(function (i, elem) {
                return $(this).attr('value') == parentRole;
            });
    
            if (role == parentRole) {
                $childRoles.prop('checked', checked);
            } else {
                var count = 0;
                $childRoles.each(function() {
                    if (!$(this).prop('checked')) {
                        all = false;
                    } else if (!/_VIEW$/.test($(this).val())) {
                        count++;
                    }
                });
                if (!/_VIEW$/.test(role)) {
                    var $viewRole = $childRoles.filter(function () {
                        return /_VIEW$/.test($(this).val());
                    });
                    if (count > 0 && parentRole != undefined) {
                        $viewRole.prop('checked', true);
                    }
                }
    
                $parentRole.prop('checked', all);
            }
        }
    
        function addRole (role) {
            if (!hasRole(role)) {
                if (!userRoles[user.id]) {
                    userRoles[user.id] = [];
                }
                userRoles[user.id].push(role);
            }
        }
    
        function removeRole (role) {
            if (hasRole(role)) {
                userRoles[user.id].splice(userRoles[user.id].indexOf(role), 1);
            }
        }
    
        function userSelectHandler (e) {
            e.preventDefault();
            if (user.$elem) user.$elem.removeClass(activeClass);
            user.$elem = $(this);
            user.$elem.addClass(activeClass);
            user.id = user.$elem.data('id');
            _this.$elems.user.val(user.id);
            checkUserRoles();
            _this.$elems.activeUser.text($(this).data('name')).parent().show();
        }
    
        function searchHandler (e) {
            var query = _this.$elems.search.val();
            _this.filterUsers(query);
        }
    
        _this.filterUsers = function (query) {
            var regex = new RegExp(query, 'i');
            var $user;
            _this.$elems.users.each(function () {
                $user = $(this);
                var value = $user.data('query');
                if (regex.test(value)) {
                    $user.show();
                } else {
                    $user.hide();
                }
            });
        }
    
        _this.init = function (options) {
            _this = this;
            _this.options = $.extend(true, _this.defaults, options);
            _this.initElems();
            _this.bindElems();
        };
    
        return _this;
    } (jQuery);

    UserRoles.init();
});