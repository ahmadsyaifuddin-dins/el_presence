@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.generate.attendance') }}">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i>
                            Generate Absensi Hari Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Karyawan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_employees'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Hadir Hari Ini</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['present_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tidak Hadir</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['absent_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-info-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Izin Hari Ini</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['on_leave_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance & Upcoming Holidays -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Attendance -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-clock mr-2"></i>
                    Absensi Terbaru Hari Ini
                </h3>
                <div class="flow-root">
                    <ul class="divide-y divide-gray-200">
                        @forelse($recent_attendances as $attendance)
                        <li class="py-3">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($attendance->status == 'Hadir') bg-green-100 text-green-800
                                        @elseif($attendance->status == 'Terlambat') bg-yellow-100 text-yellow-800
                                        @elseif($attendance->status == 'Izin') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $attendance->status }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $attendance->employee->full_name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        @if($attendance->checked_in_at)
                                            {{ $attendance->checked_in_at->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-3">
                            <p class="text-gray-500 text-center">Belum ada absensi hari ini</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Lihat semua absensi →
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    <i class="fas fa-calendar mr-2"></i>
                    Hari Libur Mendatang
                </h3>
                <div class="flow-root">
                    <ul class="divide-y divide-gray-200">
                        @forelse($upcoming_holidays as $holiday)
                        <li class="py-3">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ $holiday->date->format('d') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $holiday->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $holiday->date->format('d M Y') }}</p>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-3">
                            <p class="text-gray-500 text-center">Tidak ada hari libur mendatang</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.holidays.index') }}" 
                       class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Kelola hari libur →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection