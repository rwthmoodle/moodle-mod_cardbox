// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   mod_cardbox
 * @copyright 2019 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function displayCharts(Y, __params) { // Wrapper function that is called by controller.php

    var __ismanager = __params['ismanager'];
    
    const ABSOLUTE_CARDS_OVER_DECK = 1;
    const CARDBOX_STATUS = 2;
    const USER_PERFORMANCE_OVER_TIME = 3;
    const CARDS_OVER_TIME = 4;
    const SESSION_DURATION = 5;

    var __performance = __params['performance'];

    require(['jquery', 'core/templates', 'core/chartjs'], function ($, templates, chart) {
    
        if(__ismanager) {
            displayAbsoluteCardsOverDecks();
            if (__performance.displayweeklystats) {
                displayNumberOfCardsOverTime();
                displayDurationOfASessionOverTime();
            }
        } else {
            displayCardboxStatus();
            displayUserPerformanceOverTime();
        }
        
    });

    /**
    * Function builds and displays a bar chart that shows how many cards
    * there are in the boxes of the current user's cardbox.
    * 
    * @returns {undefined}
    */
   function displayAbsoluteCardsOverDecks() {
    var __absoluteboxcount = __params['absoluteboxcount'];
    var context = document.getElementById("cardbox-statistics-absolute-over-deck").getContext("2d");
    var mixedChart = new Chart(context, {
        type: 'bar',
        data: {
            datasets: [{
                label: M.util.get_string('absolutenumberofcards', 'cardbox'),
                backgroundColor: [
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#00b33c'
            ],
                data: [__absoluteboxcount[0], __absoluteboxcount[1], __absoluteboxcount[2], __absoluteboxcount[3], __absoluteboxcount[4], __absoluteboxcount[5], __absoluteboxcount[6]],
            }],
            labels: [
                M.util.get_string('new', 'cardbox'),
                '1',
                '2',
                '3',
                '4',
                '5',
                M.util.get_string('known', 'cardbox')
            ]
        },
        options: {
            title: {
                display: true,
                text: M.util.get_string('barchartstatistic1', 'cardbox'),
                fontSize: 16,
                position: 'top'
              },
            scales: {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: M.util.get_string('barchartxaxislabel', 'cardbox'),
                        fontSize: 16,
                    },
                    stacked: true
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: M.util.get_string('barchartyaxislabel', 'cardbox'),
                        fontSize: 16
                    },
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        stepSize: calculateStepSize(ABSOLUTE_CARDS_OVER_DECK),
                    },
                    stacked: true
                }]
            }
        }
    });
    
   }
    /**
    * Function builds and displays a bar chart that shows how many cards
    * there are in the boxes of the current user's cardbox.
    * 
    * @returns {undefined}
    */
   function displayCardboxStatus() {

        var context = document.getElementById("cardbox-statistics-cardboxstatus").getContext("2d");
        var __studentboxcount = __params['studentboxcount'];
        var cardboxdata = {

           // These labels appear in the legend and in the tooltips when hovering different arcs.
            labels: [
                M.util.get_string('new', 'cardbox'),
                '1',
                '2',
                '3',
                '4',
                '5',
                M.util.get_string('known', 'cardbox')
            ],

           datasets: [{
                label: M.util.get_string('flashcardsdue', 'cardbox'),
                data: [__studentboxcount[0], __studentboxcount[1]['due'], __studentboxcount[2]['due'], __studentboxcount[3]['due'], __studentboxcount[4]['due'], __studentboxcount[5]['due'], 0],
                backgroundColor: [
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#00b33c'
                ],
                stack: 'Stack 0'
            },
            {
                label: M.util.get_string('flashcardsnotdue', 'cardbox'),
                data: [0, __studentboxcount[1]['notdue'], __studentboxcount[2]['notdue'], __studentboxcount[3]['notdue'], __studentboxcount[4]['notdue'], __studentboxcount[5]['notdue'], __studentboxcount[6]],
                backgroundColor: [
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#00b33c'
                ],
                stack: 'Stack 0'
           }]
       };

        if (__performance.displayaverageprogress) {
            var __averageboxcount = __params['averageboxcount'];
            cardboxdata.datasets.push({
                label: M.util.get_string('averagestudentscompare', 'cardbox'),
                data: [__averageboxcount[0], __averageboxcount[1], __averageboxcount[2], __averageboxcount[3], __averageboxcount[4], __averageboxcount[5], __averageboxcount[6]],
                backgroundColor: [
                        '#7A6FAC',
                        '#7A6FAC',
                        '#7A6FAC',
                        '#7A6FAC',
                        '#7A6FAC',
                        '#7A6FAC',
                        '#7A6FAC'
                ],
                stack: 'Stack 1'
            });
        }

       var barChart1 = new Chart(context, {
           type: 'bar',
           data: cardboxdata,
           options: {
               responsive: true,
               title: {
                   display: true,
                   text: M.util.get_string('titleoverviewchart', 'cardbox'),
                   fontSize: 16,
                   position: 'top'
               },
               legend: {
                   display: true,
                   position: 'top'
               },
               ticks: {
                   beginAtZero: true,
                   min: 0
               },               
               scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('barchartxaxislabel', 'cardbox'),
                            fontSize: 16,
                        },
                        stacked: true
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('barchartyaxislabel', 'cardbox'),
                            fontSize: 16
                        },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            stepSize: calculateStepSize(CARDBOX_STATUS),
                        },
                        stacked: true
                    }]
                }
           }
       });
   }

    /**
     * Function builds and displays a line graph that shows the user's
     * past performances in practicing with the current cardbox.
     * 
     * @returns {undefined}
     */
    function displayUserPerformanceOverTime() {
       
        var context = document.getElementById("cardbox-statistics-progress-over-time").getContext("2d");

        var userdata = {

            // These labels appear in the legend and in the tooltips when hovering different arcs.
            labels: __performance.dates,

            datasets: [{
                 label: M.util.get_string('performance', 'cardbox'),
                 data: __performance.performances,
                 backgroundColor: '#0066ff', // '#0066ff'
                 borderColor: '#0066ff', // specifies the line color
                 borderCapStyle: 'butt', // no change
                 borderDash: [], // no change
                 borderDashOffset: 0.0, // no change
                 borderJoinStyle: 'miter', // no change
                 pointBorderColor: "#0066ff",
                 pointBackgroundColor: "#0066ff",
                 pointBorderWidth: 1,
                 pointHoverRadius: 5,
                 pointHoverBackgroundColor: "#0066ff",
                 pointHoverBorderColor: "#0066ff",
                 pointHoverBorderWidth: 2,
                 pointRadius: 1,
                 pointHitRadius: 10,
                 spanGaps: false,
                 fill: false,
                 lineTension: 0                
            }]

        };

        var lineGraph = new Chart(context, {
             type: 'line',
             data: userdata,
             options: {
                 title: {
                    display: true,
                    text: M.util.get_string('titleperformancechart', 'cardbox'),
                    fontSize: 16,
                    position: 'top'
                },
                legend: {
                    display: false
                },
                lineTension: 0,
                elements: {
                    line: {
                        tension: 0 // dots are connected with straight lines instead of interpolation.
                    }
                },
                ticks: {
                    beginAtZero: true,
                    min: 0,
                    max: 100 // no effect
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphxaxislabel', 'cardbox'),
                            fontSize: 16
                        },
                    }],
                    yAxes: [{
                        stacked: true,
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphyaxislabel_performance', 'cardbox'),
                            fontSize: 16
                        },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            stepSize: 10
                        }
                    }]
                }
            }
        });

   }

   function displayNumberOfCardsOverTime() {
       
        var context = document.getElementById("cardbox-statistics-number-of-cards").getContext("2d");

        var userdata = {

            // These labels appear in the legend and in the tooltips when hovering different arcs.
            labels: __performance.weeks,

            datasets: [
                {
                    label: M.util.get_string('numberofcardsmin', 'cardbox'),                     //Min
                    data: __performance.numberofcardsmin,
                    tooltiplabels: __performance.tooltips.numberofcards.min,
                    backgroundColor: '#0066ff', // '#0066ff'
                    borderColor: '#0066ff', // specifies the line color
                    borderCapStyle: 'butt', // no change
                    borderDash: [], // no change
                    borderDashOffset: 0.0, // no change
                    borderJoinStyle: 'miter', // no change
                    pointBorderColor: "#0066ff",
                    pointBackgroundColor: "#0066ff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#0066ff",
                    pointHoverBorderColor: "#0066ff",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    pointStyle: 'line',
                    spanGaps: false,
                    fill: false,
                    lineTension: 0                
                },
                {
                label: M.util.get_string('numberofcardsavg', 'cardbox'),                      //Average
                data: __performance.numberofcardsavg,
                tooltiplabels: __performance.tooltips.numberofcards.average,
                backgroundColor: '#9C9E9F', // '#9C9E9F'
                borderColor: '#9C9E9F', // specifies the line color
                borderCapStyle: 'butt', // no change
                borderDash: [5, 5], 
                borderDashOffset: 0.0, // no change
                borderJoinStyle: 'miter', // no change
                pointBorderColor: "#9C9E9F",
                pointBackgroundColor: "#9C9E9F",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#9C9E9F",
                pointHoverBorderColor: "#9C9E9F",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                pointStyle: 'line',
                spanGaps: false,
                fill: false,
                lineTension: 0                
            },
            {
                label: M.util.get_string('numberofcardsmax', 'cardbox'),                      //Max
                data: __performance.numberofcardsmax,
                tooltiplabels: __performance.tooltips.numberofcards.max,
                backgroundColor: '#57AB27', // '#57AB27'
                borderColor: '#57AB27', // specifies the line color
                borderCapStyle: 'butt', // no change
                borderDash: [], // no change
                borderDashOffset: 0.0, // no change
                borderJoinStyle: 'miter', // no change
                pointBorderColor: "#57AB27",
                pointBackgroundColor: "#57AB27",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#57AB27",
                pointHoverBorderColor: "#57AB27",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                pointStyle: 'line',
                spanGaps: false,
                fill: false,
                lineTension: 0                
            }
        ]

        };

        var lineGraph = new Chart(context, {
            type: 'line',
            data: userdata,
            options: {
                title: {
                    display: true,
                    text: M.util.get_string('titlenumberofcards', 'cardbox'),
                    fontSize: 16,
                    position: 'top'
                },
                legend: {
                    labels: {
                        usePointStyle: true,
                    },
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].tooltiplabels[tooltipItem.index]
                        }
                    }
                },
                lineTension: 0,
                elements: {
                    line: {
                        tension: 0 // dots are connected with straight lines instead of interpolation.
                    }
                },
                ticks: {
                    beginAtZero: true,
                    min: 0,
/*                     max: 100 // no effect */
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphxaxislabel', 'cardbox'),
                            fontSize: 16
                        },
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: false,
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphyaxislabel_numbercards', 'cardbox'),
                            fontSize: 16
                        },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
