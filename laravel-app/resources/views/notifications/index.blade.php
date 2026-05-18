<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-700">Theo dõi thông báo</p>
            <h1 class="mt-2 text-3xl font-black text-slate-950">Nhật ký thông báo</h1>
            <p class="mt-2 text-slate-600">Theo dõi cảnh báo gần tới lượt, lượt đang gọi và trạng thái gửi sang workflow.</p>
        </div>
    </x-slot>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-hidden rounded-2xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-4 py-4">Bệnh nhân</th>
                        <th class="px-4 py-4">Điện thoại</th>
                        <th class="px-4 py-4">Số phiếu</th>
                        <th class="px-4 py-4">Loại</th>
                        <th class="px-4 py-4">Kênh</th>
                        <th class="px-4 py-4">Trạng thái</th>
                        <th class="px-4 py-4">Đã gửi lúc</th>
                        <th class="px-4 py-4">Nội dung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm text-slate-700">
                    @forelse ($notifications as $notification)
                        <tr>
                            <td class="px-4 py-4 font-semibold text-slate-950">{{ $notification->patient_name }}</td>
                            <td class="px-4 py-4">{{ $notification->patient_phone ?: 'Chưa có' }}</td>
                            <td class="px-4 py-4">{{ $notification->queueTicket?->displayNumber() }}</td>
                            <td class="px-4 py-4">{{ $notification->type }}</td>
                            <td class="px-4 py-4">{{ $notification->channel }}</td>
                            <td class="px-4 py-4">{{ $notification->status }}</td>
                            <td class="px-4 py-4">{{ $notification->sent_at?->format('d/m/Y H:i') ?: 'Chưa gửi' }}</td>
                            <td class="px-4 py-4">{{ $notification->message }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-500">Chưa có thông báo nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
