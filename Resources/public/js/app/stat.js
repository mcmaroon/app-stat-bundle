(function ($) {

    if (typeof APP === 'object') {

        APP.stat = function (selector, options) {
            var self = this;

            self.selector = selector;

            self.chart = null;

            self.response = null;

            self.formChangedCheck = null;

            self.xhr = null;

            var defaults = {
                url: null,
                formSelector: '#form-stat'
            };

            self.settings = $.extend({}, defaults, options);

            // ~

            self.appendChart = function () {
                try {
                    var data = google.visualization.arrayToDataTable(self.response.items);

                    var options = {
                        //selectionMode: 'multiple',
                        //tooltip: {trigger: 'selection'},
                        //aggregationTarget: 'category',
                        lineWidth: 1,
                        pointSize: 3,
                        legend: 'bottom',
                        vAxis: {
                            viewWindow: {
                                min: 0
                            }
                        }
                    };

                    var chart = new google.visualization.AreaChart($(self.selector).get(0));
                    chart.draw(data, options);
                } catch (err) {
                    APP.trace('appendChart error ' + err.message);
                }
            };

            // ~

            self.sendRequest = function () {

                if (self.xhr && self.xhr.readyState !== 4) {
                    self.xhr.abort();
                }

                self.xhr = $.ajax({
                    cache: false,
                    url: self.settings.url,
                    method: 'GET',
                    dataType: 'json',
                    data: self.formChangedCheck,
                    beforeSend: function () {
                        $(self.selector).addClass('preloader');
                    },
                    complete: function () {
                        $(self.selector).removeClass('preloader');
                    },
                    error: function (request, status, error) {

                    },
                    success: function (response, status, request) {
                        if (typeof response.items === 'object') {
                            self.response = response;
                            self.appendChart();
                        }
                    }
                });
            };

            // ~

            self.init = function () {

                self.formChangedCheck = $(self.settings.formSelector).serialize();

                self.sendRequest();

                $(self.settings.formSelector).on('keyup change', 'input, select, textarea', function () {
                    if (self.formChangedCheck !== $(self.settings.formSelector).serialize()) {
                        self.formChangedCheck = $(self.settings.formSelector).serialize();
                        self.sendRequest();
                    }
                });
            };

            self.init();

        };

    }

})(jQuery);