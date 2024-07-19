
/**
* user.password.js
* Author: JCm
*/

$(document).ready(function(){
	
	var sDomain       = document.domain;
    var sSiteBasePath = '/tablet/index.php/';
    if(sDomain === 'localhost' || sDomain === '127.0.0.1'){
		sSiteBasePath = '/tablet/index.php/';
    }
    if(document.location !== undefined && location.port){
        sDomain = document.domain + ':' + location.port
    }
    var sUrl = 'http://'+ sDomain + sSiteBasePath +'clinical_trigger_api/';
	
	//Send asyncronous request to the server
    //-------------------------------------
    var ajaxRequest = function(type, url, pData, successCallback, completeCallback, errorCallback, pAsync){
        
        pData = pData || null;                       //data to be transmitted asyncronously
        successCallback  = successCallback || null;  //call back function when ajax request completed
        completeCallback = completeCallback || null; //call back function when ajax request completed
        errorCallback = errorCallback || ajaxOnError;
		if(typeof pAsync === 'undefined'){
			pAsync = true;
		}
        
        $.ajax({
            type: type,
            data: pData,
            url: url,
			async: pAsync,
            success: function(data, status, xhr){
                if(status){
                    if(successCallback){
                        successCallback(data, status, xhr);
                    }
                }else{
                    //Show XHR response text
					alert(status);
                }
            },
            error: function(xhr, status, errStr){
				if(errorCallback){
					errorCallback(xhr, status, errStr);
				}
            },
            complete: function(xhr, status){
                //completeCallback(xhr, status);
                if(completeCallback){
                    completeCallback(xhr, status);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    };
	
	var revup_referral = $("#revup_referral");
	if(revup_referral.length > 0){
		var currentLocation = window.location;
		var pathname		= currentLocation.pathname;
		pathname 			= pathname.split("/");
		
		//alert(pathname[5]); check with regex if its the correct encounter_id
		ajaxRequest('GET', sUrl + 'revup_referral/' + pathname[5], null, 
			function(data, status, xhr){
				if(data.status){
					$('input[name="username"]').val(data.patient.FirstName + ' ' + data.patient.LastName);
					$('input[name="activation_code"]').val(data.patient.PMS_PKey);
				}else{
					alert('Failed to get encounter details.');
				}
			}, 
			null, 
			function(xhr, status, errStr){
				lert('Clinical trigger api module server error.\n'+ status +': '+ errStr +'\nPlease contact JCm');
			}
		);
	}
	
});