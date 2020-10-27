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
    inputLabel: String,
    valueInput:String,
    minValue:String,
    maxValue:String,
    purchasePrice:String,
    step:String
  },
  methods: {
    valueChanged: function() {
       var formatter = new Intl.NumberFormat('en-UK', {
          style: 'currency',
          currency: 'GBP'
        });

       var value = formatter.format(this.sliderValue);
       document.getElementById(this.valueInput).value = this.sliderValue;
       $('#'+this.inputLabel).text(value);
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