<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tachometer-alt mr-2"></i>
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Card 1 -->
                        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                            <div class="flex items-center">
                                <i class="fas fa-users text-3xl mr-4"></i>
                                <div>
                                    <h3 class="text-lg font-semibold">Total Karyawan</h3>
                                    <p class="text-2xl font-bold">150</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-3xl mr-4"></i>
                                <div>
                                    <h3 class="text-lg font-semibold">Hadir Hari Ini</h3>
                                    <p class="text-2xl font-bold">145</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card 3 -->
                        <div class="bg-red-500 text-white p-6 rounded-lg shadow-lg">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-3xl mr-4"></i>
                                <div>
                                    <h3 class="text-lg font-semibold">Tidak Hadir</h3>
                                    <p class="text-2xl font-bold">5</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test SweetAlert Button -->
                    <div class="mt-6">
                        <button onclick="testSweetAlert()" 
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-bell mr-2"></i>
                            Test SweetAlert
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testSweetAlert() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'SweetAlert2 sudah berjalan dengan baik!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }
    </script>
</x-app-layout>