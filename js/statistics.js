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

function displayCharts(Y, __cmid, __boxcount) { // Wrapper function that is called by controller.php

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

       var boxlabel = M.util.get_string('box', 'cardbox');

       var cardboxdata = {

           // These labels appear in the legend and in the tooltips when hovering different arcs.
           labels: [
               M.util.get_string('new', 'cardbox'),
               boxlabel + ' 1',
               boxlabel + ' 2',
               boxlabel + ' 3',
               boxlabel + ' 4',
               boxlabel + ' 5'
           ],

           datasets: [{
               label: M.util.get_string('flashcards', 'cardbox'),
               data: [__boxcount[0], __boxcount[1], __boxcount[2], __boxcount[3], __boxcount[4], __boxcount[5]],
               backgroundColor: '#0066ff'
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
                   position: 'bottom'
               }//,
   //                    barPercentage: 1,
   //                    categoryPercentage: 1
           }
       });
   }

   function displayUserPerformanceOverTime() {
       
       var context = document.getElementById("cardbox-statistics-progress-over-time").getContext("2d");

//       var boxlabel = M.util.get_string('box', 'cardbox');
       
       var userdata = {

           // These labels appear in the legend and in the tooltips when hovering different arcs.
           labels: [
               '1.1.2019',
               '1.2.2019',
               '1.3.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019',
               '1.4.2019'
           ],

           datasets: [{
                label: M.util.get_string('performance', 'cardbox'),
                data: [35, 40, 45, 43, 55, 60, 62, 60, 65, 58, 70, 68, 60, 67, 70, 73, 76, 74, 78, 80, 77, 79, 42, 55, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80],
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
//            datasets: [
//            {
//                label: "My First dataset",
//                fill: false,
//                lineTension: 0,
//                backgroundColor: "rgba(75,192,192,0.4)",
//                borderColor: "rgba(75,192,192,1)",
//                borderCapStyle: 'butt',
//                borderDash: [],
//                borderDashOffset: 0.0,
//                borderJoinStyle: 'miter',
//                pointBorderColor: "rgba(75,192,192,1)",
//                pointBackgroundColor: "#fff",
//                pointBorderWidth: 1,
//                pointHoverRadius: 5,
//                pointHoverBackgroundColor: "rgba(75,192,192,1)",
//                pointHoverBorderColor: "rgba(220,220,220,1)",
//                pointHoverBorderWidth: 2,
//                pointRadius: 1,
//                pointHitRadius: 10,
//                data: [65, 59, 80, 81, 56, 55, 40],
//                spanGaps: false,
//            }
//        ]
//
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
                   position: 'bottom'
               },
               lineTension: 0,
               elements: {
                   line: {
                       tension: 0
                   }
               }
            }
        });

   }


} // end of displayCharts()