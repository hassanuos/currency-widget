"use strict";

// IIFE
(function (){
    const $loader = $("table#loading-overlay");

    // multiple api endpoints object
    const apiUrls = {
        live_currency: `${BASE_URL}/ExchangeRateAPI.php`
    };

    // General Call for each kind of request GET / POST
    const callRequest = (options) => {

        if (typeof options.beforeRequest !== "undefined") {
            options.beforeRequest();
        }
        let dataType = (typeof options.dataType !== "undefined" ? options.dataType : "html");
        $.ajax({
            url: options.url,
            type: options.method,
            dataType: dataType,
            data: options.data,
            processData: (dataType !== "JSON"),
            contentType: (dataType === "JSON" ? false : "application/x-www-form-urlencoded"),
            error: function (response) {
                if (typeof options.errorRequest !== "undefined") {
                    options.errorRequest(response);
                }
            },
            success: function (response) {
                if (typeof options.afterRequest !== "undefined") {
                    options.afterRequest(response);
                }
            }
        });
    };

    // cache expiry count down for end user
    const cacheCountDown =  (duration, display) => {
        if (!isNaN(duration)) {
            var timer = duration, minutes, seconds;

            const interVal = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                $(display).html("<b>" + minutes + "m : " + seconds + "s" + "</b>");
                if (--timer < 0) {
                    timer = duration;
                    loadData();
                    $('a.display-timer span').text("Timeout! Requesting New Data...");
                    clearInterval(interVal)
                }
            }, 1000);
        }
    }

    // generic function load latest data
    const loadData = () => {
        callRequest({
            url: apiUrls.live_currency,
            method: 'GET',
            dataType: "JSON",
            errorRequest: function(e){
                toastr.error("Something went wrong");
            },
            beforeRequest: function (e) {
                $loader.LoadingOverlay("show", {"size": 5});
            },
            afterRequest: (response) => {
                if(response.success){
                    let currencyHtml = "";

                    // Header setting
                    $("th.table-head").html(`<div class="flag-icon flag-icon-${response.base.substring(0, 2).toLowerCase()}"></div>&nbsp;${response.base.substring(0, 2)} Dollar Exchange Rate`);
                    currencyHtml += `<tr><td colspan="2" class="text-right">1 ${response.base} = </td><tr>`;

                    // collect each record to render
                    response.rates.forEach((obj, index) => {
                        currencyHtml += `<tr><td><div class="flag-icon ${obj.flag_class}"></div>&nbsp;${obj.country_name}</td><td class="text-right">${obj.amount}</td></tr>`;
                    });

                    // Footer Setting
                    currencyHtml += `<tr><td colspan="2" class="text-right">Rates ${response.date}</td><tr>`;
                    $("tbody#data-container").html(currencyHtml);

                    // Start count down for cache expiry
                    cacheCountDown(response.cache_expiry_time, $("a.display-timer span"));
                }else{
                    toastr.error("Something went wrong!");
                }

                $loader.LoadingOverlay("hide");
            }
        })
    }

    $(document).ready(() => {
        loadData();
    });

    $(document).on("click", ".reload", () => {
        loadData();
    });

})();