<template>
  <div class="item-container">
    <div class="item-container-wrapper">
      <div class="item-image-box w-100" v-on:click="goToOffer()">
        <v-lazy-image
          class="w-100"
          :src="
            offeritem.propertyPhoto
              ? offeritem.propertyPhoto + '/thumb:335*197*outbound'
              : '#'
          "
          src-placeholder="/images/home/loading.gif"
        />
        <span class="item-flag aa">{{ offeritem.tag }}</span>
       
        <div class="item-type">{{ offeritem.propertyType.label }}</div>
      </div>
      <div class="item-description text-left p-2">
        <h4>{{ offeritem.name }}</h4>
        <div>
          <small>Created at {{ getFormatedDate() }}</small>
        </div>
      </div>
      <hr />
      <div class="row">
        <div class="col-md-12">
          <table class="table table-bordered table-values">
            <tbody>
              <tr>
                <td><strong>{{ 'Original Value' | trans }}</strong></td>
                <td class="text-right">
                  {{
                    offeritem.originalPrice
                      | currency("£")
                  }}
                </td>
              </tr>
              <tr
                v-bind:class="{
                  offerdeclined: offeritem.counterOfferValue > 0
                }"
              >
                <td><strong>{{ 'Offer' | trans }}</strong></td>
                <td class="text-right">
                  {{
                    offeritem.offerValue
                      | currency("£")
                  }}
                </td>
              </tr>
              <tr>
                <td><strong>{{ 'How many people want to move in?' | trans }}</strong></td>
                <td class="text-right">
                  {{
                    offeritem.peopleCount
                  }}
                </td>
              </tr>
              <tr>
                <td><strong>{{ 'Offer status' | trans }}</strong></td>
                <td class="text-right">{{ offeritem.status.label }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="d-flex">
            <div>
              <img
                :src="offeritem.agentAvatar || defaultPhotoUrl"
                alt=""
                style="width: 100px; margin-botton: 5px;"
              />
            </div>
            <div class="p-2">
              <a :title="offeritem.agentName" rel="nofollow" href="#">
                <strong>{{ offeritem.agentName }}</strong>
              </a>
              <a :title="offeritem.agentEmail" rel="nofollow" href="#">
                <strong>{{ offeritem.agentEmail }}</strong>
              </a>
              <a
                :href="'tel:' + offeritem.agentPhone"
                class="branch-telephone-number"
              >
                <strong>{{ offeritem.agentPhone }}</strong>
              </a>
            </div>
          </div>
        </div>
      </div>
      <hr style="margin-top: 5px" />

      <table v-if="offeritem.counterOfferValue > 0" class="table table-bordered table-values">
        <tbody>
          <tr>
            <td><strong> {{ 'Counter offer' | trans }}</strong></td>
            <td class="text-right">
              {{
                offeritem.counterOfferValue
                  | currency("£")
              }}
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="offeritem.counterOfferValue > 0 && !offeritem.counterOfferAcceptedAt" class="row">
        <div class="col-md-6">
          <form :action="acceptUrl" method="post">
            <button
              type="submit"
              class="btn btn-success btn-block btn-accept"
            >
              <span class="fa fa-handshake"></span>
              Accept
            </button>
          </form>
        </div>
        <div class="col-md-6">
          <form id="form-decline" :action="declineUrl" method="post">
            <button
              type="submit"
              class="btn btn-warning btn-block btn-decline"
            >
              <span class="fa fa-ban"></span>
              Decline
            </button>
          </form>
        </div>
      </div>

      <hr style="margin-top: 5px" />
      <form id="form-delete" :action="deleteUrl" method="post">
        <a :href="reserveUrl" class="btn btn-primary btn-block">
				  To reserve
				</a>
        <button
          type="submit"
          class="btn btn-danger btn-block"
          name="_method"
          value="DELETE"
        >
          <span class="fa fa-trash"></span>
          Remove Offer
        </button>
      </form>
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
  margin-top: 0;
}

.item-button {
  cursor: pointer;
  grid-area: itemButton;
  border: solid 1px #cb9900;
  color: black;
  background-color: rgba(255, 205, 53, 0.14);
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

.offerdeclined {
  text-decoration: line-through;
  color: #ccc;
}
</style>

<script>
import VLazyImage from "v-lazy-image";
import moment from '../utils/moment'


export default {
  components: {
    VLazyImage
  },
  props: {
    offeritem: {
      required: true
    },
    baseUrl: {
      type: String
    }
  },
  methods: {
    goToOffer: function() {
      window.location.href = "#"; //this.url;
    },
    getFormatedDate: function() {
      return moment(this.offeritem.createdAt).format('DD/MM/YYYY h:mm:ss')
    }
  },
  computed: {
    url: function() {
      var str = ["/url/to/offer/", this.offeritem.id, "/view"].join("");

      return str;
    },
    reserveUrl: function() {
      var str = [this.baseUrl, "renting/", this.offeritem.resourceSlug, "/reserve"].join("");

      return str;
    },
    deleteUrl: function() {
      debugger;
      var str = [
        this.baseUrl,
        "dashboard/tenant/offers/delete/",
        this.offeritem.id
      ].join("");

      return str;
    },
    acceptUrl: function() {
      var str = [
        this.baseUrl,
        "dashboard/tenant/offers/accept/",
        this.offeritem.id
      ].join("");

      return str;
    },
    declineUrl: function() {
      var str = [
       this.baseUrl,
        "dashboard/tenant/offers/decline/",
        this.offeritem.id
      ].join("");

      return str;
    },
    defaultPhotoUrl: function() {
      var str = [this.base, "/images/nobody_m.original.jpg"].join("");

      return str;
    }
  }
};
</script>
