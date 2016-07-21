/**
 * Created by Jack Bui on 1/28/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function () {
        //
        /**
         * mjob collections
         */
        Collections.Order = Backbone.Collection.extend({
            model: Models.Mjob,
            action: 'ae-fetch-mjob_order',
            initialize: function() {
                this.paged = 1;
            }
        });
        /**
         * define mjob item view
         */
        var orderItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'order-item',
            template: _.template($('#order-item-loop').html()),
            initialize: function() {
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
            },
        });

        // Init view for task item
        var taskItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'task-item',
            template: _.template($('#task-item-loop').html()),
            initialize: function() {
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
            },
        });

        /**
         * list view control order list
         */
        ListOrders = Views.ListPost.extend({
            tagName: 'ul',
            itemView: orderItem,
            itemClass: 'order-item'
        });

        /**
         * list view control task list
         */
        ListTasks = Views.ListPost.extend({
            tagName: 'ul',
            itemView: taskItem,
            itemClass: 'task-item'
        });

        $('.order-container-control').each(function() {
            if (typeof orderCollection == 'undefined') {
                //Get public  collection
                if ($('.order_postdata').length > 0) {
                    var order = JSON.parse($('.order_postdata').html());
                    orderCollection = new Collections.Order(order);
                } else {
                    orderCollection = new Collections.Order();
                }
            }
            /**
             * init list blog view
             */
            var listOrders = new ListOrders({
                itemView: orderItem,
                collection: orderCollection,
                el: $(this).find('.list-orders')
            });
            //post-type-archive-project
            //old block-projects
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: orderCollection,
                el: $(this),
                onAfterFetch: function(result, res){
                    var view = this;
                    if(res.success ){
                        if( view.$el.find('.no-items').length > 0){
                            view.$el.find('.no-items').remove();
                        }
                        if( res.data.length == 0 ){
                            $('.list-orders').html(ae_globals.no_orders);
                        }

                    }
                    else{
                        $('.list-orders').html(ae_globals.no_orders);
                    }
                }
            });
        });

        // Tasks block control
        $('.task-container-control').each(function() {
            if (typeof taskCollection == 'undefined') {
                //Get public  collection
                if ($('.task_postdata').length > 0) {
                    var order = JSON.parse($('.task_postdata').html());
                    taskCollection = new Collections.Order(order);
                } else {
                    taskCollection = new Collections.Order();
                }
            }
            var listTasks = new ListTasks({
                itemView: taskItem,
                collection: taskCollection,
                el: $(this).find('.list-tasks')
            });
            new Views.BlockControl({
                collection: taskCollection,
                el: $(this),
                onAfterFetch: function(result, res){
                    var view = this;
                    if(res.success ){
                        if( view.$el.find('.no-items').length > 0){
                            view.$el.find('.no-items').remove();
                        }
                        if( res.data.length == 0 ){
                            $('.list-tasks').html(ae_globals.no_orders);
                        }

                    }
                    else{
                        $('.list-tasks').html(ae_globals.no_orders);
                    }
                }
            });
        });

        Models.Delivery = Backbone.Model.extend({
            action: 'ae-order_delivery-sync',
            initialize: function() {}
        });
        /**
         * mjob collections
         */
        Collections.Delivery = Backbone.Collection.extend({
            model: Models.Delivery,
            action: 'ae-fetch-order_delivery',
            initialize: function() {
                this.paged = 1;
            }
        });
        Views.SingleOrder = Backbone.View.extend({
            el: '.mjob-single-order-page',
            events: {
                'change .order-action': 'changeOrderStatus',
                'click .order-action': 'changeOrderStatus',
                'click .order-delivery-btn': 'openModalDelivery',
                'click .mjob-dispute-order': 'showFormDispute',
                'click .requirement-item': 'showModalRequirement',
                'click .show-requirement-doc': 'showRequirementContent',
                'click .btn-work-complete-action': 'showWorkComplete',
                'click .btn-continue-service-btn': 'showContinue'
            },
            initialize: function () {
                var view = this;
                if( $('#order_single_data').length > 0 ){
                    var postdata = JSON.parse($('#order_single_data').html());
                    view.model = new Models.Order(postdata);
                }
                else{
                    view.model = new Models.Order();
                }
                AE.pubsub.on('ae:form:submit:success', this.deliverySuccess, this);
                this.blockUi = new Views.BlockUi();
                if( typeof view.disputeModel === 'undefined' ) {
                    view.disputeModel = new Models.Message();
                }
                view.disputeModel.set('post_parent', this.model.get('ID'));
                view.disputeModel.set('type', 'dispute');
                view.disputeForm = new Views.AE_Form({
                    el: '.mjob-dispute-form', // parent of form
                    model: view.disputeModel,
                    rules: {
                        post_content: 'required'
                    },
                    type: 'dispute',
                    blockTarget: '.mjob-dispute-form button'
                });
                if (typeof view.carousels_dispute === 'undefined') {
                    view.carousels_dispute = new Views.Carousel({
                        el: $('.gallery_container_dispute'),
                        uploaderID:'carousel_dispute',
                        model: view.disputeModel,
                        carouselTemplate: '#ae_carousel_file_template',
                        extensions: ae_globals.file_types

                    });
                }
                if( $('.mjob-admin-dispute-form').length > 0 ){
                    view.adminDisputeForm = new Views.AE_Form({
                        el: '.mjob-admin-dispute-form', // parent of form
                        model: view.disputeModel,
                        rules: {
                            post_content: 'required'
                        },
                        type: 'dispute',
                        blockTarget: '.mjob-admin-dispute-form button'
                    });
                }
                if( !$('#noti-show').val() ){
                    $('.requirement-task').find('i').hide();
                }
                else{
                    $('.requirement-task').find('i').show();
                }
            },
            changeOrderStatus: function(e){
                e.preventDefault();
                var $target = $(e.currentTarget);
                var view = this;
                if( $target.attr('value') == 'late' ) {
                    view.model.set('late', 1);
                    view.model.set('noSetupPayment', false);
                    view.model.save('late', '1', {
                        beforeSend: function () {
                            view.blockUi.block($target)
                        },
                        success: function (result, res, xhr) {
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                $('.order_status').html(res.data.status_text);
                                $('.order_status').addClass(res.data.status_text_color);
                                $target.remove();
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                            view.blockUi.unblock();

                        }
                    });
                }
                else if( $target.attr('value') == 'finished' ){
                    var view = this;
                    if( typeof view.reviewModal  === 'undefined' ){
                        view.reviewModal = new Views.ModalReview();
                    }
                    view.reviewModal.onOpen(view.model);
                }
            },
            openModalDelivery: function(e){
                e.preventDefault();
                var view = this,
                $target = $(e.currentTarget);
                var parent = $target.attr('data-id');
                if( typeof view.deliveryModal  === 'undefined' ){
                    view.deliveryModal = new Views.ModalDelivery();
                }
                view.deliveryModal.onOpen(parent);
            },
            deliverySuccess: function(result, resp, jqXHR, type){
                var view = this;
                if( type == 'delivery' ){
                    view.deliveryModal.closeModal();
                    $('.order_status').html(ae_globals.delivery_status);
                    window.location.reload(true);
                }
                else if( type == 'dispute' ){
                    $('.order_status').html(ae_globals.disputing_status);
                    window.location.reload(true);
                }
            },
            showFormDispute: function (e) {
                var view = this;
                e.preventDefault();
                $target = $(e.currentTarget);
                $('.mjob-dispute-order').fadeOut(500);
                $('.mjob-dispute-form').fadeIn(500);
            },
            showRequirementContent: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var data_href = $target.attr('data-id');
                var name = $target.attr('data-name');
                var slug = $target.attr('data-slug');
                var click_type = $target.attr('data-type');
                if (typeof this.modalrequirementcontent === 'undefined') {
                    this.modalrequirementcontent = new Views.ModalRequirementContent();
                }
                this.modalrequirementcontent.onOpen(this.model, data_href, $target, name, slug, click_type);
            },
            showModalRequirement: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var data_id = $target.attr('data-id');
                var type = $target.attr('data-type');
                var modal_name = $target.attr('data-modal-name');
                var checkbox_name = $target.attr('data-checkbox-name');
                if( type == 'open-contact-info'){
                    if( $('#mjob_profile_data').length > 0 ){
                        this.profileModel = new Models.mJobProfile(JSON.parse($('#mjob_profile_data').html()));
                    }
                    else{
                        this.profileModel = new Models.mJobProfile();
                    }
                    if (typeof this.modalcontactinfo === 'undefined') {
                        this.modalcontactinfo = new Views.ModaContactInfo();
                    }
                    this.modalcontactinfo.onOpen(this.profileModel, $target, this.model, data_id);
                }
                else if( type == 'open-billing-info'){
                    if( $('#mjob_profile_data').length > 0 ){
                        this.profileModel = new Models.mJobProfile(JSON.parse($('#mjob_profile_data').html()));
                    }
                    else{
                        this.profileModel = new Models.mJobProfile();
                    }
                    if (typeof this.modalbilinginfo === 'undefined') {
                        this.modalbilinginfo = new Views.ModaBillingInfo();
                    }
                    this.modalbilinginfo.onOpen(this.profileModel,  $target, this.model, data_id);
                }
                else {
                    var data_name = $target.attr('data-name');
                    if (typeof this.modalrequirement === 'undefined') {
                        this.modalrequirement = new Views.ModalRequirement();
                    }
                    this.modalrequirement.onOpen(this.model, data_id, $target, data_name, modal_name, checkbox_name);
                }
            },
            showWorkComplete: function(e){
                e.preventDefault();
                if (typeof this.modalworkcomplete === 'undefined') {
                    this.modalworkcomplete = new Views.ModalWordComplete();
                }
                var target = $(e.currentTarget);
                this.modalworkcomplete.onOpen(this.model, target);
            },
            showContinue: function(e){
                e.preventDefault();
                var view = this;
                if (typeof this.modalContinue === 'undefined') {
                    this.modalContinue = new Views.ModalReorder();
                }
                this.modalContinue.onOpen(this.model);
            }
        });
        Views.ModalRequirementContent = Views.Modal_Box.extend({
            el: '#show_requirement_modal',
            events: {
                'click .btn-close': 'closeModalR',
                'click .resend-requirement': 'showModalUnlockRequirement'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
            },
            closeModalR: function(e){
                e.preventDefault();
                this.closeModal();
            },
            onOpen: function (model, data_href, $target, name, slug, click_type) {
                var view = this;
                this.model = model;
                this.data_href = data_href;
                this.target = $target;
                this.name = name;
                this.slug = slug;
                this.click_type  = click_type;
                view.openModal();
                $('#modal-show-requirement-title').html(name);
                $('.show-requirement-iframe').attr('src', this.data_href);
                var pid = data_href;
                if( this.target.attr('data-agreement') == 1 ){
                    view.$el.find('iframe').show();
                    $('.show-requirement-modal').addClass('padding-top-0');
                    view.$el.find('iframe').attr('src', ae_globals.view_pdf+'?cid='+pid+'&n='+this.name);
                    view.$el.find('.btn-download').click(function(){
                        window.location.href = ae_globals.download_pdf+'?cid='+pid+'&n='+view.name;
                    });
                    view.$el.find('.resend-requirement-css').hide();
                    view.$el.find('.show-requirement-img').hide();
                }
                else if( this.target.attr('data-payment') == 1 ){
                    view.$el.find('iframe').show();
                    view.$el.find('iframe').attr('src', ae_globals.view_pdf + '?pid=' + pid);
                    $('.show-requirement-modal').addClass('padding-top-0');
                    view.$el.find('.btn-download').click(function(){
                        window.location.href = ae_globals.download_pdf+'?pid='+pid;
                    });
                    view.$el.find('.resend-requirement-css').hide();
                }
                else if( this.target.attr('data-attachment') == 1){
                    view.$el.find('iframe').attr('src', ae_globals.view_pdf + '?pid=' + pid+'&type=attachment');
                    if( this.target.attr('data-mine-type') == 'image/jpg' || this.target.attr('data-mine-type') == 'image/jpeg' || this.target.attr('data-mine-type') == 'image/png'){
                        view.$el.find('iframe').hide();
                        view.$el.find('.show-requirement-img').attr('src', ae_globals.view_pdf + '?pid=' + pid+'&type=attachment');
                        view.$el.find('.show-requirement-img').show();
                        $('.show-requirement-modal').removeClass('padding-top-0');
                    }
                    else{
                        view.$el.find('iframe').show();
                        view.$el.find('.show-requirement-img').hide();
                        $('.show-requirement-modal').addClass('padding-top-0');
                    }
                    view.$el.find('.btn-download').click(function(){
                        window.location.href = ae_globals.download_pdf+'?pid='+pid+'&type=attachment';
                    });
                    view.$el.find('.resend-requirement-css').hide();
                }
                else {
                    view.$el.find('iframe').attr('src', ae_globals.view_pdf + '?id=' + pid);
                    if( this.target.attr('data-mime-type') == 'image/jpg' || this.target.attr('data-mime-type') == 'image/jpeg' || this.target.attr('data-mime-type') == 'image/png'){
                        view.$el.find('iframe').hide();
                        view.$el.find('.show-requirement-img').attr('src', ae_globals.view_pdf + '?id=' + pid)
                        view.$el.find('.show-requirement-img').show();
                        $('.show-requirement-modal').removeClass('padding-top-0');
                    }
                    else{
                        view.$el.find('iframe').show();
                        view.$el.find('.show-requirement-img').hide();
                    }
                    view.$el.find('.btn-download').click(function(){
                        window.location.href = ae_globals.download_pdf+'?id='+pid;
                    });
                    view.$el.find('.resend-requirement-css').show();
                }
            },
            showModalUnlockRequirement: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var data_id = $target.attr('data-slug');
                var type = $target.attr('data-type');
                var data_name = $target.attr('data-name');
                if (typeof this.modalunlockrequirement === 'undefined') {
                    this.modalunlockrequirement = new Views.ModalUnlockRequirement();
                }
                this.modalunlockrequirement.onOpen(this.model, this.slug, $target, this.name);
                this.closeModal();
            }
        });
        Views.ModalRequirement = Views.Modal_Box.extend({
            el: '#requirement_modal',
            events: {
                'click .btn-save-requirement': 'saveOrderRequirment',
                'change #allow_upload': 'allow_upload'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                AE.pubsub.on('carousels:success:upload', this.showListFile, this);
                AE.pubsub.on('ae:carousel:after:remove', this.removeAll, this);
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function(model, data_id, $target, data_name, modal_name, checkbox_name){
                var view = this;
                this.model = model;
                this.data_id = data_id;
                this.target = $target;
                this.arr_ids = [];
                view.openModal();
                view.$el.find('.requirement-modal-title').html(modal_name);
                view.$el.find('.requirement-modal-title-allow').html(checkbox_name);
                $('.requirement-modal-title-here').html(modal_name+' here');
                $('#allow_upload').attr('checked', false);
                view.initCarousel();
            },
            initCarousel: function(){
                var view = this;
                view.container = $('#requirement_container');
                view.uploadID = 'requirement';
                view.$el.find('.requirement-image-list').html('');
                if(typeof view.requirementUploader === 'undefined') {
                    view.requirementUploader = new Views.Carousel({
                        el: view.container,
                        uploaderID: view.uploadID,
                        //multipart_params: {
                        //    _ajax_nonce: view.$el.find('.et_ajaxnonce').attr('id'),
                        //    // action: 'et-carousel-upload',
                        //    imgType: 'requirement',
                        //    author: view.model.get('post_author'),
                        //    data: view.uploadID
                        //},
                        name_item:'requirement_carousel',
                        model: this.model,
                        carouselTemplate: '#ae_carousel_file_template',
                        extensions: ae_globals.file_types,
                        dragdrop: true
                    })
                }
            },
            showListFile: function(up, file, res){
                var view = this;
                view.$el.find('.btn-save-requirement').attr('disabled', false);
                view.arr_ids.push(res.data.attach_id);
                $('.f-upload').hide();
            },
            saveOrderRequirment: function(e){
                e.preventDefault();
                var view = this;
                if( $('#allow_upload').prop('checked') && view.$el.find('.requirement-image-list .image-item').length > 0 ) {
                    var view = this;
                    this.model.set('requirement_files', view.arr_ids);
                    this.model.set('requirement_id', view.data_id);
                    view.model.set('need_upload_remove', view.data_id);
                    $target = $(e.currentTarget);
                    this.model.unset('ae_message');
                    this.model.unset('order_delivery');
                    this.model.save('', '', {
                        beforeSend: function () {
                            view.blockUi.block($target)
                        },
                        success: function (result, res, xhr) {
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                view.closeModal();
                                view.target.addClass('disabled');
                                view.target.find('i').removeClass('fa-square-o');
                                view.target.find('i').addClass('fa-check-square-o');
                                if (typeof res.data.doc_html !== 'undefined') {
                                    $('.document-list').html('');
                                    $('.document-list').append(res.data.doc_html);
                                }
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                            view.blockUi.unblock();

                        }
                    });
                }
                else{
                    console.log($(this).find('.requirement-image-list .image-item').length);
                    if( $(this).find('.requirement-image-list .image-item').length <= 0){
                        $('.f-upload').show();
                    }
                    if( !$('#allow_upload').prop('checked') ) {
                        $('.l-hide').show();
                    }
                }
            },
            removeAll: function(model){
                var view = this;
                if( view.$el.find('.requirement-image-list .image-item').length <= 0){
                    $('.f-upload').show();
                }
            },
            allow_upload: function(e){
                var view = this;
                $target = $(e.currentTarget);
                if($target.prop('checked')){
                    view.$el.find('.disablediv').removeClass('disablediv');
                }
                else{
                    view.$el.find('#requirement_container').addClass('disablediv');
                }
                $('.l-hide').hide();
            }
        });
        Views.ModaBillingInfo = Views.Modal_Box.extend({
            el: '#billing_info_modal',
            events: {
                'change select[name="use_billing_address"]': 'selectBilling',
                'change select[name="use_holder_account"]': 'selectAccount'
            },
            initialize: function () {
                AE.pubsub.on('ae:form:submit:success', this.afterSave, this);
            },
            onOpen: function(model, $target, orderModel, data_id){
                var view = this;
                this.model = model;
                view.openModal();
                view.setupFields(this.model);
                view.orderModel = orderModel;
                view.target  = $target;
                view.data_id = data_id;
                this.model.set('_wpnonce', $('#profile_wpnonce').val());
                var rules = {};
                use_address = this.model.get('use_billing_address');
                if( use_address == 'no') {
                    var rules = {
                        billing_other_address: 'required',
                        billing_city: 'required',
                        billing_state: 'required',
                        billing_zip_code: 'required'
                    }
                }
                if(typeof this.billingFormModal === "undefined") {
                    this.bilingFormModal = new Views.AE_Form({
                        el: '.form-confirm-billing-modal', // Wrapper of for
                        model: this.model,
                        rules: rules,
                        type: 'update-billing-hiring-modal',
                        blockTarget: '.form-confirm-billing-modal button'
                    });
                }
                if( view.$el.find('#mjob_order_id').length > 0){
                    view.$el.find('#mjob_order_id').val(this.orderModel.get('ID'));
                }
            },
            afterSave: function(result, resp, jqXHR, type){
                var view = this;
                console.log(type);
                if( type == 'update-billing-hiring-modal'){
                    view.orderModel.set('need_upload_remove', view.data_id);
                    this.orderModel.save('', '', {
                        beforeSend: function () {

                        },
                        success: function (result, res, xhr) {
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                view.closeModal();
                                view.target.addClass('disabled');
                                view.target.find('i').removeClass('fa-square-o');
                                view.target.find('i').addClass('fa-check-square-o');
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }

                        }
                    });
                }
            },
            setupFields: function(model){
                var view = this;
                view.$el.find('input.input-item,input[type="text"],input[type="hidden"], textarea,select').each(function() {
                    var $input = $(this);
                    if( $input.attr('name') != '_wpnonce' && $input.attr('name') != 'is_billing'  ) {
                        $input.val(model.get($input.attr('name')));
                    }
                });
            },
            selectBilling: function(event){
                event.preventDefault();
                $target = $(event.currentTarget);
                if( $target.val() == 'no' ){
                    $('.billing-order-address').show();
                }
                else{
                    $('.billing-order-address').hide();
                }
            },
            selectAccount: function(event){
                event.preventDefault();
                $target = $(event.currentTarget);
                if( $target.val() == 'no' ){
                    $('.account-holder').show();
                }
                else{
                    $('.account-holder').hide();
                }
            },

        });
        Views.ModaContactInfo = Views.Modal_Box.extend({
            el: '#contact_info_modal',
            events: {},
            initialize: function () {
                AE.pubsub.on('ae:form:submit:success', this.afterSave, this);
            },
            onOpen: function(model, $target, orderModel, data_id){
                var view = this;
                this.model = model;
                view.openModal();
                view.setupFields(this.model);
                view.orderModel = orderModel;
                view.target  = $target;
                view.data_id = data_id;
                this.model.set('_wpnonce', $('#profile_wpnonce').val());
                if(typeof this.profileFormModal === "undefined") {
                    this.profileFormModal = new Views.AE_Form({
                        el: '.form-confirm-info-modal', // Wrapper of form
                        model: this.model,
                        rules: {
                            first_name: 'required',
                            last_name: 'required',
                            phone: 'required',
                            business_email: 'required',
                            billing_full_address: 'required',
                            city: 'required',
                            state: 'required',
                            zip_code: 'required'
                        },
                        type: 'update-profile-contact-modal',
                        blockTarget: '.form-confirm-info-modal button'
                    });
                }
                if( view.$el.find('#mjob_order_id').length > 0){
                    view.$el.find('#mjob_order_id').val(this.orderModel.get('ID'));
                }
            },
            afterSave: function(result, resp, jqXHR, type){
                var view = this;
                if( type == 'update-profile-contact-modal'){
                    view.orderModel.set('need_upload_remove', view.data_id);
                    this.orderModel.save('', '', {
                        beforeSend: function () {

                        },
                        success: function (result, res, xhr) {
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                view.closeModal();
                                view.target.addClass('disabled');
                                view.target.find('i').removeClass('fa-square-o');
                                view.target.find('i').addClass('fa-check-square-o');
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }

                        }
                    });
                }
            },
            setupFields: function(model){
                var view = this;
                view.$el.find('input.input-item,input[type="text"],input[type="hidden"], textarea,select').each(function() {
                    var $input = $(this);
                    if( $input.attr('name') != '_wpnonce' ) {
                        $input.val(model.get($input.attr('name')));
                    }
                });
            }
        });
        Views.ModalUnlockRequirement = Views.Modal_Box.extend({
            el: '#unlock_requirement_modal',
            events: {
                'click .btn-ask-requirement': 'askRequirment'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function (model, data_id, $target, data_name) {
                var view = this;
                this.model = model;
                this.data_id = data_id;
                this.target = $target;
                this.arr_ids = [];
                this.data_name = data_name;
                view.openModal();
                //view.$el.find('.unlock-more').html(data_name);
            },
            askRequirment: function(e){
                var view = this;
                this.model.set('need_upload_add', this.data_id);
                this.model.set('document_name', this.data_name);
                $target = $(e.currentTarget);
                this.model.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block($target)
                    },
                    success: function (result, res, xhr) {
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            view.closeModal();
                            view.target.parent().addClass('disabled');
                            view.target.parent().find('.fa-check-square-o').addClass('fa-square-o');
                            view.target.parent().find('.fa-check-square-o').removeClass('fa-check-square-o');
                            view.target.find('i').remove();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                        view.blockUi.unblock();

                    }
                });
            }
        });
        Views.ModalWordComplete = Views.Modal_Box.extend({
            el: '#work_complete_modal',
            events: {
                'click .btn-work-complete-submit': 'workComplete'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function (model, target) {
                var view = this;
                this.model = model;
                view.openModal();
                if( target.attr('data-content') != '' ) {
                    $('.note-body').html(target.attr('data-content'));
                }
                $('#work_complete_date').datepicker();
            },
            workComplete: function(e){
                e.preventDefault();
                var view = this;
                $target = $(e.currentTarget);
                view.data ={
                    action: 'mjob-work-complete-confirm',
                    'order_id': view.model.get('ID')
                };
                if( $('#work_complete_date').length > 0 ){
                    view.data ={
                        action: 'mjob-work-complete-confirm',
                        'order_id': view.model.get('ID'),
                        'work_complete_date': $('#work_complete_date').val()
                    };
                }
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: view.data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            view.closeModal();
                            window.location.reload(true);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        Views.ModalReorder = Views.Modal_Box.extend({
            el: '#reorder_modal',
            events: {
                'submit #reoder-agreement': 'reOrder',
                'click .agreement-title-link': 'showModalAgreement',
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function (model) {
                var view = this;
                this.model = model;
                view.openModal();
            },
            reOrder: function(e){
                e.preventDefault();
                var view = this;
                $target = $(e.currentTarget);
                view.field_to_check = new Array();
                view.ageement_ids = new Array();
                $('#reorder_modal').find('input[type="checkbox"]').each( function(){
                    view.field_to_check[$(this).attr('name')] = "required"
                    view.ageement_ids.push($(this).attr('data-id'));
                });
                view.data1=  {
                    action: 'mjob-send-agreement-email',
                        aid: view.ageement_ids,
                        jid: view.model.get('mjob_id'),
                };
                view.data ={
                    action: 'mjob-reorder',
                    'order_id': view.model.get('ID'),
                    'jid': view.model.get('mjob_id'),
                    'aid': view.agreement_ids

                };
                view.initValidator($target, view.field_to_check);
                if($target.valid()){
                    $.ajax({
                        url: ae_globals.ajaxURL,
                        type: 'post',
                        data: view.data1,
                        beforeSend: function() {
                            view.blockUi.block($target);
                        },
                        success: function(res) {
                            view.blockUi.unblock();
                            if (res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                $.ajax({
                                    url: ae_globals.ajaxURL,
                                    type: 'post',
                                    data: view.data,
                                    beforeSend: function() {
                                        view.blockUi.block($target);
                                    },
                                    success: function(res) {
                                        view.blockUi.unblock();
                                        if (res.success) {
                                            AE.pubsub.trigger('ae:notification', {
                                                msg: res.msg,
                                                notice_type: 'success'
                                            });
                                            view.closeModal();
                                            window.location.reload(true);
                                        } else {
                                            AE.pubsub.trigger('ae:notification', {
                                                msg: res.msg,
                                                notice_type: 'error'
                                            });
                                        }
                                    }
                                });
                            } else {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            }
                        }
                    });

                }
            },
            initValidator: function($target_form, field_to_check){
                var view = this;
                var form_validator = $target_form.validate({
                    errorElement: "p",
                    rules: field_to_check,
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
                });
            },
            showModalAgreement: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                if( typeof this.agreementModal == 'undefined' ){
                    this.agreementModal = new Views.agreementModal();
                }
                var aid = $target.attr('data-id');
                var data = JSON.parse($('#agreement_data_'+aid).html());
                this.agreementModal.onOpen(data);
            },
        });
        Views.ModalDelivery = Views.Modal_Box.extend({
            el: '#delivery',
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                if( typeof this.model === 'undefined' ){
                    this.model = new Models.Delivery();
                }
                AE.pubsub.on('carousels:success:upload', this.enableButtons, this);
            },
            onOpen: function(parent){
                var view = this;
                this.model.set('post_type', 'order_delivery');
                view.model.set('post_parent', parent);
                view.openModal();
                view.setupFields();
            },
            setupFields: function(){
                var view = this;
                if (typeof view.carousels === 'undefined') {
                    view.carousels = new Views.Carousel({
                        el: $('.gallery_container'),
                        name_item:'et_carousel',
                        uploaderID:'carousel_deliver',
                        model: view.model,
                        extensions: ae_globals.file_types,
                        carouselTemplate: '#ae_carousel_file_template'

                    });
                }
                var deliveryForm = new Views.AE_Form({
                    el: '.delivery-order', // parent of form
                    model: view.model,
                    rules: {
                        post_content: 'required'
                    },
                    type: 'delivery',
                    blockTarget: '.delivery-order button'
                });

            },
            enableButtons: function(up, file, res){
                var view = this;
                console.log( view.$el.find('button').length);
                view.$el.find('button').removeAttr('disabled');
            }
        });
        Views.ModalReview = Views.Modal_Box.extend({
            el: '#modal_review',
            events: {
                'click .btn-skip': 'skipReview',
                'submit .review-form': 'review'
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function(model){
                var view = this;
                view.model = model;
                view.openModal();
                view.setupFields();
            },
            setupFields: function(){
                var view = this;
                $('.rating-it').raty({
                    half: true,
                    hints: raty.hint
                });
            },
            skipReview: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var view = this;
                view.saveModel(view.model, $target);
            },
            review: function(e){
                e.preventDefault();
                var view = this,
                    $target = $(e.currentTarget);
                if( view.$el.find('input[name="score"]').length > 0  && view.$el.find('input[name="score"]').val() != '') {
                    var score = view.$el.find('input[name="score"]').val();
                }
                else{
                    var score = 0;
                }
                var message = view.$el.find('textarea[name="comment_content"]').val();
                var data = {
                    action: 'mjob-user-review',
                    score: score,
                    comment_content: message,
                    order_id: view.model.get('ID')

                }
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            view.saveModel(view.model, $target, false);
                            //setTimeout(function() {
                            //    window.location.reload();
                            //}, 2000);
                            if (typeof view.modalreorder === 'undefined') {
                               view.modalreorder = new Views.ModalReorder();
                            }
                            view.modalreorder.onOpen(view.model);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            saveModel: function (model, $target, s) {
                var view = this;
                model.set('finished', 1);
                model.set('noSetupPayment', false);
                model.save('finished', '1', {
                    beforeSend: function () {
                        view.blockUi.block($target)
                    },
                    success: function (result, res, xhr) {
                        if (res.success) {
                            if( s != false ) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'success'
                                });
                                if (typeof this.modalreorder === 'undefined') {
                                    this.modalreorder = new Views.ModalReorder();
                                }
                                this.modalreorder.onOpen(view.model);
                            }
                            view.closeModal();
                            $('.order_status').html(res.data.status_text);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                        view.blockUi.unblock();

                    }
                });
            }
        });
        new Views.SingleOrder();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);