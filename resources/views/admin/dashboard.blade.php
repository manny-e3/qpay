@extends('layouts.admin')

@section('title', 'Dashboard | OTP Cloud')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome to OTP Management Dashboard.')

@section('content')
<div class="nk-block">
    <div class="row g-gs">
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="project-item">
                        <div class="project-head">
                            <div class="project-title">
                                <div class="user-avatar sq bg-primary"><span><em class="icon ni ni-grid-alt-fill"></em></span></div>
                                <div class="project-info">
                                    <h6 class="title">Total Applications</h6>
                                    <span class="sub-text">Registered apps</span>
                                </div>
                            </div>
                        </div>
                        <div class="project-details">
                            <div class="h3">{{ $stats['total_apps'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="project-item">
                        <div class="project-head">
                            <div class="project-title">
                                <div class="user-avatar sq bg-success"><span><em class="icon ni ni-check-circle-fill"></em></span></div>
                                <div class="project-info">
                                    <h6 class="title">Active Apps</h6>
                                    <span class="sub-text">Currently running</span>
                                </div>
                            </div>
                        </div>
                        <div class="project-details">
                            <div class="h3 text-success">{{ $stats['active_apps'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="project-item">
                        <div class="project-head">
                            <div class="project-title">
                                <div class="user-avatar sq bg-warning"><span><em class="icon ni ni-bolt-fill"></em></span></div>
                                <div class="project-info">
                                    <h6 class="title">OTPs (24h)</h6>
                                    <span class="sub-text">Generated codes</span>
                                </div>
                            </div>
                        </div>
                        <div class="project-details">
                            <div class="h3">{{ $stats['otps_generated_24h'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="project-item">
                        <div class="project-head">
                            <div class="project-title">
                                <div class="user-avatar sq bg-info"><span><em class="icon ni ni-history"></em></span></div>
                                <div class="project-info">
                                    <h6 class="title">Total History</h6>
                                    <span class="sub-text">Audit trail entries</span>
                                </div>
                            </div>
                        </div>
                        <div class="project-details">
                            <div class="h3">{{ $stats['total_otp_history'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block nk-block-lg mt-5">
    <div class="row g-gs">
         <div class="col-8">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-2">
                        <div class="card-title">
                            <h6 class="title">Top Applications by Volume</h6>
                            <p>Most active applications based on total OTP requests.</p>
                        </div>
                    </div>
                    <div class="nk-ck-sm" style="height: 280px;">
                        <canvas class="bar-chart" id="topAppsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card card-bordered h-100">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-2">
                        <div class="card-title">
                            <h6 class="title">Status Distribution</h6>
                            <p>OTP status breakdown.</p>
                        </div>
                    </div>
                    <div class="nk-ck-sm">
                        <canvas class="doughnut-chart" id="statusDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block nk-block-lg mt-5">
    <div class="row g-gs">
        <div class="col-12">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-2">
                        <div class="card-title">
                            <h6 class="title">OTP Generation Trends</h6>
                            <p>Overview of OTPs generated in the last 15 days.</p>
                        </div>
                    </div>
                    <div class="nk-ck-sm" style="height: 280px;">
                        <canvas class="line-chart" id="otpTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nk-block nk-block-lg mt-5">
    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">Recent Generation Requests</h5>
            </div>
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th class="tb-col">App ID</th>
                        <th class="tb-col">User</th>
                        <th class="tb-col">IP Address</th>
                        <th class="tb-col">Expires At</th>
                        <th class="tb-col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_activity as $log)
                    <tr>
                        <td class="tb-col"><span class="badge badge-dim bg-primary">{{ $log->appID }}</span></td>
                        <td class="tb-col">{{ $log->username }}</td>
                        <td class="tb-col"><code>{{ $log->IP }}</code></td>
                        <td class="tb-col text-soft">{{ \Carbon\Carbon::parse($log->OTP_End)->diffForHumans() }}</td>
                        <td class="tb-col">
                            <span class="badge badge-dot {{ $log->status === 'validated' ? 'bg-success' : ($log->status === 'expired' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-5 text-soft">No recent activity logged.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function(NioApp, $) {
        'use strict';

        var trendData = {
            labels: {!! json_encode($trends->pluck('date')) !!},
            data: {!! json_encode($trends->pluck('count')) !!}
        };

        var distData = {
            labels: {!! json_encode($distribution->pluck('status')->map(fn($s) => ucfirst($s))) !!},
            data: {!! json_encode($distribution->pluck('count')) !!}
        };

        var topAppsData = {
            labels: {!! json_encode($top_apps->map(fn($app) => $app->appName ?? $app->appID)) !!},
            data: {!! json_encode($top_apps->pluck('count')) !!}
        };

        var otpTrendChart = {
            labels: trendData.labels,
            dataSets: [{
                label: "OTPs Generated",
                color: "#798bff",
                background: NioApp.hexRGB('#798bff', .15),
                data: trendData.data
            }]
        };

        var statusDistChart = {
            labels: distData.labels,
            dataSets: [{
                label: "Status",
                backgroundColor: ["#1ee0ac", "#f4bd0e", "#ff63a5", "#09c2de", "#364a63"],
                data: distData.data
            }]
        };

        var topAppsChart = {
            labels: topAppsData.labels,
            dataSets: [{
                label: "Requests",
                color: "#9d72ff",
                data: topAppsData.data
            }]
        };

        function lineChart(selector, usageData) {
            var $selector = $(selector || '.line-chart');
            $selector.each(function() {
                var $self = $(this),
                    _self_id = $self.attr('id'),
                    _get_data = (typeof usageData === 'undefined') ? eval(_self_id) : usageData;
                var selectCanvas = document.getElementById(_self_id).getContext("2d");

                var chart_data = [];
                for (var i = 0; i < _get_data.dataSets.length; i++) {
                    chart_data.push({
                        label: _get_data.dataSets[i].label,
                        tension: 0.4,
                        backgroundColor: _get_data.dataSets[i].background,
                        fill: true,
                        borderWidth: 2,
                        borderColor: _get_data.dataSets[i].color,
                        pointBorderColor: 'transparent',
                        pointBackgroundColor: 'transparent',
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: _get_data.dataSets[i].color,
                        pointBorderWidth: 2,
                        pointHoverRadius: 4,
                        pointHoverBorderWidth: 2,
                        pointRadius: 4,
                        pointHitRadius: 4,
                        data: _get_data.dataSets[i].data,
                    });
                }
                var chart = new Chart(selectCanvas, {
                    type: 'line',
                    data: {
                        labels: _get_data.labels,
                        datasets: chart_data,
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.parsed.y} OTPs`;
                                    }
                                },
                                backgroundColor: '#eff6ff',
                                titleFont: { size: 13 },
                                titleColor: '#6783b8',
                                bodyColor: '#9eaecf',
                                bodyFont: { size: 12 },
                                displayColors: false,
                                padding: 12,
                                boxWidth: 8,
                                boxHeight: 8,
                                boxPadding: 6,
                                touchStartThreshold: 2,
                                bevelWidth: 0,
                                bevelHighlightColor: 'rgba(255,255,255,0.75)',
                                bevelShadowColor: 'rgba(0,0,0,0.5)',
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowOffsetY: 0,
                                shadowColor: 'rgba(0,0,0,0.5)',
                                sliceOffset: 0,
                                strokeWidth: 2,
                                borderRadius: 4,
                                borderWidth: 1,
                                borderColor: '#e5e9f2',
                            },
                        },
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                display: true,
                                grid: {
                                    color: NioApp.hexRGB('#526484', .1),
                                    tickLength: 0,
                                },
                                ticks: {
                                    padding: 10,
                                    font: { size: 10 },
                                    color: '#8094ae',
                                    beginAtZero: true,
                                    stepSize: 10
                                },
                            },
                            x: {
                                display: true,
                                grid: {
                                    color: "transparent",
                                    tickLength: 0,
                                },
                                ticks: {
                                    padding: 10,
                                    font: { size: 10 },
                                    color: '#8094ae',
                                },
                            },
                        },
                    },
                });
            });
        }

        function doughnutChart(selector, usageData) {
            var $selector = $(selector || '.doughnut-chart');
            $selector.each(function() {
                var $self = $(this),
                    _self_id = $self.attr('id'),
                    _get_data = (typeof usageData === 'undefined') ? eval(_self_id) : usageData;
                var selectCanvas = document.getElementById(_self_id).getContext("2d");

                var chart_data = [];
                for (var i = 0; i < _get_data.dataSets.length; i++) {
                    chart_data.push({
                        label: _get_data.dataSets[i].label,
                        backgroundColor: _get_data.dataSets[i].backgroundColor,
                        hoverBackgroundColor: _get_data.dataSets[i].backgroundColor,
                        borderWidth: 2,
                        borderColor: "#fff",
                        data: _get_data.dataSets[i].data,
                    });
                }
                var chart = new Chart(selectCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: _get_data.labels,
                        datasets: chart_data,
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed} OTPs`;
                                    }
                                },
                                backgroundColor: '#eff6ff',
                                titleFont: { size: 13 },
                                titleColor: '#6783b8',
                                bodyColor: '#9eaecf',
                                bodyFont: { size: 12 },
                                displayColors: false,
                                padding: 12,
                                boxWidth: 8,
                                boxHeight: 8,
                                boxPadding: 6,
                                touchStartThreshold: 2,
                                bevelWidth: 0,
                                bevelHighlightColor: 'rgba(255,255,255,0.75)',
                                bevelShadowColor: 'rgba(0,0,0,0.5)',
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowOffsetY: 0,
                                shadowColor: 'rgba(0,0,0,0.5)',
                                sliceOffset: 0,
                                strokeWidth: 2,
                                borderRadius: 4,
                                borderWidth: 1,
                                borderColor: '#e5e9f2',
                            },
                        },
                        rotation: -1.5,
                        cutout: '70%',
                        maintainAspectRatio: false,
                    },
                });
            });
        }

        function barChart(selector, usageData) {
            var $selector = $(selector || '.bar-chart');
            $selector.each(function() {
                var $self = $(this),
                    _self_id = $self.attr('id'),
                    _get_data = (typeof usageData === 'undefined') ? eval(_self_id) : usageData;
                var selectCanvas = document.getElementById(_self_id).getContext("2d");

                var chart_data = [];
                for (var i = 0; i < _get_data.dataSets.length; i++) {
                    chart_data.push({
                        label: _get_data.dataSets[i].label,
                        backgroundColor: _get_data.dataSets[i].color,
                        borderWidth: 0,
                        data: _get_data.dataSets[i].data,
                        borderRadius: 4
                    });
                }
                var chart = new Chart(selectCanvas, {
                    type: 'bar',
                    data: {
                        labels: _get_data.labels,
                        datasets: chart_data,
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.parsed.y} Requests`;
                                    }
                                },
                                backgroundColor: '#eff6ff',
                                titleFont: { size: 13 },
                                titleColor: '#6783b8',
                                bodyColor: '#9eaecf',
                                bodyFont: { size: 12 },
                                displayColors: false,
                                padding: 12,
                                borderRadius: 4,
                                borderWidth: 1,
                                borderColor: '#e5e9f2',
                            },
                        },
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                display: true,
                                grid: {
                                    color: NioApp.hexRGB('#526484', .1),
                                    tickLength: 0,
                                },
                                ticks: {
                                    padding: 10,
                                    font: { size: 10 },
                                    color: '#8094ae',
                                    beginAtZero: true
                                },
                            },
                            x: {
                                display: true,
                                grid: {
                                    color: "transparent",
                                    tickLength: 0,
                                },
                                ticks: {
                                    padding: 10,
                                    font: { size: 10 },
                                    color: '#8094ae',
                                },
                            },
                        },
                    },
                });
            });
        }

        // init chart
        lineChart();
        doughnutChart();
        barChart();

    })(NioApp, jQuery);
</script>
@endpush
