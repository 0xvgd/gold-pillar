<template>
  <div class="item-container">
    <div class="item-container-wrapper">
      <div class="item-image-box w-100" v-on:click="goToProperty()">
        <v-lazy-image
          class="w-100"
          :src="item.mainPhoto ? item.mainPhoto + '/thumb:335*197*outbound' : '/images/home/loading.gif'"
          src-placeholder="/images/home/loading.gif"
        />
        <span class="item-flag aa">{{ item.tag ? item.tag : '-' }}</span>
        <span class="favorite">
          <button type="button" class="btn btn-primary-outline">
            <i class="heart far fa-heart"></i>
          </button>
        </span>
        <span v-if="item.status.value == 'reserved'" class="stamp is-sold">{{ 'Reserved' | trans }}</span>
        <span v-if="item.status.value == 'rented'" class="stamp is-sold">{{ 'Rented' | trans }}</span>
        <div class="item-type">{{ item.propertyType.label | trans }}</div>
      </div>
      <div class="item-description text-left p-2">
        <h4>{{ item.name }}</h4>
        <div class="item-itemCode">
          <small>{{ item.referenceCode }}</small>
        </div>
      </div>
      <hr>
      <div class="row">
        <div class="col-md-4 col-4 text-center" >
          <i aria-hidden="true" class="fa fa-bed"></i>
          {{ 'bedrooms' | trans }}
          <p>{{ item.bedrooms ? item.bedrooms : '-' }}</p>
        </div>
          <div class="col-md-4 col-4 text-center">
            <i aria-hidden="true" class="fa fa-bath"></i>
            {{ 'bathrooms' | trans }}
            <p>{{ item.bathrooms ? item.bathrooms : '-' }}</p>
        </div>
        <div class="col-md-4 col-4 text-center">
            <i class="fas fa-ruler-horizontal"></i>
            <p>{{ item.squareFoot ? item.squareFoot + ' sq. ft.' : '-' }}</p>
        </div>
      </div>

      <div class="item-button button-generic text-center p-2" v-on:click="goToProperty()" v-if="item.slug">
        <strong>
          <span>{{ 'VIEW PROPERTY' | trans }}</span>
        </strong>
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
    item: {
      required: true
    }
  },
  methods: {
    goToProperty: function() {
      if (this.item.slug) {
        window.location.href = this.url;
      }
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
