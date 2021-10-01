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

function displayCharts(Y, __cmid, __boxcount, __performance, __ismanager) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'chartjs'], function ($, templates, chart) {
    
        if(__ismanager) {
            displayAverageCardsOverDecks();
            displayNumberOfCardsOverTime();
            displayDurationOfASessionOverTime();

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
   function displayAverageCardsOverDecks() {

    var context = document.getElementById("cardbox-statistics-average-over-deck").getContext("2d");
    var mixedChart = new Chart(context, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Absolute number of cards',
                backgroundColor: [
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#00b33c'
            ],
                data: [__boxcount[0]['abs'], __boxcount[1]['abs'], __boxcount[2]['abs'], __boxcount[3]['abs'], __boxcount[4]['abs'], __boxcount[5]['abs'], __boxcount[6]['abs']]
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
                text: M.util.get_string('barchartstatistic1', 'cardbox')
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
                        stepSize: 10
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
                data: [__boxcount[0], __boxcount[1]['due'], __boxcount[2]['due'], __boxcount[3]['due'], __boxcount[4]['due'], __boxcount[5]['due'], 0],
                backgroundColor: [
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#0066ff',
                        '#00b33c'
                ]
            },
            {
                label: M.util.get_string('flashcardsnotdue', 'cardbox'),
                data: [0, __boxcount[1]['notdue'], __boxcount[2]['notdue'], __boxcount[3]['notdue'], __boxcount[4]['notdue'], __boxcount[5]['notdue'], __boxcount[6]],
                backgroundColor: [
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#99c2ff',
                        '#00b33c'
                ]
           }]

       };

       var barChart = new Chart(context, {
           type: 'bar',
           data: cardboxdata,
           options: {
               title: {
                   display: true,
                   text: M.util.get_string('titleoverviewchart', 'cardbox'),
                   fontSize: 16,
                   position: 'top'
               },
               legend: {
                   display: true,
                   position: 'right'
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
                            stepSize: 10
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

            datasets: [{
                label: M.util.get_string('numberofcards', 'cardbox'),
                data: __performance.numberofcards,
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
                    text: M.util.get_string('titlenumberofcards', 'cardbox'),
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
                            labelString: M.util.get_string('linegraphyaxislabel_numbercards', 'cardbox'),
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

    function displayDurationOfASessionOverTime() {
       
        var context = document.getElementById("cardbox-statistics-duration-of-a-session").getContext("2d");

        var userdata = {

            // These labels appear in the legend and in the tooltips when hovering different arcs.
            labels: __performance.weeks,

            datasets: [{
                label: M.util.get_string('duration', 'cardbox'),
                data: __performance.durationofsession,
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
                    text: M.util.get_string('titledurationofasession', 'cardbox'),
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
                            labelString: M.util.get_string('linegraphyaxislabel_duration', 'cardbox'),
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

} // end of displayCharts()