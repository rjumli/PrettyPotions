<template>
    <b-modal v-model="showModal" title="View Booking"  style="--vz-modal-width: 600px;" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <div class="d-flex">
            <div class="flex-grow-1">
                <h4>{{this.selected.code}}</h4>
                <div class="hstack gap-3 flex-wrap mt-n1">
                    <div><a href="#" class="text-primary d-block">{{selected.user.profile.firstname}} {{selected.user.profile.lastname}}</a></div>
                    <div class="vr"></div>
                    <div class="text-muted">Date : <span class="text-body fw-medium">{{selected.date}}</span></div>
                    <div class="vr"></div>
                    <div class="text-muted">Status : <span class="fw-medium" :class="selected.status.others">{{selected.status.name}}</span></div>
                </div>
            </div>
            <div class="flex-shrink-0">
            </div>
        </div>
        <hr class="text-muted"/>
        <div class="table-responsive mt-3">
            <table class="table table-nowrap align-middle mb-0">
                <thead class="table-light">
                    <tr class="fs-11">
                        <!-- <th class="text-center" style="width: 3%;">#</th> -->
                        <th style="width: 30%;">Service</th>
                        <th style="width: 15%;" class="text-end">Price</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-responsive" style="max-height: 150px; overflow: auto;">
            <table class="table table-nowrap align-middle mb-0">
                <tbody>
                    <tr v-for="(list,index) in selected.lists" v-bind:key="index" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                        <!-- <td class="text-center">{{ index + 1 }}.</td> -->
                        <td class="fs-12">
                            {{list.service.service}} <span v-if="list.service.description != 'n/a'" class="fs-11 text-muted">({{list.service.description}})</span> 
                        </td>
                        <td class="text-end fs-12">{{formatMoney(list.service.price)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table table-nowrap align-middle mb-0">
                <tbody>
                    <tr class="table-light text-muted fs-12">
                        <td colspan="2">Subtotal : </td>
                        <td class="text-end">{{formatMoney(selected.total)}}</td>
                    </tr>
                    <tr class="table-light text-muted fs-12">
                        <td colspan="2">Discount : </td>
                        <td class="text-end">{{formatMoney(selected.discount)}}</td>
                    </tr>
                    <tr class="table-success fw-semibold">
                        <td colspan="2">Total : </td>
                        <td class="text-end">{{formatMoney(selected.total)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="selected.review" class="alert alert-info alert-dismissible material-shadow mt-3" role="alert" aria-live="polite" aria-atomic="true" show="">
            Comment : {{selected.review.comment}} <br /><br />
            <vue3-star-ratings :disableClick="true" :numberOfStars="5" v-model="selected.review.rating"/>
        </div>
        <hr class="text-muted mb-n2"/>

        <template v-slot:footer>
            <b-button v-if="selected.status.name === 'Pending'" @click="save(21)" variant="danger" block>Cancel Booking</b-button>
            <b-button v-if="selected.status.name === 'Pending' && client === false" @click="save(22)" variant="primary" :disabled="form.processing" block>Mark as Confirmed</b-button>
            <b-button v-if="selected.status.name === 'Confirmed' && client === false" @click="save(23)" variant="info" :disabled="form.processing" block>Mark as Ongoing</b-button>
            <b-button v-if="selected.status.name === 'Ongoing' && client === false" @click="save(20)" variant="success" :disabled="form.processing" block>Mark as Completed</b-button>
            
            <b-button @click="hide()" variant="light" block>Close</b-button>
        </template>
    </b-modal>
</template>
<script>
import vue3StarRatings from "vue3-star-ratings";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
export default {
    components: { Multiselect, vue3StarRatings },
    props: ['specialists','categories','users'],
    data(){
        return {
            selected: {
                user: {
                    profile: {}
                },
                status: {},
                review: {}
            },
            form: {},
            showModal: false,
            editable: false,
            client: false
        }
    },
    methods : {
        show(data) {
            this.selected = data;
            this.showModal = true;
        },
        view(data){
            this.client = true;
            this.selected = data;
            this.showModal = true;
        },
        save(status){
            this.form = this.$inertia.form({
                id: this.selected.id,
                status_id: status
            });

            this.form.put('/appointments/update',{
                preserveScroll: true,
                onSuccess: (response) => {
                    this.hide();
                },
            });
        },
        formatMoney(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return '₱'+val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
        hide(){
            this.showModal = false;
        },
    }
}
</script>