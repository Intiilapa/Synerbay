(function ($) {
    'use strict';
    var synerbay = synerbay || {};
    synerbay.init = function () {
        synerbay.$body = $(document.body),
            synerbay.$window = $(window);
    }


    // MOCK
    synerbay.showModal = function(name) {
        var $modal = $('#mf-'+name+'-popup');
        $modal.fadeIn();
        $modal.addClass('open');

        // if (days > 0 && document.cookie.match(/^(.*;)?\s*mf_offer_apply_popup\s*=\s*[^;]+(.*)?$/)) {
        //     return;
        // }

        if ($modal.length < 1) {
            return;
        }

        // martfury.$window.on('load', function () {
        //     setTimeout(function () {
        //         $modal.addClass('open');
        //     }, seconds * 1000);
        // });

        $modal.on('click', '.close-modal', function (e) {
            e.preventDefault();
            closeModal();
            $modal.removeClass('open');
            $modal.fadeOut();
        });

        $modal.on('click', '.n-close', function (e) {
            e.preventDefault();
            closeModal();
            $modal.removeClass('open');
            $modal.fadeOut();
        });

        $modal.find('.mc4wp-form').submit(function () {
            closeModal();
        });

        $modal.find('.formkit-form').submit(function () {
            closeModal();
        });

        function closeModal() {
            var date = new Date(),
                value = date.getTime();
            //
            // date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));

            document.cookie = 'mf_'+name+'_popup=' + value + ';expires=' + date.toGMTString() + ';path=/';
        }
    }

    synerbay.appearOffer= function(offerID) {
        let productQty = $("input[name=quantity]").val();

        // console.log(productQty);
        let response = synerbay.restCall({
            'offerID': offerID,
            'productQty': productQty,
        }, 'appear_offer', true);

        synerbay.hideLoader();
        location.reload();
    }

    synerbay.disAppearOffer= function(offerID) {
        // console.log(productQty);
        let response = synerbay.restCall({
            'offerID': offerID
        }, 'disappear_offer', true);

        synerbay.hideLoader();
        location.reload();
    }

    synerbay.restCall = function (callParams, endpoint, returnResponse = false) {
        synerbay.showLoader();

        let returnData = true;

        $.ajax({
            type: "post",
            dataType: "json",
            async: false,
            url : synerbayAjax.restURL + 'synerbay/api/v1/' + endpoint,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', synerbayAjax.restNonce)
            },
            data : callParams,
            success: function(response, textStatus, xhr) {
                if(response.success !== 'undefined' && response.success)  {
                    if (returnResponse) {
                        return returnData = response;
                    } else {
                        synerbay.hideLoader();
                        // toastr??
                    }
                } else {
                    returnData = false;
                    // toastr??
                    if (xhr.status === 401) {
                        synerbay.hideLoader();
                        // toastr??
                    } else {
                        synerbay.hideLoader();
                        // toastr??
                    }
                }
            },
            complete: function(xhr, textStatus) {
                if (xhr.status === 401) {
                    synerbay.hideLoader();
                    synerbay.showModal('login');
                }
            }
        });

        return returnData;
    }

    synerbay.showLoader = function() {
        let loader = document.getElementsByClassName('loader')[0];
        loader.style.display = "";
    }

    synerbay.hideLoader = function() {
        let loader = document.getElementsByClassName('loader')[0];
        loader.style.display = "none";
    }

    /**
     * Document ready
     */
    $(function () {
        synerbay.init();
        window.synerbay = synerbay;
    });
})
(jQuery);