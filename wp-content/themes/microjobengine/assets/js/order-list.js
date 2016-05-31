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
                'click .order-delivery-btn': 'openModalDelivery',
                'click .mjob-dispute-order': 'showFormDispute',
                'click .requirement-item': 'showModalRequirement'
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
            showModalRequirement: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var data_id = $target.attr('data-id');
                if( typeof this.modalrequirement === 'undefined' ) {
                    this.modalrequirement = new Views.ModalRequirement();
                }
                this.modalrequirement.onOpen(this.model, data_id);
            }
        });
        Views.ModalRequirement = Views.Modal_Box.extend({
            el: '#requirement_modal',
            events: {
                'click .btn-save': 'saveOrderRequirment'
            },
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                AE.pubsub.on('carousels:success:upload', this.showListFile, this);
                AE.pubsub.on('ae:carousel:after:remove', this.removeAll, this);
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function(model, data_id){
                var view = this;
                this.model = model;
                this.data_id = data_id;
                this.arr_ids = new Array();
                view.openModal();
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
                view.$el.find('.btn-save').attr('disabled', false);
                view.arr_ids.push(res.data.attach_id);
            },
            saveOrderRequirment: function(e){
                e.preventDefault();
                var view = this;
                if( this.model.get('requirement_files') != '' ) {
                    var arr_files =  this.model.get('requirement_files');
                }
                else {
                    var arr_files = new Array();
                }
                arr_files[this.data_id] = view.arr_ids;
                this.model.set('requirement_files', arr_files);
                //this.model.save('', '', {
                //    beforeSend: function () {
                //        view.blockUi.block($target)
                //    },
                //    success: function (result, res, xhr) {
                //        if (res.success) {
                //                AE.pubsub.trigger('ae:notification', {
                //                    msg: res.msg,
                //                    notice_type: 'success'
                //                });
                //            view.closeModal();
                //        } else {
                //            AE.pubsub.trigger('ae:notification', {
                //                msg: res.msg,
                //                notice_type: 'error'
                //            });
                //        }
                //        view.blockUi.unblock();
                //
                //    }
                //});
            },
            removeAll: function(model){
                var view = this;
                if( view.$el.find('.requirement-image-list .image-item').length <= 0){
                    view.$el.find('.btn-save').attr('disabled', true);
                }
            }
        });
        Views.ModalDelivery = Views.Modal_Box.extend({
            el: '#delivery',
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                if( typeof this.model === 'undefined' ){
                    this.model = new Models.Delivery();
                }
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
                        uploaderID:'carousel',
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
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
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