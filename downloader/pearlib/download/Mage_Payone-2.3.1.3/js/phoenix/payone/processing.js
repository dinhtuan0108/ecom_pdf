/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 */
Event.observe(window, 'load', function(){
	if (typeof isCcApiEnabled != 'undefined' && isCcApiEnabled) {
        if (typeof payment != 'undefined' && typeof payment.save != 'undefined') {
            payonePaymentWrap();
        }
        if (typeof order != 'undefined' && typeof order.submit != 'undefined') {
            payoneOrderWrap();
            prepareParamsWrap();
        }
    }
});

function enableElements(elements) {
	for (var i=0; i<elements.length; i++) {
    	elements[i].disabled = false;
	}
}
function payoneDisplayCcForm() {
	$('payment_form_payone_cc').contentWindow.show('payone_cc_iframe_cont');
	$('payment_form_payone_cc').contentWindow.hide('payoneDisplayCcFormLink');
    $('hidden_cc_additional_data').setAttribute('value', '');
}

function payonePaymentWrap() {
	payment.save = payment.save.wrap(function(origSaveMethod){
		if ($('p_method_payone_cc') && $('p_method_payone_cc').checked) {
			if (checkout.loadWaiting!=false) return;
			
			var elements = $('fieldset_payone_cc').select('input[type="hidden"]');
			enableElements(elements);
			
			if (!$('hidden_cc_additional_data').hasAttribute('value') || $('hidden_cc_additional_data').getAttribute('value').length==0) {
				if (this.validate() && $('payment_form_payone_cc').contentWindow.validate()) {
					checkout.setLoadWaiting('payment');
					$('payment_form_payone_cc').contentWindow.updateHiddenElements(elements);
	                application.getApplicationParams();
				}
			}
			else {
				origSaveMethod();
			}
		} else {
			origSaveMethod();
		}
	});
}
function payoneOrderWrap() {
    order.submit = order.submit.wrap(function(origSaveMethod){
        if ($('p_method_payone_cc') && $('p_method_payone_cc').checked) {
            if(editForm.validator.validate() && $('payment_form_payone_cc').contentWindow.validate()){
            	var elements = $('fieldset_payone_cc').select('input[type="hidden"]');
				$('payment_form_payone_cc').contentWindow.updateHiddenElements(elements);
                application.isAdmin = true;
                application.getApplicationParams();
            }
        } else {
            origSaveMethod();
        }
        
    });
}

function prepareParamsWrap() {
    order.prepareParams = order.prepareParams.wrap(function(origMethod, origParams){
        if ($('p_method_payone_cc') && $('p_method_payone_cc').checked) {
            var params = origMethod(origParams);
            params['cc_owner'] = '';
            params['cc_type'] = '';
            params['cc_number'] = '';
            params['cc_exp_month'] = '';
            params['cc_exp_year'] = '';
            params['cc_cid'] = '';
            return params;
        } else {
            return origMethod(origParams);
        }
    });
}

var saveSessInfoUrlGlobal = false;
var payoneApplication = Class.create();
    payoneApplication.prototype = {
        initialize: function(initUrl, saveSessInfoUrl){
            this.isError = false;
            this.error = '';
            this.isAdmin = false;
            this.initUrl = initUrl;
            saveSessInfoUrlGlobal = saveSessInfoUrl;
            this.data = {
                request : 'creditcardcheck',
                storecarddata : 'yes'
            }
            this.onSuccessGetParams = this.initApplication.bindAsEventListener(this);
            this.onCompleteGetParams = this.payoneCcSave.bindAsEventListener(this);
        },
        getApplicationParams: function()
        {
            if (typeof checkout != 'undefined') {
                var request = new Ajax.Request(
                    this.initUrl,
                    {
                        method:'post',
                        onComplete: this.onCompleteGetParams,
                        onSuccess: this.onSuccessGetParams,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: ''
                    }
                );
            } else {
                var request = new Ajax.Request(
                    this.initUrl,
                    {
                        method:'post',
                        onComplete: this.onCompleteGetParams,
                        onSuccess: this.onSuccessGetParams,
                        parameters: ''
                    }
                );
            }
        },
        initApplication: function(transport)
        {
            if (transport && transport.responseText){
                try{
                    response = eval('(' + transport.responseText + ')');
                }
                catch (e) {
                    response = {};
                }
            }
            this.data.mode = response.mode;
			this.data.mid = response.mid;
            this.data.aid = response.aid;
            this.data.portalid = response.portalid;
            this.data.language = response.language;
            this.data.encoding = response.encoding;
            this.data.hash = response.hash;
			this.data.cardholder = $('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_cc_owner');
			this.data.cardpan = $('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_cc_number');
			this.data.cardtype = getCardType($('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_cc_type'));
			this.data.cardexpiremonth = $('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_expiration');
			this.data.cardexpireyear = $('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_expiration_yr');
			this.data.cardcvc2 = $('payment_form_payone_cc').contentWindow.getInputValue('payone_cc_cc_cid');
        },
        payoneCcSave: function()
        {
            var cllBack = this.isAdmin ? 'processPayoneResponseAdmin' : 'processPayoneResponse';
            var request = new PayoneRequest(this.data, {
                return_type : 'object',
                callback_function_name : cllBack
            });
            request.checkAndStore();            
        }
    }
function processPayoneResponse(response)
{
    if (response.get('status') != 'VALID') {
    	alert(response.get('customermessage'));
        checkout.setLoadWaiting(false);
    } else {
        $('hidden_cc_additional_data').value = response.get('pseudocardpan');
        
        if (saveSessInfoUrlGlobal) {
	        new Ajax.Request(
	        	saveSessInfoUrlGlobal,
	            {
	                method: 'post',
	                parameters: Form.serialize(payment.form)
	            }
	        );
        }
        
        var request = new Ajax.Request(
            payment.saveUrl,
            {
                method:'post',
                onComplete: payment.onComplete,
                onSuccess: payment.onSave,
                onFailure: checkout.ajaxFailure.bind(checkout),
                parameters: Form.serialize(payment.form)
            }
        );
    }
}

function processPayoneResponseAdmin(response)
{
    if (response.get('status') != 'VALID') {
    	alert(response.get('customermessage'));
    } else {
    	var elements = $('fieldset_payone_cc').select('input[type="hidden"]');
		enableElements(elements);
        $('hidden_cc_additional_data').value = response.get('pseudocardpan');
        if(order.orderItemChanged){
            if(confirm('You have item changes')){
                editForm.submit();
            }
            else{
                order.itemsUpdate();
            }
        }
        else{
            editForm.submit();
        }
    }
}
function getCardType(cardtype){
	if(cardtype == "VI") return "V";
	else if(cardtype == "MC") return "M";
	else if(cardtype == "AE") return "A";
	else return "";
}
