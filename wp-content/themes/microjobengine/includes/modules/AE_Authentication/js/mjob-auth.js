(function($, Views, Models, Collections){
    $(document).ready(function() {
        Models.mJobUser = Backbone.Model.extend({
            action: 'mjob_sync_user'
        });

        // MODAL SIGN UP STEP 1: Input email
        Views.SignUpEmailModal = Views.Modal_Box.extend({
            el: '#signUpStep1',
            events: {
                'submit form' : 'nextStep'
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                AE.pubsub.trigger('ae:init:modal:signup', this);
                if(typeof this.signUpModal === "undefined") {
                    this.signUpModal = new Views.SignUpModal();
                }
                this.initValidator();
            },
            nextStep: function(event) {
                event.preventDefault();
                var view = this;
                var role = $(event.currentTarget).find('#role').val();
                if( role == '' ){
                    role = 'company';
                }
                if(view.form_validator.form()) {
                    view.model = new Models.mJobUser({
                        user_email: $(event.currentTarget).find('#user_email').val(),
                        role: role,
                        do_action: 'check_email'
                    });

                    this.model.save('', '', {
                        success: function (result, resp, jqXHR) {
                            if( resp.success ) {
                                // Close this modal
                                view.closeModal();

                                // Open next step - modal sign up
                                view.signUpModal.$el.find('form').append('<input type="hidden" id="user_email" name="user_email" value="'+ view.model.get('user_email') +'"/>');
                                view.signUpModal.$el.find('form').append('<input type="hidden" id="role" name="role" value="'+ view.model.get('role') +'"/>');
                                // Open modal
                                view.signUpModal.openModal();
                            }
                            else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: resp.msg,
                                    notice_type: 'error'
                                });
                            }
                        }
                    });

                }
            },
            initValidator: function() {
                var view = this;
                view.form_validator = view.$el.find('form').validate({
                    rules: {
                        user_email: {
                            required: true,
                            email: true
                        }
                    },
                    errorElement: "p",
                    highlight:function(element, errorClass, validClass){
                        var $target = $(element );
                        var $parent = $(element ).parent();
                        $parent.addClass('has-error');
                        $target.addClass('has-visited');
                    },
                    unhighlight:function(element, errorClass, validClass){
                        // position error label after generated textarea
                        var $target = $(element );
                        var $parent = $(element ).parent();
                        $parent.removeClass('has-error');
                        $target.removeClass('has-visited');
                    }
                })
            }
        });
        // MODAL SIGN UP STEP 1: Input email
        Views.selectUserRole = Views.Modal_Box.extend({
            el: '#selectUserRole',
            events: {
                'click .btn-select-user-role' : 'nextStep'
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                AE.pubsub.trigger('ae:init:modal:signup', this);
                if(typeof this.SignUpEmailModal === "undefined") {
                    this.SignUpEmailModal = new Views.SignUpEmailModal();
                }
            },
            nextStep: function(event) {
                var view = this;
                event.preventDefault();
                view.SignUpEmailModal.$el.find('form').append('<input type="hidden" id="role" name="role" value="'+ $(event.currentTarget).attr('data-value') +'"/>');
                view.SignUpEmailModal.openModal();
                view.closeModal();
            }
        });
        // MODAL SIGN UP
        Views.SignUpModal = Views.Modal_Box.extend({
            el: '#signUpStep2',
            initialize: function() {
                AE.pubsub.on('ae:form:submit:success', this.registerSuccess, this);
                },
            registerSuccess: function(result, resp, jqXHR, type) {
                if(type == 'signUp') {
                    var view = this;
                    if(resp.success == true) {
                        view.closeModal();
                        if(ae_globals.user_confirm == "1") {
                            AE.pubsub.trigger('ae:notification', {
                                msg: resp.msg,
                                notice_type: 'success'
                            });

                            setTimeout(function() {
                                if(typeof resp.data.redirect_url !== 'undefined') {
                                    window.location.href = resp.data.redirect_url;
                                } else {
                                    window.location.reload();
                                }
                            }, 4000);
                        } else {
                            if(typeof resp.data.redirect_url !== 'undefined') {
                                window.location.href = resp.data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        });
                    }
                }
            },
        });

        // MODAL SIGN IN
        Views.SignInModal = Views.Modal_Box.extend({
            el: '#signIn',
            initialize: function() {
                AE.pubsub.on('ae:form:submit:success', this.authSuccess, this);
                AE.pubsub.on('ae:init:modal:signup', this.close, this);
                AE.pubsub.on('ae:init:modal:forgot', this.close, this);
            },
            authSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(type == 'signIn') {
                    if(resp.success == true) {
                        view.closeModal();
                        if(typeof resp.data.redirect_url !== 'undefined') {
                            window.location.href = resp.data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        });
                    }
                }
            },
            close: function() {
                var view = this;
                view.closeModal();
            }
        });
        Views.UserAuthenticationPage = Backbone.View.extend({
            el: '.page-user-authentication',
            events:{
                'keyup .check-user-email': 'checkEmail',
                'keyup #user_email': 'checkEmailValid',
                'keyup #user_login': 'checkEmailValidLogin'
            },
            initialize: function() {
                var view = this;
                AE.pubsub.on('ae:form:submit:success', this.authSuccess, this);
            },
            checkEmail: function(e){
                var view  = this;
                if(e.keyCode == 13 ){
                    e.preventDefault();
                }
                var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
                if(reg.test($('#check_user_email').val()) === true){
                    setTimeout(function(){
                        view.model = new Models.mJobUser({
                            check_user_email: $('#check_user_email').val(),
                            do_action: 'check_email'
                        });
                        view.model.save('', '', {
                            success: function (result, resp, jqXHR) {
                                if (resp.success) {
                                    view.$el.find('#signUpForm').show();
                                    view.$el.find('#user_email').val(resp.user_email);
                                    view.$el.find('#user_email').focus();
                                    view.$el.find('#user_login').val('sdfsdf');
                                    view.$el.find('#signUpFormStep1').hide();
                                }
                                else {
                                    view.$el.find('#signInForm').show();
                                    view.$el.find('#user_login').val(resp.user_email);
                                    view.$el.find('#signUpFormStep1').hide();
                                }
                            }
                        });
                       // e.preventDefault();
                    }, 1000);
                }
            },
            checkEmailValid: function(e){
                var view = this;
                if(e.keyCode != 37 && e.keyCode != 39 ) {
                    view.model = new Models.mJobUser({
                        check_user_email: $(event.currentTarget).find('#user_email').val(),
                        do_action: 'check_email'
                    });
                    var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
                    if (reg.test($('#user_email').val()) === true) {
                        this.model.save('', '', {
                            success: function (result, resp, jqXHR) {
                                if (!resp.success) {
                                    if (resp.show == 1) {
                                        view.$el.find('#signInForm').show();
                                        view.$el.find('#user_login').val(resp.user_email);
                                        view.$el.find('#user_login').focus();
                                        if (view.$el.find('#signUpFormStep1').length > 0) {
                                            view.$el.find('#signUpFormStep1').hide();
                                        }
                                        if (view.$el.find('#signUpForm').length > 0) {
                                            view.$el.find('#signUpForm').hide();
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            },
            checkEmailValidLogin: function(e){
                var view = this;
                if(e.keyCode != 37 && e.keyCode != 39 ) {
                    view.model = new Models.mJobUser({
                        check_user_email: $(event.currentTarget).find('#user_login').val(),
                        do_action: 'check_email'
                    });
                    var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
                    if (reg.test($('#user_login').val()) === true) {
                        this.model.save('', '', {
                            success: function (result, resp, jqXHR) {
                                if (resp.success) {
                                    if (resp.show == 1) {
                                        view.$el.find('#signUpForm').show();
                                        view.$el.find('#user_email').val(resp.user_email);
                                        view.$el.find('#user_email').focus();
                                        if (view.$el.find('#signUpFormStep1').length > 0) {
                                            view.$el.find('#signUpFormStep1').hide();
                                        }
                                        if (view.$el.find('#signInForm').length > 0) {
                                            view.$el.find('#signInForm').hide();
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            },
            authSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(type == 'signIn') {
                    if(resp.success == true) {
                        if(typeof resp.data.redirect_url !== 'undefined') {
                            window.location.href = resp.data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        });
                    }
                }
                else if( type == 'signUp' ){
                    view.registerSuccess(result, resp, jqXHR, type);
                }
            },
            registerSuccess: function(result, resp, jqXHR, type) {
                if(type == 'signUp') {
                    var view = this;
                    if(resp.success == true) {
                        if(ae_globals.user_confirm == "1") {
                            AE.pubsub.trigger('ae:notification', {
                                msg: resp.msg,
                                notice_type: 'success'
                            });

                            setTimeout(function() {
                                if(typeof resp.data.redirect_url !== 'undefined') {
                                    window.location.href = resp.data.redirect_url;
                                } else {
                                    window.location.reload();
                                }
                            }, 4000);
                        } else {
                            if(typeof resp.data.redirect_url !== 'undefined') {
                                window.location.href = resp.data.redirect_url;
                            } else {
                                window.location.reload();
                            }
                        }
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        });
                    }
                }
            },
        });
        new Views.UserAuthenticationPage();
        /**
         * MODAL FORGOT PASSWORD
         */
        Views.ForgotPassword = Views.Modal_Box.extend({
            el: '#forgotPassword',
            initialize: function() {
                // do forgot password
                var forgotPasswordForm = new Views.AE_Form({
                    el: '#forgotPasswordForm',
                    model: new Models.User({
                        do: 'forgot',
                        method: 'read'
                    }),
                    rules: {
                        user_login: {
                            required: true,
                            email: true
                        }
                    },
                    type: 'forgotPassword',
                    blockTarget: '#forgotPasswordForm button'
                });

                AE.pubsub.on('ae:form:submit:success', this.sendResetPassSuccess, this);
                AE.pubsub.trigger('ae:init:modal:forgot', this);
            },
            sendResetPassSuccess: function(result, resp, jqXHR, type) {
                var view = this;
                if(resp.success == true) {
                    view.closeModal();
                }
            }
        });

        /**
         * A U T H E N T I C A T I O N  V I E W
         */
        Views.Authentication = Backbone.View.extend({
            el: 'body',
            events: {
                'click a.open-signin-modal' : 'openSignInModal',
                'click a.open-signup-modal' : 'openSignUpEmailModal',
                'click a.open-forgot-modal' : 'openForgotPasswordModal',
                'click a.focus-signin-form' : 'focusSignInForm',
                'click a.focus-signup-form' : 'focusSignUpForm',
            },
            initialize: function() {
                // do sign in
                var signInForm = new Views.AE_Form({
                    el: '#signInForm', // parent of form
                    model: new Models.User({
                        do: 'login',
                        method: 'read'
                    }),
                    rules: {
                        user_login: 'required',
                        user_pass: 'required'
                    },
                    type: 'signIn',
                    blockTarget: '#signInForm button',
                    showNotice: false
                });

                // do sign up
                var signUpForm = new Views.AE_Form({
                    el: '#signUpForm',
                    model: new Models.User(),
                    rules: {
                        user_login: "required",
                        user_pass: "required",
                        repeat_pass: {
                            required: true,
                            equalTo: "#signUpForm #user_pass"
                        },
                        user_email: {
                            required: true,
                            email: true
                        },
                        term_privacy: "required",
                    },
                    type: 'signUp',
                    blockTarget: '#signUpForm button',
                    showNotice: false
                });

                // do reset password
                var resetPassForm = new Views.AE_Form({
                    el: '#resetPassForm',
                    model: new Models.User({
                        do: 'resetpass'
                    }),
                    rules: {
                        new_password: 'required',
                        repeat_pass: {
                            required: true,
                            equalTo: '#resetPassForm #new_password'
                        }
                    },
                    type: 'resetPassword',
                    blockTarget: '#resetPassForm button'
                });

                // do change password
                if($('#current_user').length > 0) {
                    var changePassModel = new Models.User(currentUser.data);
                } else {
                    var changePassModel = new Models.User();
                }
                changePassModel.set('do', 'changepass');
                var changePassForm = new Views.AE_Form({
                    el: '#changePassForm',
                    model: changePassModel,
                    rules: {
                        old_password: 'required',
                        new_password: 'required',
                        renew_password: {
                            required: true,
                            equalTo: '#changePassForm #new_password'
                        }
                    },
                    type: 'changePassword',
                    blockTarget: '#changePassForm button'
                });

                AE.pubsub.on('ae:form:submit:success', this.submitSuccess, this);
            },
            openSignInModal: function(event) {
                event.preventDefault();
                if(typeof this.signInModal === 'undefined') {
                    this.signInModal = new Views.SignInModal();
                }
                this.signInModal.openModal();
                AE.App.authModal = this.signInModal;
            },
            openSignUpEmailModal: function(event) {
                event.preventDefault();
                if( typeof this.selectUserRole === 'undefined' ) {
                    this.selectUserRole = new Views.selectUserRole();
                }

                // close signin modal
                if(typeof this.signInModal !== 'undefined') {
                    this.signInModal.closeModal();
                }

                this.selectUserRole.openModal();
                AE.App.authModal = this.emailModal;
            },
            openForgotPasswordModal: function(event) {
                event.preventDefault();
                if(typeof this.forgotModal === 'undefined') {
                    this.forgotModal = new Views.ForgotPassword();
                }
                // close signin modal
                if(typeof this.signInModal !== 'undefined') {
                    this.signInModal.closeModal();
                }
                this.forgotModal.openModal();
            },
            submitSuccess: function(result, resp, jqXHR, type) {
                if(type == 'resetPassword') {
                    if(resp.success == true && typeof resp.data.redirect_url != 'undefined') {
                        window.location.href  = resp.data.redirect_url;
                    }
                } else if((type == 'signIn'  || type == 'signUp') && (resp.success)) {
                    AE.pubsub.trigger('ae:user:auth', result, resp, jqXHR);

                    // Live update user section on main navigation
                    if($('#mjob_my_account_header').length > 0) {
                        var template = _.template($('#mjob_my_account_header').html());
                        $('#myAccount').html(template({
                            avatar: resp.data.avatar,
                            display_name: resp.data.display_name
                        }));
                    }

                    //if(typeof resp.data.redirect_url != 'undefined') {
                    //    window.location.href = resp.data.redirect_url;
                    //}
                } else if(type == 'changePassword') {
                    if(resp.success == true) {
                        if(typeof resp.data.redirect_url != 'undefined') {
                            window.location.href = resp.data.redirect_url;
                        }
                    }
                }
            },
            focusSignInForm: function(event) {
                event.preventDefault();
                var signInForm = $('#signInForm');
                var signUpForm = $('#signUpForm');

                signUpForm.hide();
                signInForm.fadeIn();
                signInForm.find('#user_login').focus();
            },
            focusSignUpForm: function(event) {
                event.preventDefault();
                var signInForm = $('#signInForm');
                var signUpForm = $('#signUpForm');

                signInForm.hide();
                signUpForm.fadeIn();
                signUpForm.find('#user_email').focus();
            }
        });
        new Views.Authentication();
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections);