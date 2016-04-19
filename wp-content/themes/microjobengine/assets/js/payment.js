/**
 * Created by Jack Bui on 1/21/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function () {
        Views.Order = Backbone.View.extend({
            el: '.mjob-order-page',
            events: {
                'click .mjob-btn-checkout': 'checkOut',
                'click .select-payment': 'selectPayment'
            },
            initialize: function (options) {
                AE.pubsub.on('ae:user:auth', this.handleAuth, this);
            },
            checkOut: function(e){
                $('html, body').animate({
                    scrollTop: $("html, body").offset().top
                }, 1000);
                e.preventDefault();
                this.setupOrder();
            },
            selectPayment: function(e){
                e.preventDefault();
                var $target = $(e.currentTarget),
                    paymentType = $target.attr('data-type'),
                    view = this;
                view.orderModel.set('payment_type', paymentType);
                view.saveOrder();

            },
            setupOrder: function(){
                var view = this;
                if( typeof view.orderModel === 'undefined' ){
                    if( $('#mjob-order-info').length > 0 ) {
                        orderdata = JSON.parse($('#mjob-order-info').html());
                        view.orderModel = new Models.Order(orderdata);
                    }
                }
                view.orderModel.set('extra_ids', AE.single.extra_ids);
                $('.mjob-order-info').hide();
                $('#checkout-step2').fadeIn(500);
                $(".items-chosen").hide();
                $(".mjob-order-page").addClass("continoue");
            },
            saveOrder: function(){
                var view = this;
                view.orderModel.save( '', '', {
                    beforeSend: function () {
                    },
                    success: function ( result, res, jqXHR ) {
                        if (res.success && res.data.ACK) {
                            if( res.data.updateAuthor ){
                                window.location.href = res.data.permalink;
                            }
                            else {
                                // call method onSubmitPaymenSuccess
                                // update form check out and submit
                                $('#checkout_form').attr('action', res.data.url);
                                if ($('#checkout_form .packageType').length > 0) {
                                    $('#checkout_form .packageType').val(view.model.get('et_package_type'));
                                }
                                if (typeof res.data.extend !== "undefined") {
                                    $('#checkout_form .payment_info').html('').append(res.data.extend.extend_fields);
                                }
                                // trigger click on submit button
                                $('#payment_submit').click();
                            }
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: resp.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                } );
            },
            handleAuth: function(model, resp, jqXHR){
                var view = this;
                if( $('#mjob-order-data').length > 0){
                    data = JSON.parse($('#mjob-order-data').html());
                    view.orderModel = new Models.Order(data);
                    view.orderModel.set('updateAuthor', 1);
                    view.orderModel.set('_wpnonce', resp.data.mjobAjaxNonce);
                    view.saveOrder();
                }
            }
        });
        new Views.Order();

    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
