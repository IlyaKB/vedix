define(["require", "exports", 'knockout', './utils'], function (require, exports, ko, Utils) {
    var User = (function () {
        function User(config) {
            var _this = this;
            this.config = config;
            User.id = ko.observable(config.user.id || 0);
            User.login = ko.observable(config.user.login || '');
            User.fullname = ko.observable(config.user.fullname || '');
            User.email = ko.observable(config.user.email || '');
            User.phone = ko.observable(config.user.phone || '');
            this.id = User.id;
            this.login = User.login;
            this.fullname = User.fullname;
            this.email = User.email;
            this.phone = User.phone;
            this.psw = ko.observable('');
            this.isValidLoginPsw = ko.computed(function () {
                return (_this.login().length >= 3) && (_this.psw().length >= 1);
            }, this);
        }
        User.prototype.auth = function () {
            if ((!this.login()) || (!this.psw())) {
                return false;
            }
            var parameters = {
                jx: 'authenticate',
                user_login: this.login(),
                user_password: this.psw()
            };
            $.post('/', parameters).then(function (response, status) {
                var data = Utils.getJSONbyText(response);
                if (data.success) {
                    location.href = location.href.replace(/#/, '');
                }
                else {
                }
            }, function (response) {
                //
            });
        };
        return User;
    })();
    return User;
});
