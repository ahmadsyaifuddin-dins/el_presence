@extends('layouts.employee')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-2xl font-bold">Selamat datang, {{ $employee->full_name }}</h1>
                    <p class="text-blue-100">{{ $employee->position }} - {{ $employee->department }}</p>
                </div>
                <div class="text-right">
                    <p class="text-blue-100">{{ now()->format('l, d M Y') }}</p>
                    <p class="text-xl font-bold">{{ now()->format('H:i') }} WITA</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Actions -->
    @if($isWorkday)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">
            <i class="fas fa-clock mr-2 text-blue-500"></i>
            Absensi Hari Ini
        </h2>
        
        @if($todayAttendance)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Card -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-2 rounded-full flex items-center justify-center
                            @if($todayAttendance->status == 'Hadir') bg-green-100
                            @elseif($todayAttendance->status == 'Terlambat') bg-yellow-100
                            @elseif($todayAttendance->status == 'Izin') bg-blue-100
                            @else bg-red-100 @endif">
                            <i class="{{ $todayAttendance->getStatusIcon() }}
                                @if($todayAttendance->status == 'Hadir') text-green-500
                                @elseif($todayAttendance->status == 'Terlambat') text-yellow-500
                                @elseif($todayAttendance->status == 'Izin') text-blue-500
                                @else text-red-500 @endif text-2xl"></i>
                        </div>
                        <p class="font-semibold">{{ $todayAttendance->status }}</p>
                        @if($todayAttendance->late_minutes > 0)
                            <p class="text-sm text-yellow-600">Terlambat {{ $todayAttendance->late_minutes }} menit</p>
                        @endif
                    </div>
                </div>

                <!-- Check In -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-center">
                        @if($todayAttendance->time_in)
                            <div class="text-green-500 mb-2">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <p class="font-semibold">Masuk: {{ $todayAttendance->time_in->format('H:i') }}</p>
                            <p class="text-sm text-gray-600">Sudah absen masuk</p>
                        @else
                            @if($todayAttendance->status == 'Izin')
                                <div class="text-blue-500 mb-2">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                                <p class="font-semibold text-blue-600">Izin</p>
                                <p class="text-sm text-gray-600">{{ $todayAttendance->notes }}</p>
                            @else
                                <form method="POST" action="{{ route('employee.attendance.checkin') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Absen Masuk
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Check Out -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-center">
                        @if($todayAttendance->time_out)
                            <div class="text-blue-500 mb-2">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <p class="font-semibold">Pulang: {{ $todayAttendance->time_out->format('H:i') }}</p>
                            <p class="text-sm text-gray-600">Sudah absen pulang</p>
                        @elseif($todayAttendance->time_in && $todayAttendance->status != 'Izin')
                            <form method="POST" action="{{ route('employee.attendance.checkout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Absen Pulang
                                </button>
                            </form>
                        @else
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <p class="text-gray-500">Belum bisa absen pulang</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leave Request -->
            @if(!$todayAttendance->time_in && $todayAttendance->status == 'Alpa')
                <div class="mt-4 pt-4 border-t">
                    <button onclick="showLeaveModal()" 
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fas fa-hand-paper mr-2"></i>
                        Ajukan Izin
                    </button>
                </div>
            @endif
        @else
            <!-- No attendance record yet -->
            <div class="text-center py-8">
                <div class="mb-4">
                    <i class="fas fa-calendar-times text-6xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 mb-4">Belum ada record absensi untuk hari ini</p>
                <div class="space-y-2">
                    <form method="POST" action="{{ route('employee.attendance.checkin') }}" class="inline-block mr-2">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Absen Masuk
                        </button>
                    </form>
                    <button onclick="showLeaveModal()" 
                            class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                        <i class="fas fa-hand-paper mr-2"></i>
                        Ajukan Izin
                    </button>
                </div>
            </div>
        @endif
    </div>
    @else
        <!-- Holiday/Weekend -->
        <div class="bg-gray-100 rounded-lg p-6 text-center">
            <div class="mb-4">
                <i class="fas fa-calendar text-6xl text-gray-400"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Hari Libur</h2>
            <p class="text-gray-500">Selamat beristirahat! Tidak ada absensi hari ini.</p>
        </div>
    @endif

    <!-- Monthly Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">
            <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
            Statistik Bulan Ini
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $monthlyStats['total_days'] }}</div>
                <div class="text-sm text-gray-600">Total Hari</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $monthlyStats['present_days'] }}</div>
                <div class="text-sm text-gray-600">Hadir</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $monthlyStats['late_days'] }}</div>
                <div class="text-sm text-gray-600">Terlambat</div>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <div class="text-2xl font-bold text-orange-600">{{ $monthlyStats['leave_days'] }}</div>
                <div class="text-sm text-gray-600">Izin</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $monthlyStats['absent_days'] }}</div>
                <div class="text-sm text-gray-600">Alpa</div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance History -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">
                <i class="fas fa-history mr-2 text-blue-500"></i>
                Riwayat Terakhir
            </h2>
            <a href="{{ route('employee.attendance.history') }}" 
               class="text-blue-600 hover:text-blue-800 font-medium">
                Lihat Semua →
            </a>
        </div>
        <div class="overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentAttendances as $attendance)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $attendance->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($attendance->status == 'Hadir') bg-green-100 text-green-800
                                @elseif($attendance->status == 'Terlambat') bg-yellow-100 text-yellow-800
                                @elseif($attendance->status == 'Izin') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $attendance->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900">
                            {{ $attendance->time_in ? $attendance->time_in->format('H:i') : '-' }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900">
                            {{ $attendance->time_out ? $attendance->time_out->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Belum ada riwayat absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming Holidays -->
    @if($upcomingHolidays->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">
            <i class="fas fa-calendar mr-2 text-blue-500"></i>
            Hari Libur Mendatang
        </h2>
        <div class="space-y-3">
            @foreach($upcomingHolidays as $holiday)
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-sm font-semibold text-blue-600">
                            {{ $holiday->date->format('d') }}
                        </span>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="font-medium text-gray-900">{{ $holiday->name }}</div>
                    <div class="text-sm text-gray-500">{{ $holiday->date->format('l, d M Y') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Leave Request Modal -->
<div id="leaveModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajukan Permohonan Izin</h3>
                <button onclick="hideLeaveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('employee.attendance.leave') }}">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Izin <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Contoh: Sakit demam, Urusan keluarga, dll..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideLeaveModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Izin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showLeaveModal() {
    document.getElementById('leaveModal').classList.remove('hidden');
}

function hideLeaveModal() {
    document.getElementById('leaveModal').classList.add('hidden');
}

// Auto refresh clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit',
        timeZone: 'Asia/Makassar'
    });
    const clockElements = document.querySelectorAll('.current-time');
    clockElements.forEach(el => el.textContent = timeString + ' WITA');
}

setInterval(updateClock, 1000);
</script>
@endsection