@extends('layouts.app')

@section('title', 'ادائي')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.performance.show') }}" second="ادائي" />

    <div class="mx-auto max-w-7xl w-full space-y-6">

        <!-- ملخص الأداء العام -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 shadow-2xl text-white">
            <h1 class="text-3xl font-bold mb-2">تقييم أدائك الشامل</h1>
            <p class="text-blue-100">النتيجة الإجمالية: {{ $latestPerformance->total_score }} %</p>
        </div>

        <!-- بطاقات الإحصائيات السريعة -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- سرعة الرد -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bolt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">سرعة الرد</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="responseSpeed">0</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">دقيقة متوسط</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 transition-all duration-1000 ease-out" id="responseSpeedBar"
                        style="width: 0%"></div>
                </div>
            </div>

            <!-- مدة التنفيذ -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">مدة التنفيذ</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="executionTime">0</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">يوم متوسط</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 transition-all duration-1000 ease-out" id="executionTimeBar"
                        style="width: 0%"></div>
                </div>
            </div>

            <!-- الرد على الرسائل -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">معدل الرد</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="messageRate">0%</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">من الرسائل</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 transition-all duration-1000 ease-out" id="messageRateBar"
                        style="width: 0%"></div>
                </div>
            </div>

            <!-- عدد الدعم -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-headset text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد الدعم</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="totalSupport">0</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">تذكرة مغلقة</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500 transition-all duration-1000 ease-out" id="supportBar"
                        style="width: 0%"></div>
                </div>
            </div>

            <!-- عدد المهام -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد المهام</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1" id="totalTasks">0</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">مهمة مكتملة</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500 transition-all duration-1000 ease-out" id="tasksBar"
                        style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- رسم بياني دائري -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-pie text-blue-600"></i> توزيع الأداء
                </h3>
                <canvas id="performanceChart"></canvas>
            </div>

            <!-- رسم بياني خطي -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-line text-green-600"></i> الأداء الأسبوعي
                </h3>
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // البيانات من Laravel
        const latestPerformance = @json($latestPerformance);
        const weeklyData = @json($weeklyData);
        const averages = @json($averages);

        // دوال حساب النقاط
        function calculateResponseScore(minutes) {
            if (!minutes || minutes === 0) return 0;
            if (minutes <= 30) return 100;
            if (minutes <= 60) return 80;
            if (minutes <= 120) return 60;
            if (minutes <= 240) return 40;
            return 20;
        }

        function calculateExecutionScore(minutes) {
            if (!minutes || minutes === 0) return 0;
            const hours = minutes / 60;
            if (hours <= 2) return 100;
            if (hours <= 6) return 80;
            if (hours <= 24) return 60;
            if (hours <= 48) return 40;
            return 20;
        }

        // دالة لعمل تأثير العد التدريجي
        function animateValue(element, start, end, duration, suffix = '', decimals = 1) {
            // التأكد من أن القيم رقمية
            start = parseFloat(start) || 0;
            end = parseFloat(end) || 0;
            
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                
                // تنسيق القيمة حسب نوع البيانات
                let displayValue;
                if (suffix === '%' || decimals === 0) {
                    displayValue = Math.round(current);
                } else {
                    displayValue = current.toFixed(decimals);
                }
                
                element.textContent = displayValue + suffix;
            }, 16);
        }

        // تحديث البيانات بشكل ديناميكي
        function updateStatistics() {
            const responseScore = calculateResponseScore(latestPerformance.response_speed);
            const executionScore = calculateExecutionScore(latestPerformance.execution_time);
            const messageRate = parseFloat(latestPerformance.message_response_rate) || 0;

            // تحديث سرعة الرد
            const responseSpeed = parseFloat(latestPerformance.response_speed) || 0;
            animateValue(document.getElementById('responseSpeed'), 0, responseSpeed, 1000, '', 0);
            setTimeout(() => {
                document.getElementById('responseSpeedBar').style.width = responseScore + '%';
            }, 100);

            // تحديث مدة التنفيذ
            const executionTimeValue = parseFloat(latestPerformance.execution_time) || 0;
            animateValue(document.getElementById('executionTime'), 0, executionTimeValue, 1000, '', 1);
            setTimeout(() => {
                document.getElementById('executionTimeBar').style.width = executionScore + '%';
            }, 200);

            // تحديث معدل الرد
            animateValue(document.getElementById('messageRate'), 0, messageRate, 1000, '%', 0);
            setTimeout(() => {
                document.getElementById('messageRateBar').style.width = messageRate + '%';
            }, 300);

            // تحديث عدد الدعم
            const supportValue = parseFloat(averages.total_support) || 0;
            animateValue(document.getElementById('totalSupport'), 0, supportValue, 1000, '', 0);
            setTimeout(() => {
                const supportPercent = Math.min(100, (supportValue / 100) * 100);
                document.getElementById('supportBar').style.width = supportPercent + '%';
            }, 400);

            // تحديث عدد المهام
            const tasksValue = parseFloat(averages.total_tasks) || 0;
            animateValue(document.getElementById('totalTasks'), 0, tasksValue, 1000, '', 0);
            setTimeout(() => {
                const tasksPercent = Math.min(100, (tasksValue / 40) * 100);
                document.getElementById('tasksBar').style.width = tasksPercent + '%';
            }, 500);
        }

        // رسم بياني دائري
        function createPieChart() {
            const responseScore = calculateResponseScore(latestPerformance.response_speed);
            const executionScore = calculateExecutionScore(latestPerformance.execution_time);
            const messageRate = latestPerformance.message_response_rate || 0;

            new Chart(document.getElementById('performanceChart'), {
                type: 'doughnut',
                data: {
                    labels: ['سرعة الرد', 'مدة التنفيذ', 'معدل الرد على الرسائل'],
                    datasets: [{
                        data: [responseScore, executionScore, messageRate],
                        backgroundColor: ['#3b82f6', '#22c55e', '#a855f7'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1500
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                font: {
                                    family: 'Cairo',
                                    size: 14
                                },
                                padding: 15
                            }
                        },
                        tooltip: {
                            rtl: true,
                            titleFont: {
                                family: 'Cairo'
                            },
                            bodyFont: {
                                family: 'Cairo'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + Math.round(context.parsed) + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        // رسم بياني خطي
        function createLineChart() {
            if (weeklyData && weeklyData.length > 0) {
                const weeklyLabels = weeklyData.map(d => {
                    const date = new Date(d.performance_date);
                    return date.toLocaleDateString('ar-EG', {
                        weekday: 'short',
                        day: 'numeric',
                        month: 'short'
                    });
                });

                const weeklyResponseScores = weeklyData.map(d => calculateResponseScore(d.response_speed));
                const weeklyExecutionScores = weeklyData.map(d => calculateExecutionScore(d.execution_time));

                new Chart(document.getElementById('weeklyChart'), {
                    type: 'line',
                    data: {
                        labels: weeklyLabels,
                        datasets: [{
                            label: 'سرعة الرد',
                            data: weeklyResponseScores,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        }, {
                            label: 'مدة التنفيذ',
                            data: weeklyExecutionScores,
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        animation: {
                            duration: 1500
                        },
                        plugins: {
                            legend: {
                                rtl: true,
                                labels: {
                                    font: {
                                        family: 'Cairo'
                                    },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                rtl: true,
                                titleFont: {
                                    family: 'Cairo'
                                },
                                bodyFont: {
                                    family: 'Cairo'
                                },
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + Math.round(context.parsed.y) + '%';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    font: {
                                        family: 'Cairo'
                                    },
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        family: 'Cairo'
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        // تشغيل التحديثات
        updateStatistics();
        createPieChart();
        createLineChart();
    });
</script>
@endsection