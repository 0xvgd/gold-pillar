import Vue from 'vue'
import VueClipboard from 'vue-clipboard2'

import Chart from 'chart.js';
import { COLORS } from '../constants/colors';

Vue.use(VueClipboard)

export default (function () {

  var app = new Vue({
    el: '#app',
    data: function () {
      return {
        bringAgentUrl: bringAgentUrl,
        bringInvestorUrl: bringInvestorUrl,
        showModal: false
      }
    },
    methods: {
      onCopy: function (e) {
      },
      onError: function (e) {
        alert('Failed to copy texts')
      }
    },
    computed: {

    }
  });


  // ------------------------------------------------------
  // @Line Charts
  // ------------------------------------------------------

  const lineChartBox = document.getElementById('line-chart');

  if (lineChartBox) {
    const lineCtx = lineChartBox.getContext('2d');
    lineChartBox.height = 80;

    new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: [salesByMonth[0].month, salesByMonth[1].month, salesByMonth[2].month, salesByMonth[3].month, salesByMonth[4].month,salesByMonth[5].month],
        datasets: [{
          label                : 'Sales',
          backgroundColor      : 'rgba(237, 231, 246, 0.5)',
          borderColor          : COLORS['deep-purple-500'],
          pointBackgroundColor : COLORS['deep-purple-700'],
          borderWidth          : 2,
          data                 : [salesByMonth[0].sales, salesByMonth[1].sales, salesByMonth[2].sales, salesByMonth[3].sales, salesByMonth[4].sales,salesByMonth[5].sales],
        }],
      },

      options: {
        legend: {
          display: false,
        },
      },

    });
  }

}())
