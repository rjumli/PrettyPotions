<template lang="">
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <b-row class="g-2">
                                <b-col md="8">
                                    <div class="search-box">
                                    <input type="text" class="form-control search" placeholder="Search Service"
                                        v-model="filter.keyword" />
                                    <i class="ri-search-line search-icon"></i>
                                    </div>
                                </b-col>
                                <b-col md="3">
                                    <Multiselect class="form-control" v-model="filter.category" 
                                    :close-on-select="true" placeholder="Select Category"
                                    :searchable="true" :create-option="true" object="true"
                                    :options="categories.data" label="name" track-by="name"/>
                                </b-col>
                                <b-col md="1">
                                    <b-button variant="primary" class="w-100" @click="openCreate()">Create</b-button>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div>
                        <b-row class="job-list-row" id="companies-list" v-if="!lists" style="height: calc(100vh - 301px); overflow-x: hidden;">
                            <b-col xxl="4" v-for="(data, index) of categories.data" :key="index">
                                <b-card no-body class="companiesList-card" @click="filter.category = data">
                                    <b-card-body style="cursor: pointer;">
                                        <div class="text-center">
                                            <h5 class="mt-3 company-name">{{ data.name }}</h5>
                                        </div>
                                        <div>
                                            <b-button type="button" variant="soft-primary" class="w-100 viewcompany-list">{{data.services.length}} Services Available</b-button>
                                        </div>
                                    </b-card-body>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row v-else>
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body" style="height: calc(100vh - 302px); overflow-x: hidden;">
                                        <div class="table-responsive">
                                            <table class="table table-nowrap align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr class="fs-11">
                                                        <th style="width: 3%;">#</th>
                                                        <th style="width: 30%;">Name</th>
                                                        <th style="width: 35%;" class="text-center">Description</th>
                                                        <th style="width: 15%;" class="text-center">Price</th>
                                                        <th style="width: 10%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(list,index) in filter.category.services" v-bind:key="index" :class="[(list.is_active == 0) ? 'table-warnings' : '']">
                                                        <td>{{ index + 1 }}</td>
                                                        <td>{{list.service}}</td>
                                                        <td class="text-center">{{list.description}}</td>
                                                        <td class="text-center">{{list.price}}</td>
                                                        <td class="text-end">
                                                            <b-button variant="primary" class="btn btn-primary btn-sm waves-effect waves-light" @click="addCart(list)">Book Now</b-button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-row>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body" style="height: calc(100vh - 205px); overflow: hidden;">
                            {{cart}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
export default {
    props: ['categories'],
    components: { Multiselect },
    data(){
        return {
            filter : {
                keyword: null,
                category: null
            },
            cart: [],
            lists: false
        }
    },
    watch: {
        "filter.category"(){
            if(this.filter.category){
                this.lists = true;
            }else{
                this.lists = false;
            }
        }
    },
    methods: {
        addCart(data){
            this.cart.push(data);
        }
    }
}
</script>