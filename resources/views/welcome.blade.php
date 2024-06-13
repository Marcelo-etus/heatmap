@extends('layouts.app')

@section('content')
<div id="app">
    <div class="date-selector">
        <input type="date" id="startDate" v-model="startDate" placeholder="Data Inicial (timestamp)">
        <input type="date" id="endDate" v-model="endDate" placeholder="Data Final (timestamp)">
        <button @click="fetchData">Enviar</button>
    </div>
    <div v-if="!error" class="chart-wrapper">
        <div id="chart"></div>
        <div v-if="bestTimes.length > 0" class="best-times">
            <h2>Os três melhores horários de postagem:</h2>
            <div v-for="(time, index) in bestTimes" :key="index" class="best-time">
                <p>@{{ time.day }} às @{{ time.hour }}</p>
            </div>
        </div>
    </div>
    <div v-if="error" class="error">
        <p>Ocorreu um erro ao obter as informações. Por favor, tente novamente mais tarde.</p>
    </div>
</div>

<style>
    #app {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .date-selector {
        margin-bottom: 20px;
    }

    .chart-wrapper {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .best-times {
        margin-left: 20px;
    }

    .best-time {
        margin-bottom: 10px;
    }

    .error {
        color: red;
        margin-top: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    new Vue({
        el: '#app',
        data: {
            instagramData: null,
            startDate: '',
            endDate: '',
            bestTimes: [],
            error: false
        },
        mounted() {
            this.setDefaultDates();
        },
        methods: {
            setDefaultDates() {
                const today = new Date();
                const oneWeekAgo = new Date(today);
                oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

                this.startDate = this.formatDate(oneWeekAgo);
                this.endDate = this.formatDate(today);
            },
            formatDate(date) {
                return date.toISOString().split('T')[0];
            },
            fetchData() {
                axios.post('/instagram-data', {
                        startDate: this.startDate,
                        endDate: this.endDate
                    })
                    .then(response => {
                        this.error = false;
                        this.instagramData = response.data;
                        console.log('Dados do Instagram:', this.instagramData);
                        this.renderChart();
                        this.calculateBestTimes();
                    })
                    .catch(error => {
                        console.error('Erro ao obter os dados do Instagram:', error);
                        this.error = true;
                    });
            },
            renderChart() {
                const days = this.instagramData.datasets_top_day_posts.map(day => day.name);
                const hours = Array.from({ length: 24 }, (_, i) => `${i < 10 ? '0' + i : i}:00`);
                const dataByDay = this.instagramData.datasets_top_day_posts.map(day => {
                    return day.data.map(data => {
                        const hourIndex = parseInt(data.x);
                        return { x: hourIndex, y: data.y };
                    });
                });

                const series = days.map((day, index) => ({
                    name: day,
                    data: dataByDay[index]
                }));

                const options = {
                    chart: {
                        type: 'heatmap',
                        height: 400
                    },
                    series,
                    xaxis: {
                        categories: hours
                    },
                    colors: ['#3366FF']
                };

                const chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            },
            calculateBestTimes() {
                if (this.instagramData) {
                    const weekdays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

                    const topTimes = [];

                    for (const day of this.instagramData.datasets_top_day_posts) {
                        const hourCounts = Array.from({ length: 24 }, () => 0);

                        for (const data of day.data) {
                            const hourIndex = parseInt(data.x);
                            hourCounts[hourIndex] += data.y;
                        }

                        for (let i = 0; i < 3; i++) {
                            const maxCount = Math.max(...hourCounts);
                            const maxHourIndex = hourCounts.indexOf(maxCount);

                            topTimes.push({
                                day: weekdays[new Date(day.name).getDay()],
                                hour: `${maxHourIndex}:00`,
                                qty: maxCount
                            });

                            hourCounts[maxHourIndex] = 0;
                        }
                    }

                    this.bestTimes = topTimes.sort((a, b) => b.qty - a.qty).slice(0, 3);
                }
            }
        }
    });
</script>

@endsection
