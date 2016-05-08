/**
 * Created by Jack Bui on 1/12/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {
        /**
         * MODELS
         */

        /*Show tooltip*/
        $('[data-toggle="tooltip"]').tooltip();

        /*Show white space two line*/
        $(".name-job").dotdotdot()


        $(".wrapper-list-conversation").mCustomScrollbar({
            theme:"minimal",
            callbacks:{
                onInit:function(){
                    $('#mCSB_1_container').css({
                        top: "-100%"
                    })
                }
            }
        });
        /*Validation input text time*/
        $('.time-delivery').keypress(function(event) {
            var key = window.event ? event.keyCode : event.which;

            if (event.keyCode === 8 || event.keyCode === 46
                || event.keyCode === 37 || event.keyCode === 39) {
                return true;
            }
            else if ( key < 48 || key > 57 ) {
                return false;
            }
            else return true;
        });

        /*Focus searh form*/
        $(".new-search-link").click(function() {
            $("#input-search").select();
            $('html, body').animate({
                scrollTop: $("html, body").offset().top
            }, 1000);
        });

        /*Scroll link statistic job home*/
        $(".link-last-job").click(function() {
            $('html, body').animate({
                scrollTop: $(".block-items").offset().top
            }, 1000);
        });

        $(".et-pull-top ,#content, .slider").click(function() {
            $('.navbar-collapse').removeClass('in');
            $('.navbar-collapse').addClass('collapsed');
        });
        /*Hover dropdown menu*/
        var windowSize = $(window).width();
        if(windowSize > 992){
            $('#et-nav .dropdown').hover(function(){
                $('.dropdown-toggle', this).trigger('click');
            });
        }
        /*Scroll header home*/
        $(window).on('scroll',function(){
            //scroll header
            var form_search = $(".form-search");
            if(form_search.length){
                var form_search_top = form_search.offset().top;
                if($(window).scrollTop() > form_search_top){
                    $('.search-bar').css("display", "inline-block");
                }else{
                    $('.search-bar').css("display", "none");
                }
            }
            /*Scroll header detail job*/
            var title_detail_job = $(".title-detail-job");
            var btn_order_aside_bar = jQuery(".action");
            var wpadminbar = $("#wpadminbar");
            if(title_detail_job.length){
                var title_detail_job_top = title_detail_job.offset().top;
                if($(window).scrollTop() > title_detail_job_top){
                    $('#logo-site, .search-bar, #myAccount, .navbar-default').css("display", "none");
                    $('.mjob-title').addClass('title-fixed-scroll');
                    if(wpadminbar.length > 0){
                        $('.title-fixed-scroll').css("margin-top", "30px");
                    }
                }else{
                    $('#logo-site, .search-bar, #myAccount, .navbar-default').css("display", "inline-block");
                    $('.mjob-title').removeClass('title-fixed-scroll');
                }
            }
            if(btn_order_aside_bar.length){
                var btn_order_aside_bar_top = btn_order_aside_bar.offset().top;
                if($(window).scrollTop() > btn_order_aside_bar_top){
                    $('.btn-order-aside-bar').addClass("btn-order-aside-bar-top");
                    if(wpadminbar.length > 0){
                        $('.btn-order-aside-bar-top').css('cssText',"top: 20px !important");
                    }
                }else{
                    $('.btn-order-aside-bar').removeClass("btn-order-aside-bar-top");
                }
            }
        });
        /*Check show arrows if two image*/
        var count = $(".carousel-indicators li").length;
        if(count <= 1){
            $('.carousel-indicators').hide();
            $('.carousel-control').hide();
        }else{
            $('.carousel-indicators').show();
            $('.carousel-control').show();
        }



        /*Animation scroll*/
        wow = new WOW(
            {
                animateClass: 'animated',
                offset:       100,
            }
        );
        wow.init();


        Models.mJobUser = Backbone.Model.extend({
            action: 'mjob_sync_user'
        });
        Models.mJobProfile = Backbone.Model.extend({
            action: 'mjob_sync_profile',
            initialize: function () {
            }
        });
        /**
         * model mjob
         */
        /*Fixed height banner home*/
         var header = $('.et-pull-bottom').outerHeight();
         var wpadminbar = $('#wpadminbar').outerHeight();
        jQuery('.slider').css({
            height: (jQuery(window).height() - 85 - header - wpadminbar) + 'px'

        });
        /*Accordion menu*/
        var Accordion = function(el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;

            var links = this.el.find('.link');
            // Evento
            links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
        }

        Accordion.prototype.dropdown = function(e) {
            var $el = e.data.el;
            $this = $(this),
                $next = $this.next();

            $next.slideToggle();
            $this.parent().toggleClass('open');

            if (!e.data.multiple) {
                $el.find('.submenu').not($next).slideUp().parent().removeClass('open');
            };
        }
        var accordion = new Accordion($('#accordion'), false);


        Models.Mjob = Backbone.Model.extend({
            action: 'ae-mjob_post-sync',
            initialize: function() {}
        });
        /**
         * mjob collections
         */
        Collections.Mjob = Backbone.Collection.extend({
            model: Models.Mjob,
            action: 'ae-fetch-mjob_post',
            initialize: function() {
                this.paged = 1;
            }
        });
        /**
         * define mjob item view
         */
        var mjobItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'col-lg-4 col-md-4 col-sm-4 col-xs-12 mjob-item',
            template: _.template($('#mjob-item-loop').html()),
            initialize: function() {
                this.renderRating();
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                $("body").tooltip({ selector: '[data-toggle=tooltip]' });
                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
                this.renderRating();
            },
            // Render rating star
            renderRating: function() {
                var view = this;
                this.$el.find('.rate-it').raty({
                    readOnly: true,
                    half: true,
                    score: function() {
                        return view.model.get('rating_score');
                    },
                    hints: raty.hint
                });
            }
        });
        /**
         * list view control mjob list
         */
        ListMjobs = Views.ListPost.extend({
            tagName: 'ul',
            itemView: mjobItem,
            itemClass: 'mjob-item'
        });
        $('.mjob-container-control').each(function() {
            if (typeof mjobCollection == 'undefined') {
                //Get public  collection
                if ($('.mJob_postdata').length > 0) {
                    var mjob = JSON.parse($('.mJob_postdata').html());
                    mjobCollection = new Collections.Mjob(mjob);
                } else {
                    mjobCollection = new Collections.Mjob();
                }
            }
            var skills = new Collections.Skills();
            /**
             * init list blog view
             */
            new ListMjobs({
                itemView: mjobItem,
                collection: mjobCollection,
                el: $(this).find('.list-mjobs')
            });
            //post-type-archive-project
            //old block-projects
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                events: function() {
                    return _.defaults({
                        'click .custom-filter-query a': 'customFilter'
                    }, Views.BlockControl.prototype.events);
                },
                thumbnail: "medium_post_thumbnail",
                collection: mjobCollection,
                skills: skills,
                el: $(this),
                // Categories accorder
                customFilter: function(event) {
                    event.preventDefault();
                    var view = this;
                    this.customFilter = true;
                    var $target = $(event.currentTarget),
                        name = $target.attr('data-name'),
                        value = $target.attr('data-value'),
                        liveUpdateEl = view.$el.find('.block-title .block-title-text');

                    // Add class active
                    $('.custom-filter-query a').removeClass('active');
                    $target.addClass('active');

                    // Add class active for parents
                    $('#accordion li').removeClass('active');
                    $target.parents('.open').addClass('active');

                    if (name !== 'undefined') {
                        //view.router.navigate($target.attr('href'));
                        window.history.pushState('', '', $target.attr('href'));

                        // update title
                        //liveUpdateEl.text($target.text());

                        view.query[name] = value;
                        view.page = 1;
                        // fetch page
                        view.fetch($target);
                    }
                    return false;
                },
                onAfterFetch: function(result, res, $target){
                    var view = this;
                    // Update block title if is custom filter category and skill
                    var liveUpdateEl = view.$el.find('.block-title .block-title-text');
                    if(view.customFilter == true) {
                        var prefix = liveUpdateEl.attr('data-prefix');
                        liveUpdateEl.find('.search-result-count').text(res.total);
                        liveUpdateEl.find('.term-name').text(prefix + " " + $target.text());
                        this.customFilter = false;
                    }
                    if(res.success ){
                        if(ae_globals.is_search || ae_globals.is_tax_mjob_category || ae_globals.is_tax_skill) {
                            liveUpdateEl.find('.search-result-count').text(res.total);
                        }
                        if( $('.not-found').length > 0){
                            $('.not-found').remove();
                        }
                       if( res.data.length == 0 ){
                           if($('.my-list-mjobs').length > 0) {
                               $('.list-mjobs').html(ae_globals.no_mjobs);
                           } else {
                               $('.list-mjobs').html(ae_globals.no_services);
                           }
                       }

                    } else{
                        if(ae_globals.is_search || ae_globals.is_tax_mjob_category || ae_globals.is_tax_skill) {
                            liveUpdateEl.find('.search-result-count').text(0);
                        }
                        if($('.my-list-mjobs').length > 0) {
                            $('.list-mjobs').html(ae_globals.no_mjobs);
                        } else {
                            $('.list-mjobs').html(ae_globals.no_services);
                        }
                    }
                }
            });
        });
        /*
         * Model extra
         */
        Models.Extra = Backbone.Model.extend({
            action:"ae-mjob_extra-sync",
            defaults:{
                post_type:'mjob_extra'
            }
        });
        /**
         * Extras list in single mjob page
         *
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author JACK BUI
         */
        Collections.Extras = Backbone.Collection.extend({
            model: Models.Extra,
            action: 'ae-fetch-mjob_extra',
            initialize: function () {
                this.paged = 1;
            }
        });
        /*
         * model order
         */
        Models.Order = Backbone.Model.extend({
            action:"ae-mjob_order-sync",
            defaults:{
                post_type:'mjob_order'
            }
        });
        /*
         * Extra item
         */
        Views.ExtraItemView =Views.PostItem.extend({
            tagName: 'div',
            className : 'form-group row clearfix job-items extra-item',
            template: _.template($('#mjobExtraItem').html()),
            events: _.extend({
                'click .mjob-remove-extra-item': 'deleteItem'
            }, Views.PostItem.prototype.events),
            initialize: function ( options ) {
                _.extend( this, options );
                Views.PostItem.prototype.initialize.call(this, options);
            },
            onItemRendered: function(){
                var view = this;
                view.$el.find('input[name="et_budget"]').attr('id', 'et_budget_'+this.model._listenId);
                view.$el.find('input[name="post_title"]').attr('id', 'et_extra_'+this.model._listenId);
            },
            deleteItem:function(e){
                var view = this;
                e.preventDefault();
                this.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                this.model.destroy();
            },
            syncChange:function(){
                var view = this;
                view.$el.find('input,textarea,select').each(function() {
                    view.model.set($(this).attr('name'), $(this).val());
                });
                var is_changed = true;
                is_changed = is_changed || this.model.hasChanged();
                if(is_changed) {
                    this.model.save( '', '', {
                        beforeSend: function () {

                        },
                        success: function ( result, res, jqXHR ) {
                            if ( res.success ) {
                                AE.pubsub.trigger('mjob:after:sync:extra', result, res, jqXHR);
                            }
                        }
                    } );
                }

            },
        });
        /**
         * list extras
         */
        Views.ExtrasListView = Backbone.Marionette.CollectionView.extend( {
            tagName: 'div',
            itemView: Views.ExtraItemView,
            itemClass: 'extra-item',
            syncChange:function(){
                var view = this;
                this.children.each(function(child){
                    child.syncChange();
                });
            }
        } );
        //blog code
        /**
         * define blog item view
         */
        BlogItem = Views.PostItem.extend({
            tagName: 'div',
            className: 'blog-wrapper post-item',
            template: _.template($('#ae-post-loop').html()),
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                // after render view
            }
        });
        /**
         * list view control blog list
         */
        ListBlogs = Views.ListPost.extend({
            tagName: 'div',
            itemView: BlogItem,
            itemClass: 'post-item'
        });
        // blog list control
        if ($('#posts_control').length > 0) {
            if ($('#posts_control').find('.postdata').length > 0) {
                var postsdata = JSON.parse($('#posts_control').find('.postdata').html()),
                    posts = new Collections.Blogs(postsdata);
            } else {
                posts = new Collections.Blogs();
            }
            /**
             * init list blog view
             */
            new ListBlogs({
                itemView: BlogItem,
                collection: posts,
                el: $('#posts_control').find('.post-list')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: posts,
                el: $('#posts_control')
            });
        }
        Views.AE_Form = Backbone.View.extend({
            events: {
                'submit form': 'submitForm'
            },
            initialize: function(options){
                this.options = _.extend(this, options);
                this.blockUi = new Views.BlockUi();
                if( typeof this.type === 'undefined' ){
                    this.type ='aeForm';
                }

                if(typeof this.showNotice === "undefined") {
                    this.showNotice = true;
                }
            },
            resetModel: function(model) {
                this.model = model;
            },
            submitForm: function (event) {
                event.preventDefault();
                var view = this,
                    temp = new Array(),
                    $target = $(event.currentTarget);
                view.initValidate();
                var tempModel = view.model;
                /**
                 * update model from input, textarea, select
                 */
                view.$el.find('input,textarea,select').each(function() {
                    tempModel.set($(this).attr('name'), $(this).val());
                });

                view.$el.find('input[type=checkbox]').each(function() {
                    var name = $(this).attr('name');
                    tempModel.set(name, []);
                });
                /**
                 * update input check box to model
                 */
                view.$el.find('input[type=checkbox]:checked').each(function() {
                    var name = $(this).attr('name');
                    if (typeof temp[name] !== 'object') {
                        temp[name] = new Array();
                    }
                    temp[name].push($(this).val());
                    tempModel.set(name, temp[name]);
                });
                /**
                 * update input radio to model
                 */
                view.$el.find('input[type=radio]:checked').each(function() {
                    tempModel.set($(this).attr('name'), $(this).val());
                });
                if( typeof view.fields != 'undefined' && view.fields.length > 0 ) {
                    for (i = 0; i < view.fields.length; i++) {
                        if (typeof tempModel.get(view.fields[i]) !== 'undefined') {
                            view.model.set(view.fields[i], tempModel.get(view.fields[i]));
                        }
                    }
                }
                else{
                    view.model = tempModel;
                }
                if( view.form_validator.form() ){
                    var response_option = {
                        beforeSend: function () {
                            if("undefined" !== typeof view.blockTarget && "" != view.blockTarget) {
                                view.blockUi.block($(view.blockTarget));
                            } else {
                                view.blockUi.block($target);
                            }
                        },
                        success: function (result, resp, jqXHR) {
                            AE.pubsub.trigger('ae:form:submit:success', result, resp, jqXHR, view.type);
                            if(view.showNotice == true) {
                                if( resp.success ) {
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: resp.msg,
                                        notice_type: 'success'
                                    });
                                    view.data = resp.data;
                                }
                                else{
                                    AE.pubsub.trigger('ae:notification', {
                                        msg: resp.msg,
                                        notice_type: 'error'
                                    });
                                }
                            }
                            view.blockUi.unblock();
                        }
                    };
                    if(view.model.get('method') == 'read') {
                        view.model.request('read', response_option)
                    } else {
                        view.model.save('', '', response_option);
                    }
                }
            },
            initValidate: function(){
                var view = this;
                view.form_validator = view.$el.find('form').validate({
                    errorElement: "p",
                    rules: view.rules,
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
        })
        /*
         *
         * F R O N T  V I E W S
         *
         */
        Views.Front = Backbone.View.extend({
            el: 'body',
            model: [],
            events: {

            },
            initialize: function (options) {
                this.$('.chosen-single').chosen({
                    width: '100%',
                    max_selected_options: 1
                });
                this.blockUi = new Views.BlockUi();
                this.$('.multi-tax-item').chosen({
                    width: '100%',
                    // max_selected_options: parseInt(ae_globals.max_cat),
                    inherit_select_classes: true
                });
                // Trigger show notification
                AE.pubsub.on('ae:notification', this.showNotice, this);
                // Trigger live update user info
                AE.pubsub.on('mjob:update:user', this.updateUser, this);
                // Notice template
                this.noti_templates = new _.template('<div class="notification autohide {{= type }}-bg">' + '<div class="main-center">' + '{{= msg }}' + '</div>' + '</div>');
                //catch action reject project
                AE.pubsub.on('ae:model:onReject', this.rejectPost, this);
                AE.pubsub.on("ae:delete:success", this.afterDelete, this);
                // Rating score
                $('.rate-it').each(function() {
                    $('.rate-it').raty({
                        readOnly: true,
                        half: true,
                        score: function () {
                            return $(this).attr('data-score');
                        },
                        hints: raty.hint
                    });
                });
                AE.pubsub.on('ae:model:onpause', this.pauseMjob, this);
                AE.pubsub.on('ae:model:onunpause', this.unPauseMjob, this);
                AE.pubsub.on('ae:openRejectModal', this.clearContent, this);
            },
            afterDelete: function(result, res, xhr){
                AE.pubsub.trigger('ae:notification', {
                    msg: res.msg,
                    notice_type: 'success'
                });
            },
            showNotice: function(params) {
                var view = this;
                toastr.options = {
                    closeButton: true,
                    showMethod: 'fadeIn',
                    newestOnTop: true,
                    timeOut: 4000,
                };
                if(params.notice_type == 'success') {
                    toastr.success(params.msg);
                } else {
                    toastr.error(params.msg);
                }
            },
            updateUser: function(params) {
                var accountHeader = $('#mjob_my_account_header');
                var myAccount = $('#myAccount');
                if(accountHeader.length > 0) {
                    var template = _.template(accountHeader.html());
                    myAccount.html(template({
                        avatar: '<img src="'+ params.avatar +'" />',
                        display_name: params.display_name
                    }));
                }
            },
            /**
             * setup reject post modal and trigger event open modal reject
             */
            rejectPost: function(model) {
                if (typeof this.rejectModal === 'undefined') {
                    this.rejectModal = new Views.RejectPostModal({
                        el: $('#reject_post'),
                        target: $('.mjob-button-reject'),
                    });
                }
                this.rejectModal.onReject(model);
            },
            pauseMjob: function(model, target){
                var view = this;
                model.set('pause', 1);
                model.save('pause', '1', {
                    beforeSend: function() {
                        view.blockUi.block(target)
                    },
                    success: function(result, res, xhr) {
                        if( res.success ) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                        }else{
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                        view.blockUi.unblock();

                    }
                });
            },
            clearContent: function(model){
                var view = this;
                view.$el.find('textarea[name="reject_message"]').val('');
            },
            unPauseMjob: function(model, target){
                var view = this;
                if( typeof model.get('pause') !== 'undefined' ){
                    model.unset('pause');
                }
                model.set('unpause', 1);
                model.save('unpause', '1', {
                    beforeSend: function() {
                        view.blockUi.block(target)
                    },
                    success: function(result, res, xhr) {
                        if( res.success ) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            model.unset('unpause');
                        }else{
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                        view.blockUi.unblock();

                    }
                });
            },
            number_format: function(number, decimals, dec_point, thousands_sep) {
            number = (number + '')
                .replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + (Math.round(n * k) / k)
                            .toFixed(prec);
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
                .split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '')
                    .length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1)
                    .join('0');
            }
            return s.join(dec);
        },
            mJobPriceFormat: function( amount, style ){
                var amount_text = this.number_format(amount, ae_globals.decimal, ae_globals.decimal_point, ae_globals.thousand_sep);
                switch (style) {
                    case 'sup':
                        format = '<sup>'+ae_globals.mjob_currency.icon+'</sup>';
                        break;

                    case 'sub':
                        format = '<sub>'+ae_globals.mjob_currency.icon+'</sub>';
                        break;

                    default:
                        format = ae_globals.mjob_currency.icon;
                        break;
                }
                align = 0;
                if( typeof ae_globals.mjob_currency !== 'undefined' ) {
                    var align = parseInt(ae_globals.mjob_currency.align);
                }
                if(align) {
                    var price       =   format + amount_text;
                }else {
                    var price       =   amount_text + format;
                }
                return price;
            }
        });
        Views.ProcessHiring = Backbone.View.extend({
            el: '.page-template-page-process-hiring',
            events: {
                'change select[name="use_billing_address"]': 'selectBilling',
                'change select[name="use_holder_account"]': 'selectAccount',
                'click .agreement-title-link': 'showModalAgreement'

            },
            initialize: function () {
                this.blockUi = new Views.BlockUi();
                this.initProcessHiring();
                AE.pubsub.on('ae:form:submit:success', this.afterSave, this);

            },
            initProcessHiring: function(){
                if($('#mjob_profile_data').length > 0) {
                    data = JSON.parse($('#mjob_profile_data').html());
                    this.profilemodel = new Models.mJobProfile(data);
                }
                else {
                    this.profilemodel = new Models.mJobProfile();
                    if($('#current_user').length > 0 && ae_globals.user_ID != 0) {
                        this.profilemodel.set({
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
                            credit_goal: ''
                        })
                    }
                }
                // Set nonce for security purpose
                this.profilemodel.set('_wpnonce', $('#profile_wpnonce').val());
                if(typeof this.profileForm === "undefined") {
                    this.profileForm = new Views.AE_Form({
                        el: '.form-confirm-info', // Wrapper of form
                        model: this.profilemodel,
                        rules: {
                        },
                        type: 'update-profile-hiring',
                        blockTarget: '.form-confirm-info button'
                    });
                }
                if(typeof this.billingForm === "undefined") {
                    this.bilingForm = new Views.AE_Form({
                        el: '.form-confirm-billing', // Wrapper of for
                        model: this.profilemodel,
                        rules: {
                        },
                        type: 'update-billing-hiring',
                        blockTarget: '.form-confirm-billing button'
                    });
                }
            },
            afterSave: function(result, resp, jqXHR, type){
                var view = this;
                if( resp.success ) {
                    if (type == 'update-profile-hiring') {
                        view.showStep2();
                    }
                    else if(type == 'update-billing-hiring'){
                        view.showStep3();
                    }
                }
            },
            showStep2: function(){
                $('.page-template-page-process-hiring .block-title').html(ae_globals.process_hiring_step2);
                $('.form-confirm-billing').show();
                $('.form-confirm-info').hide();
                var use_billing_address = this.profilemodel.get('use_billing_address');
                var use_holder_account = this.profilemodel.get('use_holder_account');
                if( use_billing_address != '' ) {
                    $('select[name="use_billing_address"]').val(this.profilemodel.get('use_billing_address'));
                }
                if( use_holder_account != '' ) {
                    $('select[name="use_holder_account"]').val(this.profilemodel.get('use_holder_account'));
                }
                if( this.profilemodel.get('use_billing_address') == 'no' ){
                    $('.billing-order-address').show();
                }
                if( this.profilemodel.get('use_holder_account') == 'no' ){
                    $('.account-holder').show();
                }
                this.showStepTwo();

            },
            showStep3: function(){
                var view = this;
                $('.page-template-page-process-hiring .block-title').html(ae_globals.process_hiring_step3);
                $('.form-sign-agreement').show();
                $('.form-confirm-billing').hide();
                view.wrapper = document.getElementById("signature-form");
                    view.clearButton = view.wrapper.querySelector("[data-action=clear]");
                    view.saveButton = view.wrapper.querySelector("[data-action=save]");
                    view.canvas = view.wrapper.querySelector("canvas");
                    view.signaturePad;
                window.onresize = view.resizeCanvas;
                view.resizeCanvas(view.canvas);
                view.signaturePad = new SignaturePad(view.canvas);
                view.signaturePad.fromDataURL(this.profilemodel.get('signature'));
                view.clearButton.addEventListener("click", function (event) {
                    view.signaturePad.clear();
                });
                view.target_form = $('#signature-form');
                view.field_to_check = new Array();
                view.ageement_ids = new Array();
                $('#signature-form').find('input[type="checkbox"]').each( function(){
                    view.field_to_check[$(this).attr('name')] = "required"
                    view.ageement_ids.push($(this).attr('data-id'));
                });
                view.initValidator(view.target_form, view.field_to_check);
                view.saveButton.addEventListener("click", function (event) {
                    event.preventDefault();
                    if( view.target_form.valid() ){
                        if (view.signaturePad.isEmpty()) {
                            alert("Please provide signature first.");
                        } else {
                            //view.reMoveBlank(view.canvas );
                            view.profilemodel.set('signature', view.signaturePad.toDataURL());
                            view.profilemodel.save('', '', {
                                beforeSend: function () {
                                    view.blockUi.block(view.saveButton);
                                },
                                success: function (result, res, jqXHR) {
                                    if (res.success) {
                                        $.ajax({
                                            type: 'POST',
                                            url: ae_globals.ajaxURL,
                                            data: {
                                                action: 'mjob-send-agreement-email',
                                                aid: view.ageement_ids
                                            },
                                            beforeSend: function() {

                                            },
                                            success: function(resp, status, jqXHR) {

                                            }
                                        })
                                        AE.pubsub.trigger('ae:notification', {
                                            msg: res.msg,
                                            notice_type: 'success'
                                        });
                                        view.blockUi.unblock();
                                    }
                                }
                            });
                        }
                    }
                });
                this.showStepThree();

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
            resizeCanvas: function(canvas) {
                // When zoomed out to less than 100%, for some very strange reason,
                // some browsers report devicePixelRatio as less than 1
                // and only part of the canvas is cleared then.
                var ratio =  Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            },
            reMoveBlank: function (canvas) {
            var imgWidth = canvas.width;
            var imgHeight = canvas.height;
                this._ctx = canvas.getContext('2d');
            var imageData = this._ctx.getImageData(0, 0, imgWidth, imgHeight),
                data = imageData.data,
                getAlpha = function(x, y) {
                    return data[(imgWidth*y + x) * 4 + 3]
                },
                scanY = function (fromTop) {
                    var offset = fromTop ? 1 : -1;

                    // loop through each row
                    for(var y = fromTop ? 0 : imgHeight - 1; fromTop ? (y < imgHeight) : (y > -1); y += offset) {

                        // loop through each column
                        for(var x = 0; x < imgWidth; x++) {
                            if (getAlpha(x, y)) {
                                return y;
                            }
                        }
                    }
                    return null; // all image is white
                },
                scanX = function (fromLeft) {
                    var offset = fromLeft? 1 : -1;

                    // loop through each column
                    for(var x = fromLeft ? 0 : imgWidth - 1; fromLeft ? (x < imgWidth) : (x > -1); x += offset) {

                        // loop through each row
                        for(var y = 0; y < imgHeight; y++) {
                            if (getAlpha(x, y)) {
                                return x;
                            }
                        }
                    }
                    return null; // all image is white
                };

            var cropTop = scanY(true),
                cropBottom = scanY(false),
                cropLeft = scanX(true),
                cropRight = scanX(false);

            var relevantData = this._ctx.getImageData(cropLeft, cropTop, cropRight-cropLeft, cropBottom-cropTop);
                canvas.width = cropRight-cropLeft;
            canvas.height = cropBottom-cropTop;
            this._ctx.clearRect(0, 0, cropRight-cropLeft, cropBottom-cropTop);
            this._ctx.putImageData(relevantData, 0, 0);
        },
            showStepOne: function(){
                $('.post-service-step-1').addClass('active');
                $('.post-service-step-1').removeClass('done');
                $('.post-service-step-2').removeClass('active');
                $('.progress-bar-success').removeClass('half');
                $('.progress-bar-success').removeClass('full');
            },
            showStepTwo: function() {
                $('.post-service-step-1').removeClass('active');
                $('.post-service-step-1').addClass('done');
                $('.post-service-step-2').addClass('active');
                $('.post-service-step-3').removeClass('active');
                $('.progress-bar-success').addClass('half');
                $('.progress-bar-success').removeClass('full');
            },
            showStepThree: function() {
                $('.post-service-step-1').addClass('done');
                $('.post-service-step-2').removeClass('active');
                $('.post-service-step-2').addClass('done');
                $('.post-service-step-3').addClass('active');
                $('.progress-bar-success').removeClass('half');
                $('.progress-bar-success').addClass('full');
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
        Views.agreementModal = Views.Modal_Box.extend({
            el: '#agreement_modal',
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
            },
            onOpen: function(data){
                var view = this;
                view.openModal();
                view.setupFields(data);
            },
            setupFields: function(data){
                var view = this;
                view.data = data;
                $('#agreement_modal_title').html(view.data.post_title);
                $('.agreement_modal_content').html(view.data.post_content);
                //var gdata = {
                //    id: view.data.ID,
                //    jid: view.$el.find('#mjob_id').val(),
                //    action: 'mjob-get-agreement-info'
                //}
                //if( $('.signature-img').length > 0 ) {
                //    $.ajax({
                //        url: ae_globals.ajaxURL,
                //        type: 'post',
                //        data: gdata,
                //        beforeSend: function () {
                //        },
                //        success: function (res) {
                //            if (res.success) {
                //                $('.signature-img').attr('src', res.data);
                //            }
                //        }
                //    });
                //}

            }
        });
        new Views.ProcessHiring();
        // Serialize object
        jQuery.fn.serializeObject = function(){
            var self = this,
                json = {},
                push_counters = {},
                patterns = {
                    "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                    "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
                    "push":     /^$/,
                    "fixed":    /^\d+$/,
                    "named":    /^[a-zA-Z0-9_]+$/
                };

            this.build = function(base, key, value){
                base[key] = value;
                return base;
            };

            this.push_counter = function(key){
                if(push_counters[key] === undefined){
                    push_counters[key] = 0;
                }
                return push_counters[key]++;
            };

            jQuery.each(jQuery(this).serializeArray(), function(){
                // skip invalid keys
                if(!patterns.validate.test(this.name)){
                    return;
                }
                var k,
                    keys = this.name.match(patterns.key),
                    merge = this.value,
                    reverse_key = this.name;

                while((k = keys.pop()) !== undefined){
                    // adjust reverse_key
                    reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
                    // push
                    if(k.match(patterns.push)){
                        merge = self.build([], self.push_counter(reverse_key), merge);
                    }
                    // fixed
                    else if(k.match(patterns.fixed)){
                        merge = self.build([], k, merge);
                    }
                    // named
                    else if(k.match(patterns.named)){
                        merge = self.build({}, k, merge);
                    }
                }
                json = jQuery.extend(true, json, merge);
            });
            return json;
        };
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);

