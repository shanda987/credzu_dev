/**
 * Created by Jack Bui on 1/12/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {
        /*
         * Post service view
         */
        Views.PostSevice = Views.SubmitPost.extend({
            events: function() {
                return _.defaults({
                    'click .mjob-img-wrapper' : 'showPreviewImage',
                    'click .mjob-replace-image': 'replaceImage',
                    'click .mjob-delete-image': 'deleteImage',
                    'click .mjob-add-extra-btn': 'addExtras',
                    'click .done': 'selectStep'
                }, Views.SubmitPost.prototype.events);
            },
            initialize: function(){
                this.blockFormTarget = ".post-job .btn-save";
                Views.SubmitPost.prototype.initialize.apply(this, arguments);
                AE.pubsub.on('Upload:Success', this.uploadSuccess, this);
                AE.pubsub.on('ae:form:submit:success', this.authSuccess, this);
                AE.pubsub.on('ae:carousel:after:remove', this.checkImage, this);
                AE.pubsub.on('ae:user:auth', this.handleAuth, this);
                AE.pubsub.on('ae:after:setup:carousels', this.setupCarousels, this);
                AE.pubsub.on('mjob:after:sync:extra', this.redirectPage, this);
                // Process bar
                this.liStepOne = $('.post-service-step-1');
                this.liStepTwo = $('.post-service-step-2');
                this.liStepThree = $('.post-service-step-3');
                this.progressBarSuccess = $('.progress-bar-success');

                this.arrFinish = [];

                // Init extra
                this.initExtras();
            },
            onAfterSelectPlan: function($step, $li) {
                //this.showStepTwo();
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
            setupStep3: function(model){
                var view = this;
                view.$el.find('.pm-mjob-img img').attr('src', model.get('the_post_thumbnail'));
                view.$el.find('.pm-mjob-img').attr('href', model.get('permalink'));
                view.$el.find('.pm-mjob-title').attr('href', model.get('permalink'));
                view.$el.find('.pm-mjob-title').html(model.get('post_title'));
                view.$el.find('.pm-by-author').html(model.get('author_name'));
                view.$el.find('.pm-price-text').html(model.get('et_budget_text'));
                view.$el.find('.rate-it').raty({
                    readOnly: true,
                    half: true,
                    score: function() {
                        return model.get('rating_score');
                    },
                    hints: raty.hint
                });
                view.$el.find('.pm-pack-description').html(model.get('plan_content'));
                view.$el.find('.pm-pack-price-text').html(model.get('plan_price_text'));
                view.$el.find('.pm-pack-price-total').html(model.get('plan_price_text'));
                view.$el.find('input[name="amount"]').html(model.get('plan_price'));
                //define extra item
                var extraItem = Views.PostItem.extend({
                    tagName: 'li',
                    className: 'extra-item',
                    template: _.template($('#extra-item-loop').html()),
                    events: _.extend({
                        'click input[type="checkbox"]': 'checkBox'
                    }, Views.PostItem.prototype.events),
                    onItemBeforeRender: function () {
                        // before render view
                    },
                    onItemRendered: function () {
                        var view = this;
                        view.$el.attr('data-id', view.model.get('ID'));
                    },
                    checkBox: function (e) {
                        $target = $(e.currentTarget);
                        AE.pubsub.trigger('mjob:extra:add', $target);
                    }
                });
                /**
                 * list view control mjob list
                 */
                ListExtras = Views.ListPost.extend({
                    tagName: 'ul',
                    itemView: extraItem,
                    itemClass: 'extra-item'
                });
                $('.extra-container').each(function () {
                    if (typeof view.extraCollection == 'undefined') {
                        //Get public  collection
                        if ($('.extra_postdata').length > 0) {
                            var extra = JSON.parse($('.extra_postdata').html());
                            view.extraCollection = new Collections.Extras(extra);
                        } else {
                            view.extraCollection = new Collections.Extras();
                        }
                    }
                    /**
                     * init list blog view
                     */
                    view.listExtras = new ListExtras({
                        itemView: extraItem,
                        collection: view.extraCollection,
                        el: $(this).find('.mjob-list-extras')
                    });
                    view.listExtras.render();
                    /**
                     * init block control list blog
                     */
                    new Views.BlockControl({
                        collection: view.extraCollection,
                        el: $(this)
                    });
                });
            },
            showStepFour: function(){
                $('.post-service-step-1').addClass('done');
                $('.post-service-step-2').removeClass('active');
                $('.post-service-step-2').addClass('done');
                $('.post-service-step-3').addClass('active');
                $('.progress-bar-success').removeClass('half');
                $('.progress-bar-success').addClass('full');
            },
            setupFields: function() {
                this.initExtras();
                var view = this,
                    form_field = view.$('#step-post'),
                    location = this.model.get('location');
                /**
                 * update form value for input, textarea select
                 */
                form_field.find('input.input-item,input[type="text"],input[type="hidden"], textarea,select').each(function() {
                    var $input = $(this);
                    if( $input.attr('name') != '_wpnonce' ) {
                        $input.val(view.model.get($input.attr('name')));
                    }
                    // trigger chosen update if is select
                    if ($input.get(0).nodeName === "SELECT") $input.trigger('chosen:updated');
                });
                form_field.find('input[type="radio"]').each(function() {
                    var $input = $(this),
                        name = $input.attr('name');
                    if ($input.val() == view.model.get(name)) {
                        $input.attr('checked', true);
                    }
                });
                if( typeof view.model.get('mjob_extras') !== 'undefined' && view.model.get('mjob_extras').length > 0){
                    view.extrasCollection.reset(view.model.get('mjob_extras'));
                }
            },
            showNextStep: function () {
                var view = this,
                    next = 'auth';
                if (view.currentStep === 'plan') {
                    if (view.user_login) { // user login skip step auth
                        next = 'post';
                        this.arrFinish.push('step1');
                    }
                    view.showStepTwo();
                }
                // current step is auth
                if (view.currentStep == 'auth') {
                    // update user_login
                    view.user_login = true;
                    next = 'post';
                    this.arrFinish.push('step2');
                    view.showStepThree();
                }
                // current step is post
                if (view.currentStep == 'post') {
                    view.user_login = true;
                    next = 'payment';
                    this.arrFinish.push('step-post');
                    view.showStepFour();
                }
                view.$el.find('.step-'+ view.currentStep).hide();
                view.$el.find('.step-'+next).show();
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
            },
            selectStep: function(event) {
                event.preventDefault();
                var $target = $(event.currentTarget),
                    view = this,
                    select = $target.attr('data-id');
                // step authentication
                if (select == 'step1') {
                    if (this.arrFinish.length < 1) return;
                }
                //// step post
                if (select == 'step-post') {
                    if ($('#step2').length > 0 && this.arrFinish.length < 2) return;
                    if ($('#step2').length == 0 && this.arrFinish.length < 1) return;
                    $('.post-service-step-2').removeClass('done');
                }
                // step payment
                if (select == 'step4') {
                    if ($('#step2').length > 0 && this.arrFinish.length < 3) return;
                    if ($('#step2').length == 0 && this.arrFinish.length < 2) return;
                }
                if (!$target.hasClass('active')) {
                    // trigger to call view beforeSelectStep
                    this.triggerMethod('before:selectStep', $target);
                    $('#'+select).fadeIn(500);
                    $('#'+view.$el.find('.active').attr('data-id')).fadeOut(500);
                    // toggle content of selected step
                    switch(select) {
                        case 'step1':
                            if (this.arrFinish.indexOf('step1') != -1) {
                                $('.post-service-step-1').addClass('active');
                                $('.post-service-step-1').removeClass('done');
                                $('.post-service-step-2').removeClass('active');
                                $('.progress-bar-success').removeClass('half');
                                $('.progress-bar-success').removeClass('full');
                            }
                            break;
                        case 'step2':
                            if (this.arrFinish.indexOf('step2') != -1) {
                                $('.post-service-step-1').removeClass('active');
                                $('.post-service-step-1').addClass('done');
                                $('.post-service-step-2').addClass('active');
                                $('.post-service-step-3').removeClass('active');
                                $('.progress-bar-success').addClass('half');
                                $('.progress-bar-success').removeClass('full');
                            }
                            break;
                        case 'step-post':
                            if (this.arrFinish.indexOf('step-post') != -1){
                                if( view.user_login ){
                                    $('.post-service-step-1').removeClass('active');
                                    $('.post-service-step-1').addClass('done');
                                    $('.post-service-step-2').addClass('active');
                                    $('.post-service-step-3').removeClass('active');
                                    $('.progress-bar-success').addClass('half');
                                    $('.progress-bar-success').removeClass('full');
                                }
                                else {
                                    $('.post-service-step-1').addClass('done');
                                    $('.post-service-step-2').removeClass('active');
                                    $('.post-service-step-2').addClass('done');
                                    $('.post-service-step-3').addClass('active');
                                    $('.progress-bar-success').removeClass('half');
                                    $('.progress-bar-success').addClass('full');
                                }
                            }
                            break;
                        case 'step4':
                            if (this.arrFinish.indexOf('step4') != -1) {
                                $('.post-service-step-1').addClass('done');
                                $('.post-service-step-2').removeClass('active');
                                $('.post-service-step-2').addClass('done');
                                $('.post-service-step-3').addClass('active');
                                $('.progress-bar-success').removeClass('half');
                                $('.progress-bar-success').addClass('full');
                            }
                            break;
                        default :
                        break;
                    }
                    // trigger to call view afterSelectStep
                    this.triggerMethod('after:selectStep', $target, this);
                }
            },
            onAfterInit: function () {
                var view = this;
                if ($('#edit_postdata').length > 0) {
                    var postdata = JSON.parse($('#edit_postdata').html());
                    view.model = new Models.Mjob(postdata);
                    view.model.set('renew', 1);
                } else {
                    view.model = new Models.Mjob;
                }
                if( typeof view.user_login !== 'undefined' && $('#step1').length <= 0 ){
                    $('#step-post').show();
                }
                if( $('#step1').length <= 0 ){
                    $('#step2').show();
                }
                view.model.set('post_type', 'mjob_post');
                if($('.sw_skill').length > 0) {
                    this.$('.sw_skill').chosen({
                        max_selected_options:parseInt(ae_globals.max_cat),
                        inherit_select_classes: false,
                        width: '100%',
                    });
                }
                if(view.$('.skill-control').length > 0 ) {
                    //new skills view
                    new Views.Skill_Control({
                        model: this.model,
                        el : view.$('.skill-control'),
                        name : 'skill'
                    });
                }
                if (typeof view.carousels === 'undefined') {
                    view.carousels = new Views.Carousel({
                        el: $('.gallery_container'),
                        name_item:'et_carousel',
                        uploaderID:'carousel',
                        model: view.model
                    });
                }
                view.redirect = false;
            },
            onAtPostSuccess: function(model, res){
                var view = this;
                if( typeof model.get('ID') !== 'undefined' ) {
                    view.$el.find('input[name="post_parent"]').val(model.get('ID'));
                }
               // view.extrasListView.syncChange();
                // Show step 3
                view.redirect_url = '';
                if( typeof res.data.redirect_url !== 'undefined') {
                    if(view.extrasListView.collection.models.length > 0 ){
                        view.redirect_url = res.data.redirect_url;
                    }
                    else{
                       window.location.href = res.data.redirect_url;
                    }
                }
                view.showStepThree();
                this.setupStep3(model);
                return false;
            },
            redirectPage: function(result, res, jqXHR){
                var view = this;
                if( typeof result !== 'undefined' && view.redirect_url != ''){
                    if( result.get('ID') == view.extrasListView.collection.models[view.extrasListView.collection.models.length -1].get('ID')){
                        window.location.href = view.redirect_url;
                    }
                }
            },
            onSubmitPaymentSuccess: function(response){
                if( typeof response.data.url !== 'undefined' && response.paymentType == 'CASH'){
                    window.location.href = response.data.url;
                }
            },
            setupFirstStep: function(){

            },
            formValidate: function() {
                /**
                 * post form validate
                 */
                var view = this;
                view.formValidator = $("form.post").validate({
                    ignore: "",
                    rules: {
                        post_title: "required",
                        mjob_category: "required",
                        post_content: "required",
                        time_delivery: {
                            required: true,
                            min: 0
                        },
                        et_carousels: 'required'
                    },
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
            customValidate: function(){
                var view = this;
                if(view.extrasListView.collection.models.length > 0 ) {
                    for( i =0; i< view.extrasListView.collection.models.length; i++){
                        if( parseFloat($('#et_budget_'+view.extrasListView.collection.models[i]._listenId).val()) <= 0){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.priceMinNoti,
                                notice_type: 'error'
                            });
                            return false;
                        }
                        if( $('#et_extra_'+view.extrasListView.collection.models[i]._listenId).val() == ''){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.requiredField,
                                notice_type: 'error'
                            });
                            $('#et_extra_'+view.extrasListView.collection.models[i]._listenId).focus();
                            return false;
                        }
                        if( $('#et_budget_'+view.extrasListView.collection.models[i]._listenId).val() == ''){
                            AE.pubsub.trigger('ae:notification', {
                                msg: ae_globals.requiredField,
                                notice_type: 'error'
                            });
                            $('#et_budget_'+view.extrasListView.collection.models[i]._listenId).focus();
                            return false;
                        }
                    }
                }
                return true;
            },
            checkImage: function(model){
                var view = this;
                if($('.image-list').find('li').length <=1 ){
                    this.changeImage(ae_globals.mJobDefaultGalleryImage, '');
                    if( view.$el.find('input[name="et_carousels"]').length == 0){
                        view.$el.find('.carousel-gallery').append('<input type="hidden" name="et_carousels" value="" />');
                        $('.upload-description').removeClass('hide');
                    }
                }
                else{
                    var src = $('.image-list li').first().find('a').attr('data-full');
                    var attach_id = $('.image-list').first().find('a').attr('data-id');
                    this.changeImage(src, attach_id);
                }
            },
            onAfterPostFail: function(model, res) {
                AE.pubsub.trigger('ae:notification', {
                    msg: res.msg,
                    notice_type: 'error',
                });
            },
            showPreviewImage: function(event){
                var view = this;
                event.preventDefault();
                $target = $(event.currentTarget);
                attach_id = $target.attr('data-id');
                view.changeImage($target.attr('data-full'), attach_id);
            },
            uploadSuccess: function(res){
                var view = this;
                if( typeof res.data.full !== 'undefined' ) {
                    view.changeImage(res.data.mjob_detail_slider[0], res.data.attach_id);
                    if( view.$el.find('input[name="et_carousels"]').length > 0){
                        view.$el.find('.carousel-gallery input, .carousel-gallery label').remove();
                    };
                }
            },
            changeImage: function(src, attach_id){
                var view = this;
                if(typeof $('.mjob-replace-image').attr('data-delete') !== 'undefined' ){
                    attachID  = $('.mjob-replace-image').attr('data-delete');
                    $('#mjob-delete-' + attachID).click();
                }
                view.$el.find('.carousel-gallery img').attr('src', src);
                view.$el.find('.mjob-delete-image').attr('data-id', attach_id);
                view.$el.find('.mjob-replace-image').attr('data-id', attach_id);
                $('.upload-description').addClass('hide');
            },
            authSuccess: function(result, resp, jqXHR, type){
                var view = this;

                if(type == 'signIn' || type == 'signUp') {
                    // Show notification
                    if(resp.success == true) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'success'
                        });
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        });
                    }
                }

                if( ( type == 'signIn'  || type == 'signUp') && (resp.success &&$('.step-auth').length > 0 )   ) {
                    // Live update user section on main navigation
                    if($('#mjob_my_account_header').length > 0) {
                        var template = _.template($('#mjob_my_account_header').html());
                        $('#myAccount').html(template({
                            avatar: resp.data.avatar,
                            display_name: resp.data.display_name
                        }));
                    }
                    view.addFinishStep('step2');
                    view.currentStep = 'auth';
                    view.showNextStep();
                    view.$el.find('.mjob-progress-bar-item').html(ae_globals.progress_bar_3);
                    /*view.showStepTwo();*/
                    $('.post-service-step-1').removeClass('active');
                    $('.post-service-step-1').addClass('done');
                    $('.progress-bar-success').addClass('half');
                    $('.post-service-step-2').addClass('active');
                }
            },
            replaceImage: function (event) {
                event.preventDefault();
                $target = $(event.currentTarget);
                if( ('.carousel_browse_button').length > 0) {
                    $('.carousel_browse_button').click();
                    $target.attr('data-delete', $target.attr('data-id'))
                }
            },
            deleteImage: function(event){
                event.preventDefault();
                var view = this;
                $target = $(event.currentTarget);
                attach_id = $target.attr('data-id');
                if( $('#mjob-delete-'+attach_id).length > 0 ) {
                    $('#mjob-delete-' + attach_id).click();
                    view.changeImage(ae_globals.mJobDefaultGalleryImage, '');
                }
            },
            addExtras: function(event){
                event.preventDefault();
                var view = this;
                //Generate invalid id for temp use in template
                var model = new Models.Extra({
                    post_title: '',
                    et_budget: '',
                });
                this.extrasCollection.add(model);
                if( $('.post-service_nonce').length >0 ){
                    $('.mjob-extras-wrapper').find('input[name="_wpnonce"]').each(function(){
                        $(this).val($('.post-service_nonce').val());
                    });
                }
            },
            initExtras: function(){
                var view = this;
                // Use the code below
                if (typeof view.extrasCollection === 'undefined') {
                    //Get public  collection
                    if ($('#mJobExtrasdata').length > 0) {
                        var extras = JSON.parse($('#mJobExtrasdata').html());
                        view.extrasCollection = new Collections.Extras(extras);
                    } else {
                        view.extrasCollection = new Collections.Extras();
                    }
                }
                if(typeof view.extrasListView === 'undefined') {
                    view.extrasListView = new Views.ExtrasListView( {
                        collection: view.extrasCollection,
                        el: '.mjob-extras-wrapper'
                    } );
                    view.extrasListView.render();
                }
            },
            handleAuth: function(model, resp, jqXHR){
                var view = this;
                view.$el.find('input[name="_wpnonce"]').val(resp.data.mjobAjaxNonce);
            },
            setupCarousels: function(res){
                if( res.data.length > 0 ){
                    if( $('input[name="et_carousels"]').length > 0 ) {
                        $('input[name="et_carousels"]').remove();
                    }
                    var src = res.data[0].mjob_detail_slider['0'];
                    var attach_id = res.data.attach_id;
                    this.changeImage(src, attach_id);
                }

            },
        });

    });

})(jQuery, AE.Models, AE.Collections, AE.Views);
