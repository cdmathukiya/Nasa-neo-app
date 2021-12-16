<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h2>Get the Asteroid stats</h2>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <form class="align-items-center row" action="">
                                    <div class="col-auto">
                                        <label>Start Date</label>
                                        <datepicker name="start_date" v-model="searchData.start_date" input-class="form-control" :format="'dd-MM-yyyy'"></datepicker>
                                    </div>
                                    <div class="col-auto">
                                        <label>End Date</label>
                                        <datepicker name="end_date" v-model="searchData.end_date" input-class="form-control" :format="'dd-MM-yyyy'"></datepicker>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary find-btn mt-4" @click="getAsteroidData()" :disabled="loading">Submit</button>
                                    </div>
                                    <p class="text-danger">{{general_error}}</p>
                                </form>
                            </div>
                            <div class="row mb-4 justify-content-center" v-if="asteroidStats != null">
                                <div class="col-8 mb-4" >
                                    <div class="accordion accordion-flush" id="asteroidStats">
                                      <div class="accordion-item">
                                        <h2 class="accordion-header" id="fasteste">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                            Fastest Asteroid</button>
                                        </h2>
                                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="fasteste" data-bs-parent="#asteroidStats">
                                            <div class="accordion-body">
                                                <p><b>Id :</b> {{asteroidStats.fastest_asteroid.id}}</p>
                                                <p><b>Speed :</b> {{asteroidStats.fastest_asteroid.speed}} km/h</p>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="accordion-item">
                                        <h2 class="accordion-header" id="closest">
                                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                            Closest Asteroid</button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="closest" data-bs-parent="#asteroidStats">
                                            <div class="accordion-body">
                                                <p><b>Id :</b> {{asteroidStats.closest_asteroid.id}}</p>
                                                <p><b>Distance :</b> {{asteroidStats.closest_asteroid.speed}} km</p>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="accordion-item">
                                        <h2 class="accordion-header" id="average-size">
                                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                            Average Size of the Asteroids
                                          </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="average-size" data-bs-parent="#asteroidStats">
                                            <div class="accordion-body">
                                              <p><b>Size :</b> {{asteroidStats.average_asteroids}} km</p>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-8" v-if="chartData != null">
                                    <bar-chart :chartData="chartData" :options="chartOption"></bar-chart>
                                </div>
                            </div>
                            <div class="row" v-if="loading">
                                <div class="col-md-12 text-center h4">Loading...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import common from '../services/common';
    import BarChart from '../components/BarChart'

    export default {
        props: ['data'],
        components: {Datepicker, BarChart},
        data() {
            // let query = this.$route.query;
            let query = {};
            let today = new Date();
            return {
                asteroidStats:null,
                chartData:null,
                chartOption:{
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                beginAtZero: true
                            }
                        }]
                    }
                },
                searchData: {
                    start_date:today,
                    end_date:today,
                },
                general_error:"",
                loading: false,
            }
        },
        mounted() {
            
        },
        methods: {
            getAsteroidData() {
                this.general_error = "";
                this.loading = true;
                this.chartData = null;
                this.asteroidStats = null;
                
                common.getAsteroidData(this.searchData).then(response => {
                    this.loading = false;
                    var result = response.data;
                    if (response.status == 200 && result.status == 200) {
                        if ((typeof result.data !== 'undefined')) {
                            this.asteroidStats = result.data;
                            // this.chartData = result.data.chart_data;
                            this.chartData = {
                              labels: result.data.chart_data.labels,
                              datasets: [
                                {
                                  label: 'Number of Asteroids',
                                  backgroundColor: '#f87979',
                                  data: result.data.chart_data.data
                                },
                              ],
                            }
                        }
                    } else {
                        this.general_error = (typeof result.message !== 'undefined') ? result.message : "Something went wrong please try again after refresh!";
                    }
                }).finally(() => {
                    this.loading = false;
                });
            },
        }
    }
</script>