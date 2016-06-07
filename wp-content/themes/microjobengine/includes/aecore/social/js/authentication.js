(function($){
AE.Views.SocialAuth = Backbone.View.extend({
	el: 'body',
	events: {
		'submit #form_auth' 	: 'authenticate',
		'submit #form_username' : 'confirm_username',
		'click .gplus_login_btn' : 'gplusDoLogin',
		'click .lkin' : 'lkinDoLogin'
	},
	initialize: function(){
		var view = this;
		this.blockUi = new AE.Views.BlockUi();
	},
	getCookie: function(){
		var view = this;
		$.ajax({
			url : ae_globals.ajaxURL,
			type : "get",
			data :{
				action: "ae-get-current-cookie"
			},
			beforeSend: function() {

			},
			success:function(resp){
				console.log(resp);
			}
		});
	},
	gplusDoLogin: function (){
		var view = this;
		$.ajax({
			url : ae_globals.ajaxURL,
			type : "get",
			data :{
				action: "ae_gplus_auth",
			},
			beforeSend: function() {
                view.blockUi.block('.gplus');
            },
			success:function(resp){
				if( resp.success ){

					window.location.href = resp.redirect;
				}
				else{
					AE.pubsub.trigger('ae:notification', {
						msg: resp.msg,
						notice_type: 'error',
					});		
				}
			}
		});
	},
	lkinDoLogin: function(e){
		var view = this;
		$.ajax({
			url : ae_globals.ajaxURL,
			type : "get",
			data :{
				action: "ae_linked_auth",
				state : "click"
			},
			beforeSend: function() {
                view.blockUi.block('.lkin');
            },
			success:function(resp){
				if( resp.success ){
					window.location.href = resp.redirect;
				}
				else{
					AE.pubsub.trigger('ae:notification', {
						msg: resp.msg,
						notice_type: 'error',
					});		
				}
			}
		});
	},
	authenticate: function(event){
		event.preventDefault();
		var form = $(event.currentTarget);
		var view = this;
		var params = {
			url: 	ae_globals.ajaxURL,
			type: 	'post',
			data: {
				action: 'et_authentication_'+JSON.parse($('#social_type').html()),
				content: form.serializeObject()
			},
			beforeSend: function(){
				//submit
				  var button = form.find('input[type=submit]')
				  view.blockUi.block(button);
			}, 
			success: function(resp){
				if ( resp.success ){
					if ( resp.data.status == 'wait' ){
						view.confirm_username();
					} else if ( resp.data.status == 'linked' ){
						AE.pubsub.trigger('ae:notification', {
							msg: resp.msg,
							notice_type: 'success',
						});
						setTimeout(function() {
							window.location = resp.data.redirect_url;
						}, 3000);
						//window.location.reload();
					}
				}
				else{
					msg = 'ERROR!';
					if(resp != 0){
						msg = resp.msg;
					}
					AE.pubsub.trigger('ae:notification', {
						msg: msg,
						notice_type: 'error',
					});				
				}
			}, 
			complete: function(){
				//view.blockUi.unblock();
			}
		}
		$.ajax(params);
	},
	
	confirm_username: function(){
		var view = this;
		if( $('#social_type').length > 0 ) {
			var params1 = {
				url: ae_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'et_confirm_username_'+JSON.parse($('#social_type').html()),
					content: $('#form_step2_auth').serializeObject()
				},
				beforeSend: function () {
					//form.find('input[type=submit]').loader('load');

				},
				success: function (resp) {
					//console.log(resp);
					if (resp.success == true) {
						AE.pubsub.trigger('ae:notification', {
							msg: resp.msg,
							notice_type: 'success'
						});
						setTimeout(function () {
							window.location = resp.data.redirect_url;
						}, 3000);
					} else {
						AE.pubsub.trigger('ae:notification', {
							msg: resp.msg,
							notice_type: 'error'
						});
					}
				},
				complete: function () {
					//form.find('input[type=submit]').loader('unload');
					//view.blockUi.unblock();
				}
			}
			console.log(params1);
			$.ajax(params1);
		}
	}
});

$(document).ready(function(){
	var view = new AE.Views.SocialAuth();
});
})(jQuery);