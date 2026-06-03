@extends('layouts.app')

@section('title', 'ادائي')

@section('content')
<section class="p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.performance.show') }}" second="ادائي" />

    <div class="mx-auto max-w-7xl w-full space-y-6">

        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 shadow-2xl text-white">
            <h1 class="text-3xl font-bold mb-2">تقييم أدائك الشامل</h1>
            <p class="text-blue-100">
                النتيجة الإجمالية: <span class="font-black text-2xl">{{ $stats['total_score'] }}%</span>
                <span class="text-sm opacity-80 mr-2">({{ $stats['period_label'] }})</span>
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bolt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">سرعة الرد</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tabular-nums">
                    {{ $stats['response_speed'] > 0 ? $stats['response_speed'] : '—' }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">دقيقة متوسط</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500" style="width: {{ $stats['response_score'] }}%"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">مدة التنفيذ</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tabular-nums">
                    {{ $stats['execution_time'] > 0 ? $stats['execution_time'] : '—' }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">يوم متوسط</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500" style="width: {{ $stats['execution_score'] }}%"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">معدل الرد</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tabular-nums">
                    {{ $stats['message_response_rate'] }}%
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">من الرسائل</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500" style="width: {{ $stats['message_response_rate'] }}%"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-headset text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تذاكر الدعم</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tabular-nums">{{ $stats['support_tickets_closed'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">مغلقة (30 يوم)</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-yellow-500" style="width: {{ min(100, $stats['support_tickets_closed'] * 5) }}%"></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-lg border border-gray-200 dark:border-gray-700 col-span-2 md:col-span-1">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">المهام</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tabular-nums">{{ $stats['completed_tasks'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">مكتملة (30 يوم)</p>
                <div class="mt-3 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500" style="width: {{ min(100, $stats['completed_tasks'] * 8) }}%"></div>
                </div>
            </div>
        </div>

        @if(Auth::user()->role == 'partner')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border-t-4 border-orange-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">الالتزام بالوقت (هذا الشهر)</h3>
                    <i class="fas fa-user-clock text-orange-500 text-2xl"></i>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">إجمالي التأخير:</span>
                        <span class="text-black font-bold">{{ $totalLateMinutes }} دقيقة</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">أيام الحضور:</span>
                        <span class="text-green-600 font-bold">{{ $workStats->where('type', 'حضور')->count() }} يوم</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 italic border-r-2 border-orange-500 pr-2">
                        {{ $totalLateMinutes > 60 ? 'لديك تأخير يتجاوز الساعة، يرجى الالتزام بمواعيد الحضور.' : 'أداؤك في الحضور ممتاز هذا الشهر.' }}
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border-t-4 border-green-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">المستحقات الإضافية</h3>
                    <i class="fas fa-money-bill-wave text-green-500 text-2xl"></i>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">إجمالي المكافآت:</span>
                        <span class="text-green-600 font-bold">+{{ number_format((float) ($financials->total_bonuses ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">إجمالي الخصومات:</span>
                        <span class="text-black font-bold">-{{ number_format((float) ($financials->total_deductions ?? 0), 2) }}</span>
                    </div>
                    @php $net = ($financials->total_bonuses ?? 0) - ($financials->total_deductions ?? 0); @endphp
                    <div class="pt-2 border-t flex justify-between items-center">
                        <span class="font-bold text-gray-900 dark:text-white">الصافي الإضافي:</span>
                        <span class="text-xl font-black {{ $net >= 0 ? 'text-green-600' : 'text-black' }}">
                            {{ number_format($net, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border-t-4 border-blue-500">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">توصيات لتحسين دخلك</h3>
                    <i class="fas fa-lightbulb text-blue-500 text-2xl"></i>
                </div>
                <ul class="text-sm space-y-2 text-gray-600 dark:text-gray-400">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                        <span>تقليل دقائق التأخير يوفر عليك خصومات شهرية.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-500 mt-1"></i>
                        <span>الرد السريع على النقاشات يرفع معدل الرد ونتيجتك الإجمالية.</span>
                    </li>
                </ul>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-pie text-blue-600"></i> توزيع الأداء
                </h3>
                <canvas id="performanceChart" height="220"></canvas>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-line text-green-600"></i> الأداء الأسبوعي
                </h3>
                <canvas id="weeklyChart" height="220"></canvas>
            </div>
        </div>

    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stats = @json($stats);

        new Chart(document.getElementById('performanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['سرعة الرد', 'مدة التنفيذ', 'معدل الرد على الرسائل'],
                datasets: [{
                    data: [
                        stats.response_score,
                        stats.execution_score,
                        stats.message_response_rate
                    ],
                    backgroundColor: ['#3b82f6', '#22c55e', '#a855f7'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', rtl: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.label + ': ' + Math.round(ctx.parsed) + '%'
                        }
                    }
                }
            }
        });

        const weekly = stats.weekly_chart;
        new Chart(document.getElementById('weeklyChart'), {
            type: 'line',
            data: {
                labels: weekly.labels,
                datasets: [{
                    label: 'سرعة الرد (نقاط)',
                    data: weekly.response_scores,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'مدة التنفيذ (نقاط)',
                    data: weekly.execution_scores,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: (v) => v + '%' } }
                },
                plugins: {
                    legend: { rtl: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + Math.round(ctx.parsed.y) + '%'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
