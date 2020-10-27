<template>
  <div class="item-container">
    <div class="item-container-wrapper">
      <div class="item-image-box w-100" v-on:click="goToProject()">
        <v-lazy-image
          class="w-100"
          :src="
            item.mainPhoto
              ? item.mainPhoto + '/thumb:335*197*outbound'
              : '/images/home/loading.gif'
          "
          src-placeholder="/images/home/loading.gif"
        />
        <span class="item-flag">{{ item.tag }}</span>
        <span class="favorite">
          <button type="button" class="btn btn-primary-outline">
            <i class="heart far fa-heart"></i>
          </button>
        </span>
        <div class="item-type">{{ item.projectType.label | trans }}</div>
      </div>
      <div class="item-description text-left p-2">
        <h4>{{ item.name }}</h4>
        <div class="item-itemCode">
          <small>#{{ item.projectCode }}</small>
        </div>
      </div>
      <div class="row layer">
        <div class="col-md-12">
          <table class="table table-bordered table-values">
            <tbody>
              <tr>
                <td class="text-left"><strong>GDV</strong></td>
                <td class="text-right">
                  <span v-if="item.salePriceProjection">
                      {{ item.salePriceProjection.amount|currency("£") }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="text-left"><strong>{{ 'Cost of project' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.purchasePrice">
                    {{ item.purchasePrice.amount|currency("£") }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="text-left"><strong>{{ 'Construction cost' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.constructionCost">
                    {{ item.constructionCost.amount | currency('£') }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="text-left"><strong>ROI</strong></td>
                <td class="text-right">
                  <span v-if="item.roi">
                    {{ (item.roi * 100).toFixed(2) }}%
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-12 text-center mt-2" v-if="item.totalInvested">
          {{ 'Total Invested' | trans }}
        </div>
        <div class="col-md-12" v-if="item.totalInvested">
          <div class="progress mT-10">
            <div
              class="progress-bar bgc-light-blue-500"
              role="progressbar"
              aria-valuenow="0"
              alt
              aria-valuemin="0"
              aria-valuemax="100"
              v-bind:style="{
                 width: getPct() + '%'
              }"
            >
              <span class="progress-text text-center overlay">
                {{ (item.totalInvested.amount || 0) | currency("£") }} of
                {{ item.purchasePrice.amount | currency("£") }}
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-12 text-center mb-2"></div>
      </div>
      <div
        class="item-button button-generic text-center p-2"
        v-on:click="goToProject()"
        v-if="item.slug"
      >
        <strong>
          <span>{{ 'VIEW PROJECT' | trans }}</span>
        </strong>
        <br />
        <span class="muted"
          >{{ item.maxInvestmentValue | currency("£") }} {{ 'available' | trans }}</span
        >
      </div>
    </div>
  </div>
</template>

<style lang="scss">
  @import '../../css/product_item.scss';
</style>

<script>
import VLazyImage from "v-lazy-image";

export default {
  components: {
    VLazyImage
  },
  props: {
    base: String,
    item: {
      required: true
    }
  },
  methods: {
    goToProject: function() {
      if (this.item.slug) {
        window.location.href = this.url;
      }
    },
    getPct: function() {
      var pct = 0;
      
			if (this.item.totalInvested.amount > 0 ) {
			  pct = this.item.totalInvested.amount / this.item.purchasePrice.amount * 100;
      }

      return pct;
    }
  },
  computed: {
    url: function() {
      var str = [this.item.slug, "/view"].join(
        ""
      );

      return str;
    }
  }
};
</script>
