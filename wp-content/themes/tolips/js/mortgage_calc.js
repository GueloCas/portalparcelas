// https://codepen.io/scorch/pen/oZLLbv
function formatAsCurrency (value, dec) {
  dec = dec || 0
  return '$' + parseFloat(value).toFixed(dec).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
};

Vue.component('custominput', {
   template: '#custominput',
   props: {
      value: {type: Number, default: 0},
      min: {type: Number, default: 0},
      max: {type: Number, default: 100000000},
      step: {type: Number, default: 10000},
      decimals: {type: Number, default: 0},
      type: {default: 'currency'}
   },
   data: function () {
      return {
         active: false,
         _value: null
      }
   },
   computed: {
      val: function () {
         if (this.type === 'currency') {
           return Number(this.value) + ''
         }
         else if (this.type === 'years') {
           return Number(this.value) + ''
         }
         else if (this.type === 'percent') {
           return Number(this.value*100).toFixed(3)
         }
      },
      formatted: function () {
         if (this.type === 'currency') {
           return formatAsCurrency(this.value, 0)
         }
         else if (this.type === 'years') {
           return Number(this.value+"".replace(/[^0-9\.]+/g,"")) + ' Años'
         }
         else if (this.type === 'percent') {
           return (this.value*100).toFixed(this.decimals) + '%'
         }
      }
   },
   methods: {
      increment: function (e) {
         if (e.shiftKey) {
           this._value += 10 * this.step
         } else {
           this._value += this.step
         }
         if (this.value > this.max) {
           this._value = this.max
         }
         this.changed()
      },
      decrement: function (e) {
         if (e.shiftKey) {
           this._value -= 10 * this.step
         } else {
           this._value -= this.step
         }
         if (this.value < this.min) {
           this._value = this.min
         }
         this.changed()
      },
      update: function () {
         this.active = false
         var tempVal = this.$el.value + ''
         this._value = Number(tempVal.replace(/[^0-9\.]+/g,""))
         if (this.type === 'percent') this._value /= 100
         this.changed()
      },
      changed: function () {
         this.$emit('input', Number(this._value))
      }
   },
   mounted(){
      this._value = this.value;
   }
})
new Vue({
   el: '#app_mortgage_calc',
   data: {
      homeValue: 350000,
      downpayment: 0,
      interestRate: 0.025,
      amortization: 15,
      paymentPeriod: {
         'Mensual': { npy: 12},
         'Semi-Mensual': { npy: 12 * 2},
         'Quincena': { npy: 365.25 / 7 / 2},
         'Semanalmente': { npy: 365.25 / 7 }
      },
      paymentSelection: 'Mensual',
      graphSelection: null
   },
   created: function(){
    console.log(calc_data.homeValue);
    if (typeof calc_data.homeValue != "undefined"){
      this.homeValue = calc_data.homeValue;
    }
  },
   computed: {
      principal: function () {
         return this.homeValue - this.downpayment
      },
      numPayments: function () {
         return this.amortization * this.numPaymentsPerYear
      },
      numPaymentsPerYear: function () {
         return this.paymentPeriod[this.paymentSelection].npy
      },
      interestPerPayment: function () {
         console.log(this.interestRate/this.numPaymentsPerYear)
         return this.interestRate/this.numPaymentsPerYear
      },
      payment: function () {
         var temp = Math.pow(1 + this.interestPerPayment, this.numPayments)
         var p = this.principal * this.interestPerPayment * temp / (temp - 1)
         return p
      },
      amortizationPayments: function () {
         var yearEndPrincipal = []
         var principal = this.principal
         var interestPortion, yearlyPrincipal
         for (var y = 0; y < this.amortization; y++){
            for (var p = 0; p < this.numPaymentsPerYear; p++){
               interestPortion = principal * this.interestPerPayment // portion of payment paying down interest
               principal = principal - (this.payment - interestPortion)
            }
            principal = principal > 0 ? principal : 0
            yearEndPrincipal.push({
               principal,
               interestPortion
           })
         }
         return yearEndPrincipal
      },
      totalCostOfMortgage: function () {
         return this.payment * this.numPayments
      },
      interestPayed: function () {
         return this.totalCostOfMortgage - this.principal
      },
   },
   methods: {
      formatAsCurrency
   }
})

