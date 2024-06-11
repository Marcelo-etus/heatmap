@extends('layouts.app')

@section('content')
<div id="app">
    <input type="date" id="startDate" v-model="startDate" placeholder="Data Inicial (timestamp)">
    <input type="date" id="endDate" v-model="endDate" placeholder="Data Final (timestamp)">
    <button @click="fetchData">Enviar</button> 
    <div id="chart"></div>
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
            endDate: ''
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
                    this.instagramData = response.data;
                    this.renderChart(); 
                })
                .catch(error => {
                    console.error('Erro ao obter os dados do Instagram:', error);
                });
            },
            renderChart() {
                const days = [];
                const hours = Array.from({ length: 24 }, (_, i) => `${i < 10 ? '0' + i : i}:00`);
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
            }
        }
    });
</script>

@endsection