/*                             max: 100, */
                            stepSize: calculateStepSize(CARDS_OVER_TIME)
                        }
                    }]
                }
            }
        });

    }

    function displayDurationOfASessionOverTime() {
       
        var context = document.getElementById("cardbox-statistics-duration-of-a-session").getContext("2d");

        var userdata = {

            // These labels appear in the legend and in the tooltips when hovering different arcs.
            labels: __performance.weeks,

            datasets: [
                {
                    label: M.util.get_string('durationmin', 'cardbox'),                              //Min
                    data: __performance.durationofsessionmin,
                    tooltiplabels: __performance.tooltips.durationofsession.min,
                    backgroundColor: '#0066ff', // '#0066ff'
                    borderColor: '#0066ff', // specifies the line color
                    borderCapStyle: 'butt', // no change
                    borderDash: [], // no change
                    borderDashOffset: 0.0, // no change
                    borderJoinStyle: 'miter', // no change
                    pointBorderColor: "#0066ff",
                    pointBackgroundColor: "#0066ff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "#0066ff",
                    pointHoverBorderColor: "#0066ff",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    pointStyle: 'line',
                    spanGaps: false,
                    fill: false,
                    lineTension: 0                
                },
                {
                label: M.util.get_string('durationavg', 'cardbox'),                             //Average
                data: __performance.durationofsessionavg,
                tooltiplabels: __performance.tooltips.durationofsession.average,
                backgroundColor: '#9C9E9F', // '#9C9E9F'
                borderColor: '#9C9E9F', // specifies the line color
                borderCapStyle: 'butt', // no change
                borderDash: [5, 5],
                borderDashOffset: 0.0, // no change
                borderJoinStyle: 'miter', // no change
                pointBorderColor: "#9C9E9F",
                pointBackgroundColor: "#9C9E9F",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#9C9E9F",
                pointHoverBorderColor: "#9C9E9F",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                pointStyle: 'line',
                spanGaps: false,
                fill: false,
                lineTension: 0                
            },
            {
                label: M.util.get_string('durationmax', 'cardbox'),                                //Max
                data: __performance.durationofsessionmax,
                tooltiplabels: __performance.tooltips.durationofsession.max,
                backgroundColor: '#57AB27', // '#57AB27'
                borderColor: '#57AB27', // specifies the line color
                borderCapStyle: 'butt', // no change
                borderDash: [], // no change
                borderDashOffset: 0.0, // no change
                borderJoinStyle: 'miter', // no change
                pointBorderColor: "#57AB27",
                pointBackgroundColor: "#57AB27",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "#57AB27",
                pointHoverBorderColor: "#57AB27",
                pointHoverBorderWidth: 2,
                pointRadius: 1,
                pointHitRadius: 10,
                pointStyle: 'line',
                spanGaps: false,
                fill: false,
                lineTension: 0                
            }
        ]

        };

        var lineGraph = new Chart(context, {
            type: 'line',
            data: userdata,
            options: {
                title: {
                    display: true,
                    text: M.util.get_string('titledurationofasession', 'cardbox'),
                    fontSize: 16,
                    position: 'top'
                },
                legend: {
                    display: true,
                    labels: {
                        usePointStyle: true,
                    },
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return data.datasets[tooltipItem.datasetIndex].tooltiplabels[tooltipItem.index]
                        }
                    }
                },
                lineTension: 0,
                elements: {
                    line: {
                        tension: 0 // dots are connected with straight lines instead of interpolation.
                    }
                },
                ticks: {
                    beginAtZero: true,
                    min: 0,
/*                     max: 100 // no effect */
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphxaxislabel', 'cardbox'),
                            fontSize: 16
                        },
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: false,
                        scaleLabel: {
                            display: true,
                            labelString: M.util.get_string('linegraphyaxislabel_duration', 'cardbox'),
                            fontSize: 16
                        },
                        ticks: {
                            beginAtZero: true,
                            min: 0,
/*                             max: 100, */
                            stepSize: 10
                        }
                    }]
                }
            }
        });

    }

    function calculateStepSize(chartname) {
        switch (chartname) {
            case ABSOLUTE_CARDS_OVER_DECK:
                var __absoluteboxcount = __params['absoluteboxcount'];
                var values = Object.values(__absoluteboxcount);
                var max = Math.max(...values);
                var stepsize = compareStepSize(max);
                break;
            case CARDBOX_STATUS:
                var __studentboxcount = __params['studentboxcount'];
                var max = __studentboxcount[0];
                for (let i = 1; i < 6; i++) {
                    max = Math.max(max, __studentboxcount[i]['due'], __studentboxcount[i]['notdue']);
                }
                max = Math.max(max, __studentboxcount[6]);

                var __performance = __params['performance'];
                if (__performance.displayaverageprogress) {
                    var __averageboxcount = __params['averageboxcount'];
                    var avgvalues = Object.values(__averageboxcount);
                    max = Math.max(max, ...avgvalues);
                }

                var stepsize = compareStepSize(max);
                break;
            case USER_PERFORMANCE_OVER_TIME:
                var stepsize = 10;
                break;
            case CARDS_OVER_TIME:
                var stepsize = compareStepSize(__performance.numberofcardsmax);
                break;
            case SESSION_DURATION:
                var stepsize = compareStepSize(__performance.durationofsessionmax);
                break;
        }
        return stepsize;
    }
    function compareStepSize(max) {
        var stepsize = 10;
        if (max <= 10) {
            stepsize = 1;
        } else if (max <= 100) {
            stepsize = 10;
        } else if (max <= 200) {
            stepsize = 20;
        } else if (max <= 500) {
            stepsize = 50;
        } else if (max <= 1000) {
            stepsize = 100;
        } else if (max <= 2000) {
            stepsize = 200;
        } else if (max <= 5000) {
            stepsize = 500;
        } else if (max <= 10000) {
            stepsize = 1000;
        } else if (max <= 20000) {
            stepsize = 2000;
        } else if (max <= 50000) {
            stepsize = 5000;
        } else {
            stepsize = 10000;
        }
        return stepsize;
    }

} // end of displayCharts()
