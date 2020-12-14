import '../../../css/booking.scss'
import '../../../css/selling.scss'

import Vue from 'vue'
import moment from '../../utils/moment'
import { CurrencyInput } from 'vue-currency-input'
import ReviewStars from '../../components/ReviewStars.vue'
import transFilter from 'vue-trans';
Vue.use(transFilter)

Vue.filter('relativeTime', (dt) => {
    if(dt == null)
        return 'N/A';
    return moment(dt).fromNow(true)
})

Vue.filter('currency', (value) => {
    const formatter = new Intl.NumberFormat('GBP', {
        style: 'currency',
        currency: 'GBP'
    });
    return formatter.format(value)
})

new Vue({
    el: '#app',
    components: {
        CurrencyInput,
        ReviewStars,
    },
    data() {
        return {
            rentValue: 0,
            findTenantFee: 0,
            findOnlyCharge: 0,
            rentCollectCharge: 0,
            fullyManagedCharge: 0,
            agent: null,
            agents: [],
            plan: '',
            loading: false,
            showAgents: false,
            showAgent: false,
            authenticated: false,
            //new logic
            queryPostCode: '',
            queryPostCodePending: false,
            pendingPostCode: false,
            resultInfo: {},
            queryStatus: true,
            currentStep:0,
            addresses:[],
            currentAddr:{},
            days:[],
            monthValue:'',
            selectedDay: {},
            selectedHour:{},
            activeIndex:0,
            bookHours:[],
            loadingHour:false,
            loadingDay:false,
            isSubmitted:false,
            errors:[],
            resultMsg:'',
            login:{email:'',password:''},
            register:{email:'',password:'',conf:'',name:'',phone:'',address1:'',address2:'',postcode:'',city:'',country:''},
        }
    },
    computed: {
        yearly() {
            return this.rentValue * 12
        },
        findOnlyFee() {
            return this.yearly * this.findOnlyCharge + this.findTenantFee
        },
        rentCollectFee() {
            return this.rentValue * this.rentCollectCharge
        },
        fullyManagedFee() {
            return this.rentValue * this.fullyManagedCharge
        },
    },
    methods: {
        checkPostCodes() {
            if (this.queryPostCode === "" || this.pendingPostCode === true)
                return false;
            this.queryPostCodePending = true;
        },
        fetchPostCodes(flag = false) {
            this.queryPostCodePending = false;
            if(flag)
                return true;
            const url = this.$el.dataset.queryCodeUrl;
            if (this.queryPostCode === "" || this.pendingPostCode === true)
                return false;
            this.pendingPostCode = true;
            $.ajax({
                url: url,
                data:{q:this.queryPostCode},
                success: (resp) => {
                    this.queryStatus = resp.status;
                    this.resultInfo = resp.result;
                    this.addresses = resp.addresses;
                    this.rentValue = this.resultInfo.price
                },
                complete: () => {
                    this.pendingPostCode = false;
                    this.currentStep = 1;
                    setTimeout(() => {
                        window.scrollTo(0, $(this.$refs.addr).offset().top - 160)
                    }, 400)
                }
            })
        },

        bindAddress(addr) {
            this.currentAddr = addr;
        },
        choosePlan(plan) {
            this.plan = plan
        },
        fetchAgents() {
            this.showAgent = false
            this.showAgents = false
            this.loading = true
            $.ajax({
                url: this.$el.dataset.agentsUrl,
                success: (response) => {
                    this.agents = response
                    this.showAgents = true
                },
                complete: () => {
                    this.loading = false
                    setTimeout(() => {
                        window.scrollTo(0, $(this.$refs.agents).offset().top - 160)
                    }, 400)
                }
            })
        },
        chooseAgent(agent) {
            this.showAgents = false
            this.showAgent = true
            this.agent = agent
            setTimeout(() => {
                window.scrollTo(0, $(this.$refs.agent).offset().top - 160)
                this.fetchDates()
            }, 400)
        },
        fetchDates() {
            this.loadingDay = true;
            if (this.agent) {
                $.ajax({
                    url: `/booking/agents/${this.agent.id}/days`,
                    success: (resp) => {
                        this.days = resp;
                        this.monthValue = this.days[0].name;
                        this.loadingDay = false;
                    }
                })
            }
        },
        carouselAction(val){
            this.activeIndex = this.activeIndex + val;
            if(this.activeIndex > this.days.length-1)
                this.activeIndex = 0;
            if(this.activeIndex < 0)
                this.activeIndex = this.days.length-1;
            this.monthValue = this.days[this.activeIndex].name;
        },
        fetchTimes(date,index_date,index_p) {
            this.selectedDay = date;
            this.selectedDay.index_date = index_date;
            this.selectedDay.index_p = index_p;
            this.loadingHour = true;
            if (date) {
                $.ajax({
                    url: `/booking/agents/${this.agent.id}/days/${date.value}`,
                    success: (response) => {
                        this.bookHours = response;
                        this.loadingHour = false;
                    }
                })
            }
        },
        bindBookHour(hour,index_time,index_p){
            this.selectedHour = hour;
            this.selectedHour.index_time = index_time;
            this.selectedHour.index_p = index_p;
        },
        bookingSubmit(field = 'auth') {
            const forms = this.gettingBookingValue();
            let data;
            if (!forms)
                return false;
            if (field === 'login') {
                if(!this.validCheck(this.login, field))
                    return false;
                data = {...forms, ...this.login}
            }
            else if (field === 'register') {
                this.register.address1 = this.$refs.regAddr1.value;
                this.register.address2 = this.$refs.regAddr2.value;
                this.register.postcode = this.$refs.regCode.value;
                this.register.city = this.$refs.regCity.value;
                this.register.country = this.$refs.regCountry.value;
                if(!this.validCheck(this.register, field))
                    return false;
                data = {...forms, ...this.register}
            }
            data.field = field
            this.isSubmitted = true
            $.ajax({
                url: this.$el.dataset.saveUrl,
                data: data,
                method: 'POST',
                success: (response) => {
                    this.isSubmitted = false;
                    this.resultMsg = response.msg;
                    setTimeout(() => {
                        this.resultMsg = ''
                    }, 2000)
                    if(response.result === true)
                        window.location.href = '/';
                }
            })
        },
        bookingStep(value){
            this.currentStep = value;
        },
        changeDate(e) {
            const date = e.currentTarget.value
            this.fetchTimes(date)
        },
        gettingBookingValue(){
            let values = {};
            if(!this.agent || !this.selectedDay || !this.selectedHour || this.valuation < 0 || !this.currentAddr)
                return false;
            values = {agent:this.agent.id,date:this.selectedDay.value,hour:this.selectedHour.val,plan:this.plan,
                price:this.rentValue,addr:this.currentAddr.addr,city:this.currentAddr.city,county:this.currentAddr.county,code:this.queryPostCode};
            return values;
        },
        validCheck(values,field = 'register'){
            let errors = {};
            const emailReg =  /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/;
            if (!values.email) {
                errors.email = "Required";
            } else if (!emailReg.test(values.email)) {
                errors.email = "Invalid";
            }
            if (!values.password) {
                errors.password = "Required";
            }
            this.errors = errors;
            if(field === 'login')
                return Object.keys(errors).length === 0;
            if (values.password.length < 6) {
                errors.password = "Password must be 6 characters long.";
            }
            if (!values.conf) {
                errors.conf = "Required";
            }
            if (values.password !== values.conf) {
                errors.conf = "Password not matched";
            }
            if (!values.name) {
                errors.name = "Required";
            }
            if (!values.phone) {
                errors.phone = "Required";
            }
            if (!values.address1) {
                errors.address1 = "Required";
            }
            if (!values.address2) {
                errors.address2 = "Required";
            }
            if (!values.city) {
                errors.city = "Required";
            }
            if (!values.postcode) {
                errors.postcode = "Required";
            }
            if (!values.country) {
                errors.country = "Required";
            }
            this.errors = errors;
            return Object.keys(errors).length === 0;
        },
    },
    mounted() {
        this.findTenantFee = parseFloat(this.$el.dataset.findTenantFee)
        this.findOnlyCharge = parseFloat(this.$el.dataset.findOnlyCharge)
        this.rentCollectCharge = parseFloat(this.$el.dataset.rentCollectCharge)
        this.fullyManagedCharge = parseFloat(this.$el.dataset.fullyManagedCharge)
        this.authenticated = this.$el.dataset.authenticated === '1'

        /*if (this.$el.dataset.agent) {
            $.ajax({
                url: this.$el.dataset.agentsUrl + '/' + this.$el.dataset.agent,
                success: (response) => {
                    this.agent = response
                    this.showAgent = true
                    this.showAgents = false
                }
            })
        }*/

        // override default behavior (window.reload)
        /*$('#sign-in-modal form')
            .off('sign-in-success')
            .on('sign-in-success', () => {
                this.authenticated = true
                $('#sign-in-modal').modal('hide')
            })*/
    }
})
