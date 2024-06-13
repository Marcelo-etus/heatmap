@extends('layouts.app')

@section('content')
<div id="app">
    <input type="date" id="startDate" v-model="startDate" placeholder="Data Inicial (timestamp)">
    <input type="date" id="endDate" v-model="endDate" placeholder="Data Final (timestamp)">
    <button @click="fetchData">Enviar</button>
    <div v-if="!error">
        <div id="chart"></div>
        <div v-if="bestTimes.length > 0" class="card">
            <h2>Os três melhores horários de postagem:</h2>

            <div v-for="(time, index) in bestTimes" :key="index">@{{ time.day  }} ás @{{time.hour}}</div>

        </div>
    </div>
    <div v-if="error" class="error">
        <p>Ocorreu um erro ao obter as informações. Por favor, tente novamente mais tarde.</p>
    </div>
</div>

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
            const today = new Date();
            const oneWeekAgo = new Date(today);
            oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);

            const startDateFormatted = oneWeekAgo.toISOString().split('T')[0];
            const endDateFormatted = today.toISOString().split('T')[0];

            this.startDate = startDateFormatted;
            this.endDate = endDateFormatted;
        },
        methods: {
            fetchData() {
                axios.post('/instagram-data', {
                        startDate: this.startDate,
                        endDate: this.endDate
                    })
                    .then(response => {
                        this.instagramData = []
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
                const days = [];
                const hours = Array.from({
                    length: 24
                }, (_, i) => `${i < 10 ? '0' + i : i}:00`);
                const dataByDay = [];

                for (const day of this.instagramData.datasets_top_day_posts) {
                    const dayIndex = days.length;
                    days.push(day.name);
                    const dayData = [];
                    for (const data of day.data) {
                        const hourIndex = parseInt(data.x);
                        dayData.push({
                            x: hourIndex,
                            y: data.y
                        });
                    }
                    dataByDay.push(dayData);
                }

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



                    const topTimes = [];


                    for (const day of this.instagramData.datasets_top_day_posts) {

                        const hourCounts = Array.from({
                            length: 24
                        }, () => 0);


                        for (const data of day.data) {
                            const hourIndex = parseInt(data.x);
                            hourCounts[hourIndex] += data.y;
                        }


                        for (let i = 0; i < 3; i++) {
                            const maxCount = Math.max(...hourCounts);
                            const maxHourIndex = hourCounts.indexOf(maxCount);


                            topTimes.push({
                                day: day.name,
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