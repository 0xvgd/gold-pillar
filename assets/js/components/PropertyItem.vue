<template>
  <div class="item-container">
    <div class="item-container-wrapper">
      <div class="item-image-box w-100" v-on:click="goToProperty()">
        <v-lazy-image
          class="w-100"
          :src="item.mainPhoto ? item.mainPhoto + '/thumb:335*197*outbound' : '/images/home/loading.gif'"
          src-placeholder="/images/home/loading.gif"
        />
        <span class="item-flag aa">{{ item.tag | trans }}</span>
        <span class="favorite">
          <button type="button" class="btn btn-primary-outline">
            <i class="far fa-heart"></i>
          </button>
        </span>
        <span v-if="item.propertyStatus.id == 3" class="stamp is-sold">{{ 'sold' | trans }}</span>
        <div class="item-type">{{ item.propertyType.label | trans }}</div>
      </div>
      <div class="item-description text-left p-2">
        <h4>{{ item.name }}</h4>
        <div><small>{{ item.referenceCode }}</small></div>
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
            <p>{{ item.squareFoot }} {{ item.squareFoot ? 'sq. ft.' : '-' }}</p>
        </div>
      </div>

      <div class="item-button button-generic text-center p-2" v-on:click="goToProperty()" v-if="item.slug">
        <strong>
          <span>{{ 'VIEW PROPERTY' | trans }}</span>
        </strong>
        <br />
        <span class="muted">{{ item.price.amount | currency('£') }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
h4 {
  color: rgb(203, 153, 0);
  margin-bottom: 0px;
  font-size: 1.1rem;
}

hr {
     margin-top:0;
}

.item-container-wrapper {
  border: solid 1px #e7e7e7;
  padding: 12px;
}

.item-image-box {
  cursor: pointer;
  grid-area: itemBanner;
}

.item-description {
  /* min-height: 96px !important; */
}
.item-flag {
  position: absolute;
  top: 24px;
  left: 36px;
  color: black;
  background-color: white;
  padding: 6px 10px;
  border: solid 1px #ccc;
  border-radius: 3px;
}

.item-type {
  position: relative;
  bottom: 48px;
  margin-bottom: -48px;
  padding: 12px;
  text-align: center;
  color: white;
  background: linear-gradient(transparent, black);
}

.v-lazy-image {
  opacity: 0.3;
  transition: opacity 1s;
}
.v-lazy-image-loaded {
  opacity: 1;
}

.stamp {
  z-index: 1;
  background: white;
  position: absolute;
  top: 6.2em;
  right: 39px;
  padding: 0.2em 0.5em;
  text-transform: uppercase;
  border-radius: 0.3rem;
  font-family: "Courier";
}

.is-sold {
  color: #c51829;
  background: white;
  border: 0.1rem solid #c51829;
  -webkit-mask-position: 13rem 6rem;
  transform: rotate(-32deg);
}

.btn-primary-outline {
  background-color: transparent;
  border-color: transparent;
}

.favorite {
  position: absolute;
  top: 5px;
  right: 36px;
  color: #fff;
}

.favorite .btn {
  border-radius: 0;
  padding: 0 3px;
  color: #fff;
  height: 25px;
  width: 28px;
  background-color: rgb(203, 153, 0);
  -webkit-text-stroke: 1px #fff;
}
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
    },
    baseUrl: {
      type: String
    },
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
      var str = [this.baseUrl , this.item.slug, "/view"].join("");
      debugger
      return str;
    }
  }
};
</script>
