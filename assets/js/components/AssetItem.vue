<template>
  <div class="item-container">
    <div class="item-container-wrapper">
      <div class="item-image-box w-100" v-on:click="goToAsset()">
        <v-lazy-image
          class="w-100"
          :src="
            item.mainPhoto
              ? item.mainPhoto + '/thumb:335*197*outbound'
              : '/images/home/loading.gif'
          "
          src-placeholder="/images/home/loading.gif"
        />
        <span class="item-flag aa">{{ item.tag }}</span>
        <span class="favorite">
          <button type="button" class="btn btn-primary-outline">
            <i class="heart far fa-heart"></i>
          </button>
        </span>
        <div class="item-type">{{ item.assetType.label }}</div>
      </div>
      <div class="item-description text-left p-2">
        <h4>{{ item.name }}</h4>
        <div class="item-itemCode">
          <small>{{ item.referenceCode }}</small>
        </div>
      </div>
      <div class="row layer mT-15">
        <div class="col-md-12">
          <table class="table table-bordered table-values">
            <tbody>
               <tr>
                <td><strong>{{ 'Market value' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.marketValue">
                    {{ (item.marketValue.amount || 0) | currency("£") }}
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>{{ 'Equity available' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.lastEquity">
                    {{ (item.lastEquity.price.amount || 0) | currency("£") }}
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>{{ 'Gross yield' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.grossYield">
                    {{ ((item.grossYield || 0) * 100).toFixed(2) }}%
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>{{ 'Year Income' | trans }}</strong></td>
                <td class="text-right">
                  <span v-if="item.yearlyIncome">
                    {{ (item.yearlyIncome || 0) | currency("£") }}
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
              aria-valuemin="0"
              aria-valuemax="100"
              v-bind:style="{
                width: getPct() + '%'
              }"
            > 
            <span class="progress-text text-center overlay">
              {{ (item.totalInvested.amount || 0) | currency("£") }} of
              {{ (item.lastEquity.price.amount || 0) | currency("£") }}
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-12 text-center mb-2">
          
        </div>
      </div>
      <div
        class="item-button button-generic text-center p-2"
        v-on:click="goToAsset()"
        v-if="item.slug"
      >
        <strong>
          <span>{{ 'VIEW ASSET' | trans }}</span>
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
    goToAsset: function() {
      if (this.item.slug) {
        window.location.href = this.url;
      }
    },
    getPct: function() {
      var pct = 0;
      
			if (this.item.totalInvested.amount > 0 ) {
			  pct = this.item.totalInvested.amount / this.item.lastEquity.price.amount * 100;
      }

      return pct;
    }
  },
  computed: {
    url: function() {
      var str = [this.item.slug, "/view"].join("");

      return str;
    }
  }
};
</script>
