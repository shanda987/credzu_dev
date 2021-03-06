(function($, Views, Models, Collection, AE) {
    $(document).ready(function() {
        Models.mJobProfile = Backbone.Model.extend({
            action: 'mjob_sync_profile',
            initialize: function () {
            }
        });

        Models.mJobUser = Backbone.Model.extend({
            action: 'mjob_sync_user'
        });

        /**
         * Strip HTML tag
         */
        function stripTag(string) {
            if(typeof string !== "undefined") {
                return string.replace(/(<([^>]+)>)/ig,"");
            }
        }

        /**
         * MODAL UPLOAD AVATAR
         */
        Views.UploadAvatar = Views.Modal_Box.extend({
            el: '#uploadAvatar',
            events: {
                'click .btn-save': 'saveAvatar',
                'click .btn-remove': 'removeAvatar'
            },
            initialize: function() {
                if($('#user_id').length > 0) {
                    var data = JSON.parse($('#user_id').html());
                    this.user = new Models.mJobUser(data);
                } else {
                    this.user = new Models.mJobUser();
                }

                this.uploadID = 'upload_avatar';
                this.container = $('#uploadAvatar');

                // Crop image variables
                this.cropX = '';
                this.cropY = '';
                this.cropWidth = '';
                this.cropHeight = '';
                this.attacmentID = '';

                // Init uploader
                this.initUploader();

                // Init block ui
                this.blockUi = new Views.BlockUi();

            },
            initUploader: function(event) {
                var view = this;
                if(typeof view.avatarUploader === 'undefined') {
                    view.avatarUploader = new Views.File_Uploader({
                        el: view.container,
                        uploaderID: view.uploadID,
                        thumbsize: 'thumbnail',
                        multipart_params: {
                            _ajax_nonce: view.container.find('.et_ajax_nonce').val(),
                            data: {
                                method: 'change_avatar',
                                author: view.user.get('ID')
                            },
                            imgType: view.uploadID,
                        },
                        dragdrop: true,
                        // When picture uploadeds
                        cbUploaded: function(up, file, res) {
                            if(res.success) {
                                // Append preview image
                                var previewImg = '<div class="preview-image"><img class="preview-image-src" src="#"</div>';
                                view.container.find('.image-upload').append(previewImg);
                                view.imgCropper = view.container.find('.preview-image-src');

                                // Enable save button
                                view.container.find('.btn-save').removeAttr('disabled');
                                view.container.find('.btn-remove').css({display: 'inline'});

                                // Hide upload button
                                view.container.find('.browse_button').hide();
                                view.container.find('.moxie-shim').hide();
                                var imgURL = res.data.full[0];
                                view.attach_id = res.data.attach_id;

                                // Init image cropper
                                view.imgCropper.attr('src', imgURL);
                                view.imgCropper.show();
                                view.imgCropper.cropper({
                                    aspectRatio: 1/1,
                                    zoomable: false,
                                    scalable: false,
                                    rotatable: false,
                                    minCropBoxWidth: 150,
                                    minCropBoxHeight: 150,
                                    crop: function(e) {
                                        // Output the result data for cropping image.
                                        view.cropX = e.x;
                                        view.cropY = e.y;
                                        view.cropWidth = e.width;
                                        view.cropHeight = e.height;
                                    }
                                });
                            }
                        },
                        beforeSend: function() {
                            view.blockUi.block(($('#' + view.uploadID + '_container')));
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                        }
                    })
                }
            },
            // Crop and save avatar
            saveAvatar: function(event) {
                event.preventDefault();
                var view = this;
                var target = $(event.currentTarget);
                $.ajax({
                    type: 'POST',
                    url: ae_globals.ajaxURL,
                    data: {
                        action: 'mjob_crop_avatar',
                        crop_x: view.cropX,
                        crop_y: view.cropY,
                        crop_width: view.cropWidth,
                        crop_height: view.cropHeight,
                        attach_id: view.attach_id,
                        user_id: view.user.get('ID')
                    },
                    beforeSend: function() {
                        view.blockUi.block(target);
                    },
                    success: function(resp, status, jqXHR) {
                        if(resp.success == true) {
                            // Show browse button
                            view.resetModal();

                            $('.profile-avatar').find('img').attr('src', resp.data.thumbnail[0]);

                            // Live update avatar on header
                            if($('#mjob_my_account_header').length > 0) {
                                var display_name = $('#myAccount').find('.display-name').text();
                                AE.pubsub.trigger('mjob:update:user', {
                                    avatar: resp.data.thumbnail[0],
                                    display_name: display_name
                                });
                            }
                            // Close modal
                            view.closeModal();

                            // Show notification
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: resp.msg
                            })
                        } else {
                            // Show notification
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'error',
                                msg: resp.msg
                            })
                        }
                    },
                    complete: function(){
                        view.blockUi.unblock();
                    }
                })

            },
            // Remove avatar
            removeAvatar: function(event) {
                event.preventDefault();
                var view = this;

                // Reset modal
                view.resetModal();

                // Remove aatar
                $.ajax({
                    type: 'POST',
                    url: ae_globals.ajaxURL,
                    data: {
                        action: 'ae_remove_carousel',
                        id: view.attach_id
                    },
                    beforeSend: function() {

                    },
                    success: function(resp, status, jqXHR) {

                    }
                })
            },
            resetModal: function(event) {
                var view = this;
                // Show browse button
                view.container.find('.browse_button').show();
                view.container.find('.moxie-shim').show();

                // Disable save button
                view.container.find('.btn-save').attr('disabled', 'false');
                view.container.find('.btn-remove').css({display: 'none'});

                // Remove preview image
                view.container.find('.preview-image').remove();
            }
        });
        Views.ModalChangeUserRole = Views.Modal_Box.extend({
            el: '#change_user_role_modal',
            events: {
                'click .btn-change-user-role': 'changeToCompany',
                'click .role-back-return': 'closeConfirmModal'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function (model, $target) {
                var view = this;
                this.model = model;
                this.target = $target;
                view.openModal();
            },
            closeConfirmModal: function(e){
                e.preventDefault();
                this.closeModal();
            },
            changeToCompany: function(e){
                var view = this;
                e.preventDefault();
                $target  = $(e.currentTarget);
                var gdata = {
                    action: 'mjob-change-user-role'
                };
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: gdata,
                    beforeSend: function () {
                        view.blockUi.block($target);
                    },
                    success: function (resp) {
                        if (resp.success) {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: resp.msg
                            });
                            window.location.reload(true);
                        }
                        else{
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'error',
                                msg: resp.msg
                            })
                        }
                        view.blockUi.unBlock();
                    }
                });
            }
        });
        /**
         * VIEWS FOR PROFILE
         */
        Views.Profile = Backbone.View.extend({
            el: '.mjob-profile-page',
            events: {
                'click .text-content': 'openEditArea',
                'focusout .input-field': 'onChangeInput',
                'focusout textarea.editable': 'onChangeTextarea',
                'click textarea.editable': 'onClickTextarea',
                'change select': 'onChangeInput',
                'click .upload-profile-avatar': 'openUploadModal',
                'keypress .input-field': 'enterChangeInput',
                'keypress .textarea.editable': 'enterChangeTextarea',
                'keypress input[name="first_name"]': 'characterOnly',
                'keypress input[name="last_name"]': 'characterOnly',
                'focusout input[name="billing_full_address"]': 'checkSmarty',
                'click .profile-page-role #role_company': 'changeUserRole'
            },
            initialize: function () {
                // Resize textarea
                if( this.$el.find('textarea').length > 0) {
                    autosize(this.$el.find('textarea'));
                }

                this.blockUi = new Views.BlockUi();

                //Init model
                if($('#mjob_profile_data').length > 0) {
                    data = JSON.parse($('#mjob_profile_data').html());
                    this.model = new Models.mJobProfile(data);
                }
                else {
                    this.model = new Models.mJobProfile();
                    if($('#current_user').length > 0) {
                        this.model.set({
                            post_author: currentUser.data.ID,
                            post_type: 'mjob_profile',
                            post_title: currentUser.data.display_name,
                            post_status: 'publish',
                            post_content: '',
                            payment_info: '',
                            billing_full_name: '',
                            billing_full_address: '',
                            billing_country: '',
                            billing_vat: '',
                            first_name: '',
                            last_name: '',
                            phone: '',
                            business_email: '',
                            credit_goal: '',
                            company_name: '',
                            company_address: '',
                            company_phone: '',
                            company_email: '',
                            company_website: '',
                            company_year_established: '',
                            company_amount_of_employees: '',
                            company_description: ''
                        })
                    }
                }

                //Init user
                if($('#current_user').length > 0) {
                    this.user = new Models.mJobUser(currentUser.data);
                } else {
                    this.user = new Models.mJobUser();
                }

                // Set nonce for security purpose
                this.model.set('_wpnonce', $('#profile_wpnonce').val());
                rules = {
                    first_name: {
                        required: true,
                    },
                    last_name: 'required',
                        phone: 'required',
                        email: 'required',
                        billing_full_address: 'required',
                        city: 'required',
                        state: 'required',
                        zip_code: 'required',
                };
                type = 'update-profile';
                if($('.page-template-page-profile-company').length > 0){
                    type = 'update-profile-company'
                    rules = {
                        company_name: {
                            required: true,
                        },
                        company_address: 'required',
                        company_city: 'required',
                        company_state: 'required',
                        company_zip_code: 'required',
                        company_phone: 'required',
                        company_email: 'required',
                        company_year_established: 'required',
                        company_amount_of_employees: 'required',
                        company_description: 'required',
                    };
                }
                if(typeof this.profileForm === "undefined") {
                    this.profileForm = new Views.AE_Form({
                        el: '.mjob-profile-form', // Wrapper of form
                        model: this.model,
                        rules: rules,
                        type: type,
                        blockTarget: '.mjob-profile-form button'
                    });
                }
                $('input[name="phone"]').mask('(999)-999-9999');
                $('input[name="company_phone"]').mask('(999)-999-9999');
            },
            enterChangeInput: function(event){
                var view = this;
                if( event.keyCode == 13 ){
                    view.onChangeInput(event);
                }
            },
            enterChangeTextarea: function(event){
                var view = this;
                if( event.keyCode == 13 ){
                    view.onChangeInput(event);
                }
            },
            openEditArea: function (event) {
                event.preventDefault();
                var target = $(event.currentTarget),
                    id = target.attr('data-id'),
                    name = target.attr('data-name'),
                    type = target.attr('data-type'),
                    edit = target.attr('data-edit'),
                    className = target.attr('class'),
                    tagName = target.prop('tagName').toLowerCase();

                if(edit == 'user') {
                    var content = stripTag(this.user.get(name));
                } else {
                    var content = stripTag(this.model.get(name));
                }

                switch (type) {
                    case 'input':
                        var html = '<input type="text" name="' + name + '" class="input-field" value="' + content + '" data-edit="'+ edit +'" data-id="'+ id +'" data-name="'+ name +'" data-type="'+ type +'" data-tag="'+ tagName +'" data-class="'+  className+'"/>';
                        break;

                    case 'textarea':
                        var html = '<textarea name="' + name + '" class="input-field" data-edit="'+ edit +'" data-id="'+ id +'" data-name="'+ name +'" data-type="'+ type +'" data-tag="'+ tagName +'" data-class="'+ className +'">' + content + '</textarea>';
                        break;
                }

                if(type != 'textarea') {
                    $(id).html(html);
                    $(id).find(type).focus();
                }
                return false;
            },
            // Save profile data
            onChangeInput: function (event) {
                event.preventDefault();
                if( $(event.currentTarget).attr('name') == 'state' || $(event.currentTarget).attr('name') == 'billing_state' || $(event.currentTarget).attr('name') == 'company_state'){
                    return false;
                }
                // Get value
                var view = this,
                    target = $(event.currentTarget),
                    id = target.attr('data-id'),
                    name = target.attr('data-name'),
                    type = target.attr('data-type'),
                    edit = target.attr('data-edit'),
                    className = target.attr('data-class'),
                    tagName = target.attr('data-tag');

                // Save model
                var user_object = '';
                if(edit == 'user') {
                    user_object = view.user;
                } else {
                    user_object = view.model;
                }

                // User object before saving
                var current_value = user_object.get(name);
                if(current_value == "") {
                    current_value = ae_globals.profile_empty_text;
                }

                if(typeof target.val() == 'string') {
                    // remove white spaces
                    var compareStr = target.val().replace(/\s+/g, '');
                    // remove break lines
                    compareStr = compareStr.replace(/(\r\n|\n|\r)/gm,"");
                } else {
                    compareStr = target.val();
                }

                // Check field empty
                if(compareStr !== "") {
                    // If select is chosen
                    if(target.hasClass('is-chosen')) {
                        user_object.set(target.attr('name'), target.val());
                        target = target.next('.chosen-container');
                    } else {
                        user_object.set(target.attr('name'), target.val());
                    }

                    // Check pending account
                    if(currentUser.data.register_status == '') {
                        user_object.save('', '', {
                            beforeSend: function () {
                                view.blockUi.block(target);
                            },
                            success: function (result, resp, jqXHR) {
                                if (resp.success == true) {
                                    view.updateElement(id, tagName, className, edit, name, type, user_object.get(name));

                                    // Live update display name
                                    if(typeof resp.data.display_name !== 'undefined') {
                                        var avatar = $('#myAccount').find('.display-avatar img').attr('src');
                                        AE.pubsub.trigger('mjob:update:user', {
                                            avatar: avatar,
                                            display_name: resp.data.display_name
                                        });
                                    }
                                } else {
                                    // If update failed
                                    view.updateElement(id, tagName, className, edit, name, type, current_value);

                                    AE.pubsub.trigger('ae:notification', {
                                        notice_type: 'error',
                                        msg: resp.msg
                                    })
                                }

                                view.blockUi.unblock();
                            }
                        })
                    } else {
                        view.updateElement(id, tagName, className, edit, name, type, current_value);
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'error',
                            msg: ae_globals.pending_account_error_txt
                        })
                    }
                } else {
                    // If empty value
                    view.updateElement(id, tagName, className, edit, name, type, current_value);
                }
            },
            onClickTextarea: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget);

                if($target.hasClass('editing') == true) {
                    return false;
                }

                $target.addClass('editing');

                var currentValue = view.model.get($target.attr('name'));
                if(currentValue == "") {
                    $target.val(currentValue);
                }
            },
            onChangeTextarea: function(event) {
                event.preventDefault();
                var view = this,
                    $target = $(event.currentTarget);

                $target.removeClass('editing');

                if(typeof $target.val() == 'string') {
                    // remove white spaces
                    var compareStr = $target.val().replace(/\s+/g, '');
                    // remove break lines
                    var compareStr = compareStr.replace(/(\r\n|\n|\r)/gm,"");
                } else {
                    compareStr = $target.val();
                }

                // User object before saving
                var current_value = view.model.get($target.attr('name'));
                if(current_value == "") {
                    current_value = ae_globals.profile_empty_text;
                }

                if(compareStr != "") {
                    // Check pending account
                    if(currentUser.data.register_status == '') {
                        view.model.set($target.attr('name'), $target.val());
                        view.model.save('', '', {
                            beforeSend: function () {
                                view.blockUi.block($target);
                            },
                            success: function (result, resp, jqXHR) {
                                if (resp.success == true) {

                                } else {
                                    AE.pubsub.trigger('ae:notification', {
                                        notice_type: 'error',
                                        msg: resp.msg
                                    })
                                }

                                view.blockUi.unblock();
                            }
                        });
                    } else {
                        $target.val(current_value);
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'error',
                            msg: ae_globals.pending_account_error_txt
                        })
                    }
                } else {
                    $target.val(current_value);
                    autosize.update($target);
                }
            },
            //Open upload modal
            openUploadModal: function(event) {
                event.preventDefault();
                if(typeof this.modal === 'undefined') {
                    this.modal = new Views.UploadAvatar();
                }
                this.modal.openModal();
            },
            updateElement: function(id, tagName, className, edit, name, type, value) {
                var html = '<'+ tagName +' class="'+ className +'" data-edit="'+ edit +'" data-id="'+ id +'" data-name="'+ name +'" data-type="'+ type +'">'+ value +'</'+ tagName +'>';
                $(id).html(html);
            },
            characterOnly: function(event){
                if (event.charCode!=0) {
                    var regex = new RegExp("^[a-zA-Z]+$");
                    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                    if (!regex.test(key)) {
                        event.preventDefault();
                        return false;
                    }
                }
            },
            checkSmarty: function(event){
                var $target = $(event.currentTarget);
                var gdata = {
                    action: 'mjob-check-smarty-address',
                    address: $target.val()
                };
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: gdata,
                    beforeSend: function () {
                    },
                    success: function (res) {
                        if (res.success) {

                        }
                    }
                });
            },
            changeUserRole: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                if (typeof this.changeUserRoleModal === 'undefined') {
                    this.changeUserRoleModal = new Views.ModalChangeUserRole();
                }
                this.changeUserRoleModal.onOpen(this.model, $target);
            }
        });
        new Views.Profile();
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections, window.AE);