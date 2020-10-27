import Vue from 'vue'

import Chart from 'chart.js';
import { COLORS } from '../constants/colors';


export default (function () {
    var app = new Vue({
        el: '#app',
        data: {
            baseUrl: GpApp.baseUrl,
            typeTo: null,
            typeFrom: null,
        },
        methods: {

        }
    });



    // ------------------------------------------------------
    // @Line Charts
    // ------------------------------------------------------

      if ($('#balance-bar-chart').length > 0) {
        $('#balance-bar-chart').sparkline([2, 5, 6, 10, 9, 12, 4, 9], {
          type: 'bar',
          height: '20',
          barWidth: '3',
          resize: true,
          barSpacing: '3',
          barColor: '#9675ce',
          label: ['q','q','q','q','q','q','q','q']
        });
      }
   

}())