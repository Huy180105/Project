<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-cyan-50 px-4 py-2 text-xs font-black uppercase tracking-[0.24em] text-cyan-700">
                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-500"></span>
                    Trung tâm điều phối hàng đợi
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-950 sm:text-5xl">Dashboard điều phối bệnh viện</h1>
                <p class="mt-3 max-w-2xl text-base font-medium leading-7 text-slate-600">
                    Theo dõi số đang gọi, số đã sẵn sàng sau xác nhận HIS, bệnh nhân vắng mặt và tiến độ khám trong ngày.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm transition hover:border-cyan-300 hover:text-cyan-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
                    Trang chủ
                </a>
                <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>
                    Mở hàng đợi
                </a>
                <a href="{{ route('queue-tickets.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-500 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:-translate-y-0.5">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Tạo số mới
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Số đang gọi</p>
                <h3 id="dashboard-calling-number" class="mt-4 text-4xl font-black text-slate-950">{{ $callingTicket?->displayNumber() ?? '---' }}</h3>
                <p id="dashboard-calling-patient" class="mt-3 text-sm text-slate-500">{{ $callingTicket?->patient_name ?? 'Chưa có lượt đang gọi' }}</p>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Chờ thanh toán/HIS</p>
                <h3 id="dashboard-waiting-count" class="mt-4 text-4xl font-black text-slate-950">{{ $waitingCount }}</h3>
                <p class="mt-3 text-sm text-slate-500">Chưa đủ điều kiện để gọi khám.</p>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Sẵn sàng gọi</p>
                <h3 id="dashboard-ready-count" class="mt-4 text-4xl font-black text-slate-950">{{ $readyCount }}</h3>
                <p class="mt-3 text-sm text-slate-500">Đã xác nhận HIS hoặc được miễn xác nhận trước.</p>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Vắng mặt</p>
                <h3 id="dashboard-missed-count" class="mt-4 text-4xl font-black text-slate-950">{{ $missedCount }}</h3>
                <p class="mt-3 text-sm text-slate-500">Có thể gọi lại thủ công khi bệnh nhân quay lại.</p>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Hoàn thành hôm nay</p>
                <h3 id="dashboard-completed-count" class="mt-4 text-4xl font-black text-slate-950">{{ $completedTodayCount }}</h3>
                <p class="mt-3 text-sm text-slate-500">Lượt khám đã kết thúc trong ngày.</p>
            </div>
            <div class="metric-card">
                <p class="text-sm font-bold text-slate-500">Thời gian chờ ước tính TB</p>
                <h3 id="dashboard-average-wait" class="mt-4 text-4xl font-black text-slate-950">{{ $averageEstimatedWait }} phút</h3>
                <p class="mt-3 text-sm text-slate-500">Tính trên nhóm đang chờ và sẵn sàng gọi.</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950">Tải phòng khám theo giờ</h2>
                        <p class="mt-1 text-sm text-slate-500">Theo dõi lưu lượng để điều phối nhân lực trong ngày.</p>
                    </div>
                    <a href="{{ route('queue-tickets.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 transition hover:border-cyan-300 hover:text-cyan-700">
                        Xem chi tiết
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid gap-3 rounded-3xl bg-cyan-50 p-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach(\App\Models\QueueTicket::departments() as $department)
                        <form method="POST" action="{{ route('queue-tickets.call-next', ['department' => $department]) }}">
                            @csrf
                            <button class="flex w-full items-center justify-between rounded-2xl bg-white px-4 py-3 text-left text-sm font-black text-slate-900 shadow-sm transition hover:bg-slate-950 hover:text-white" type="submit">
                                <span>{{ $department }}</span>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="mt-6 h-[340px]">
                    <canvas id="queueChart"></canvas>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Trạng thái hiện tại</h2>
                    <div class="mt-5 grid gap-4">
                        <div class="rounded-3xl bg-slate-50 p-5">
                            <p class="text-sm font-bold text-slate-500">Số đang gọi</p>
                            <p id="dashboard-current-number" class="mt-3 text-5xl font-black text-slate-950">{{ $callingTicket?->displayNumber() ?? '---' }}</p>
                            <p id="dashboard-current-room" class="mt-2 text-sm text-slate-600">{{ $callingTicket ? \App\Models\QueueTicket::roomForService($callingTicket->service_type, $callingTicket->department) : 'Chưa có phòng đang gọi' }}</p>
                        </div>
                        <div class="rounded-3xl bg-cyan-50 p-5">
                            <p class="text-sm font-bold text-cyan-700">Số kế tiếp theo luật ưu tiên</p>
                            <p id="dashboard-next-number" class="mt-3 text-5xl font-black text-cyan-900">{{ $nextTicket?->displayNumber() ?? '---' }}</p>
                            <p id="dashboard-next-patient" class="mt-2 text-sm text-cyan-800">{{ $nextTicket?->patient_name ?? 'Chưa có bệnh nhân sẵn sàng' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Luồng điều phối</h2>
                    <div class="mt-5 space-y-3 text-sm font-medium leading-6 text-slate-600">
                        <p><span class="font-black text-slate-950">Chờ thanh toán</span> chỉ chuyển sang sẵn sàng khi HIS xác nhận.</p>
                        <p><span class="font-black text-slate-950">Cấp cứu mức 5</span> luôn đứng trước các mức ưu tiên khác.</p>
                        <p><span class="font-black text-slate-950">Vắng mặt</span> không tự quay lại hàng chờ, điều dưỡng phải gọi lại thủ công.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('queueChart');

            if (canvas && window.Chart) {
                new window.Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @json($hourlyLabels),
                        datasets: [{
                            label: 'Lượt khám',
                            data: @json($hourlyData),
                            tension: 0.42,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(14,165,233,0.14)',
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#7c3aed',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.20)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            const updateText = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                }
            };

            const refreshQueueStatus = async () => {
                try {
                    const response = await fetch('{{ route('queue-tickets.status') }}', {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (! response.ok) {
                        return;
                    }

                    const data = await response.json();
                    updateText('dashboard-waiting-count', data.waiting);
                    updateText('dashboard-ready-count', data.ready);
                    updateText('dashboard-missed-count', data.missed);
                    updateText('dashboard-completed-count', data.completed_today);
                    updateText('dashboard-average-wait', `${data.average_estimated_wait} phút`);
                    updateText('dashboard-calling-number', data.calling_ticket?.queue_number || '---');
                    updateText('dashboard-calling-patient', data.calling_ticket?.patient_name || 'Chưa có lượt đang gọi');
                    updateText('dashboard-current-number', data.calling_ticket?.queue_number || '---');
                    updateText('dashboard-current-room', data.calling_ticket?.room || 'Chưa có phòng đang gọi');
                    updateText('dashboard-next-number', data.next_ticket?.queue_number || '---');
                    updateText('dashboard-next-patient', data.next_ticket?.patient_name || 'Chưa có bệnh nhân sẵn sàng');
                } catch (error) {
                    // Polling is progressive enhancement.
                }
            };

            setInterval(refreshQueueStatus, 10000);
        });
    </script>
</x-app-layout>
