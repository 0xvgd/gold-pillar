<template>
  <range-slider
    class="slider"
    v-bind:min="minValue"
    v-bind:max="maxValue"
    v-bind:step="step"
    v-on:input="valueChanged"
    v-model="sliderValue"
  ></range-slider>
</template>

<script>
import RangeSlider from "vue-range-slider";
// you probably need to import built-in style
import "vue-range-slider/dist/vue-range-slider.css";

export default {
  data() {
    return {
      sliderValue: 0
    };
  },
  props: {
    percentInput: String,
    percentLabel: String,
    minValue:String,
    maxValue:String,
    purchasePrice:String,
    step:String
  },
  methods: {
    valueChanged: function() {
       var percent = (this.sliderValue * 100 / (this.purchasePrice * 1 ));
       percent = parseFloat(Math.round(percent * 100) / 100).toFixed(2);
       percent = percent > 100 ? 100 : percent;
       document.getElementById(this.percentInput).value = this.sliderValue;
       $('#'+this.percentLabel).text(percent + '%');
    },
  },
  components: {
    RangeSlider
  }
};
</script>

<style>
.slider {
  /* overwrite slider styles */
  width: 100%;
}
</style>