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

function displayCharts(Y, __cmid, __boxcount, __performance) { // Wrapper function that is called by controller.php

    console.log('__performance: ', __performance);

    require(['jquery', 'core/templates', 'chartjs'], function ($, templates, chart) {
    
        displayCardboxStatus();
        displayUserPerformanceOverTime();
    
    
    });

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
               label: M.util.get_string('flashcards', 'cardbox'),
               data: [__boxcount[0], __boxcount[1], __boxcount[2], __boxcount[3], __boxcount[4], __boxcount[5], __boxcount[6]],
               backgroundColor: [
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
                    '#0066ff',
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
                   display: false
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
                        }
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
                            stepSize: 1
                        }
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

        var lineChart = new Chart(context, {
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
                            labelString: M.util.get_string('linegraphyaxislabel', 'cardbox'),
                            fontSize: 16
                        }
                    }]
                }
            }
        });

   }

} // end of displayCharts()