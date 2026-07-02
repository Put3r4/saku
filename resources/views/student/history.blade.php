@extends('layouts.student')

@section('title', 'Riwayat Budget')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-saku-dark mb-2">
                📊 Riwayat Budget & Pilihan Menu
            </h1>
            <p class="text-saku-muted">
                Lihat histori budget Anda dan tren pengeluaran 14 hari terakhir.
            </p>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-bold text-saku-dark mb-4">Tren Pengeluaran (14 Hari Terakhir)</h2>
        <div class="relative" style="height: 300px;">
            <canvas id="budgetTrendChart"></canvas>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-saku-dark">Riwayat Pilihan Menu</h2>
        </div>

        @if($histories->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-bold text-saku-dark mb-2">Belum Ada Riwayat</h3>
                <p class="text-saku-muted mb-6">
                    Anda belum memilih menu apapun. Mulai dengan input budget di dashboard!
                </p>
                <a href="{{ route('student.dashboard') }}" class="inline-block bg-saku-accent hover:bg-saku-primary text-white font-bold py-3 px-6 rounded-lg transition">
                    Ke Dashboard
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-saku-muted uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-saku-muted uppercase tracking-wider">
                                Budget
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-saku-muted uppercase tracking-wider">
                                Menu Dipilih
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-saku-muted uppercase tracking-wider">
                                Vendor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-saku-muted uppercase tracking-wider">
                                Harga
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($histories as $history)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-saku-dark">
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    Rp {{ number_format($history->budget_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-saku-dark">
                                    @if($history->selectedMenu)
                                        {{ $history->selectedMenu->menu_name }}
                                    @else
                                        <span class="text-saku-muted italic">Menu tidak tersedia</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-saku-muted">
                                    @if($history->selectedMenu)
                                        {{ $history->selectedMenu->vendor_name }}
                                    @else
                                        <span class="text-saku-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-saku-dark">
                                    @if($history->selectedMenu)
                                        Rp {{ number_format($history->selectedMenu->price, 0, ',', '.') }}
                                    @else
                                        <span class="text-saku-muted italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $histories->links() }}
            </div>
        @endif
    </div>

    <!-- Back to Dashboard -->
    <div class="mt-8 text-center">
        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center text-saku-muted hover:text-saku-dark font-medium transition">
            <span class="mr-2">←</span> Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data from backend
    const chartData = @json($chartData);
    
    // Create chart
    const ctx = document.getElementById('budgetTrendChart').getContext('2d');
    const budgetTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Total Budget (Rp)',
                data: chartData.data,
                borderColor: '#D68438',
                backgroundColor: 'rgba(214, 132, 56, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#D68438',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Figtree',
                            size: 12,
                            weight: '600'
                        },
                        color: '#2A324A'
                    }
                },
                tooltip: {
                    backgroundColor: '#2A324A',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return 'Budget: Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: {
                            family: 'Figtree',
                            size: 11
                        },
                        color: '#7A82A6'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Figtree',
                            size: 11
                        },
                        color: '#7A82A6'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection
