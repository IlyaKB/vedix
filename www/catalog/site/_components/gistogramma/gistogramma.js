define(["require", "exports"], function(require, exports) {
    var GistogrammaModel = (function () {
        function GistogrammaModel(config) {
            var self = this;

            _.bindAll(this, 'setCurrency');

            this.cntId = config.cntId;
            this.elCnt = $('#' + this.cntId);

            this.currencies = [
                { divider: 1, measDivider: 'млн.', course: 1 },
                { divider: 1000, measDivider: 'тыс.', course: 48 },
                { divider: 1000, measDivider: 'тыс.', course: 36 }
            ];

            this.graph = {
                width: 540,
                height: 300,
                margin: { top: 20, right: 60, bottom: 30, left: 0 },
                svg: null,
                divTooltip: null,
                x: null,
                y: null,
                x_axis: null,
                y_axis: null
            };
            this.initGraph();

            this.currency = ko.observable({});
            this.currency.subscribe(function () {
                self.draw();
            });
            this.currency({ index: 0, currencyText: 'р' });
        }
        GistogrammaModel.prototype.setCurrency = function (obj, el) {
            var button = $(el.target).closest('button');
            this.currency({ index: button.data('currency'), currencyText: button.text() });
        };

        GistogrammaModel.prototype.initGraph = function () {
            this.graph.svg = d3.select('#gistogramma_graph').append('svg').attr('width', this.graph.width + this.graph.margin.left + this.graph.margin.right).attr('height', this.graph.height + this.graph.margin.top + this.graph.margin.bottom).append('g').attr('transform', 'translate(' + this.graph.margin.left + ', ' + this.graph.margin.top + ')');

            this.graph.divTooltip = d3.select('#gistogramma_graph').append('div').attr('class', 'tooltip').style('opacity', 0);

            this.graph.x = d3.scale.ordinal().rangeRoundBands([0, this.graph.width], .1);
            this.graph.y = d3.scale.linear().range([this.graph.height, 0]);

            this.graph.x_axis = d3.svg.axis().scale(this.graph.x).orient('bottom');

            this.graph.y_axis = d3.svg.axis().tickFormat(function (s) {
                return d3.format('.2s')(s);
            }).tickSize(-this.graph.width, 0).ticks(9).scale(this.graph.y).orient('right');
        };

        GistogrammaModel.prototype.draw = function () {
            var self = this;

            var data = [
                { letter: 'A', price: 7000000 * Math.random(), color: '#f66' },
                { letter: 'B', price: 6500000, color: '#6f6' },
                { letter: 'C', price: 6700000, color: '#66f' },
                { letter: 'D', price: 3300000, color: '#ff0' },
                { letter: 'E', price: 4800000, color: '#0dd' },
                { letter: 'F', price: 5000000, color: '#e5e' },
                { letter: 'G', price: 5200000, color: '#aa0' },
                { letter: 'H', price: 4000000, color: '#4bb' },
                { letter: 'I', price: 5500000, color: '#a5b' },
                { letter: 'J', price: 2000000, color: '#6a7' }
            ];

            var index = self.currency().index;
            var _data = _.map(data, function (d) {
                d.price = d.price / (self.currencies[index].course * self.currencies[index].divider);
                return d;
            });

            this.graph.svg.selectAll('*').remove();

            this.graph.x.domain(_data.map(function (d) {
                return d.letter;
            }));
            this.graph.y.domain([0, d3.max(_data, function (d) {
                    return d.price * 1.1;
                })]);

            this.graph.svg.append('g').attr('class', 'x axis').attr('transform', 'translate(0,' + this.graph.height + ')').call(this.graph.x_axis);
            this.graph.svg.append('g').attr('class', 'y axis').attr('transform', 'translate(' + this.graph.width + ', 0)').call(this.graph.y_axis).call(function (s) {
                s.append('text').attr({ transform: 'translate(4,-5)', class: 'axis-y-label' }).text(function () {
                    return self.currencies[self.currency().index].measDivider;
                });
                s.append('text').attr({ transform: 'translate(30,-5)', class: 'axis-y-label rouble' }).html(self.currency().currencyText);
            });

            this.graph.svg.selectAll('.bar').data(_data).enter().append('rect').attr('class', 'bar').attr('fill', function (d, i) {
                return d.color;
            }).attr('x', function (d) {
                return self.graph.x(d.letter) + 3;
            }).attr('width', this.graph.x.rangeBand() - 5).attr('y', function (d) {
                return self.graph.y(d.price);
            }).attr('height', function (d) {
                return self.graph.height - self.graph.y(d.price);
            }).on('mouseover', function (d, i) {
                self.graph.divTooltip.transition().duration(200).style('opacity', .95);
                self.graph.divTooltip.html(Math.floor(Math.random() * 100) + ' квартиры в продаже<br/><span>' + Math.floor(10 - Math.random() * 20) + '%</span> за февраль');
            }).on('mousemove', function (d, i) {
                self.graph.divTooltip.style('left', (d3.event.pageX - 50) + 'px').style('top', (d3.event.pageY - 40) + 'px');
            }).on('mouseout', function (d, i) {
                self.graph.divTooltip.transition().duration(500).style('opacity', 0);
            });

            this.graph.svg.selectAll('.rectLabel').data(_data).enter().append('text').attr({
                x: function (d) {
                    return self.graph.x(d.letter) + 15;
                },
                y: function (d) {
                    return self.graph.y(d.price) - 5;
                },
                class: 'rectLabel'
            }).text(function () {
                return Math.floor(10 - Math.random() * 20) + '%';
            });
        };
        return GistogrammaModel;
    })();

    
    return GistogrammaModel;
});
