<x-layouts.app title="Periksa Pasien">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Periksa Pasien</h2>
    </div>

    @if (session('success'))
    <div class="alert alert-success mb-4 rounded-xl shadow-sm">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if (session('message'))
    <div class="alert alert-{{ session('type') ?? 'success' }} mb-4 rounded-xl shadow-sm">
        <i class="fas fa-circle-check"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            {{-- TAMBAHAN: info poli dan jadwal supaya dokter tahu konteks pendaftaran --}}
                            <th class="px-6 py-4">Poli & Jadwal</th>
                            <th class="px-6 py-4">No Antrian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPasien as $dp)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-4 text-slate-500">{{ $loop->iteration }}</td>

                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800">{{ $dp->pasien->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $dp->pasien->no_rm ?? '-' }}</p>
                            </td>

                            <td class="px-6 py-4 text-slate-500">{{ $dp->keluhan }}</td>

                            {{-- 
                                Tampilkan nama poli dan jadwal praktek dari pendaftaran ini.
                                Relasi: DaftarPoli → jadwalPeriksa → dokter → poli
                            --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-indigo-600 text-sm">
                                    {{ $dp->jadwalPeriksa->dokter->poli->nama_poli ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $dp->jadwalPeriksa->hari }},
                                    {{ \Carbon\Carbon::parse($dp->jadwalPeriksa->jam_mulai)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($dp->jadwalPeriksa->jam_selesai)->format('H:i') }}
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <span class="badge badge-outline text-slate-600 px-3 py-1 font-bold">
                                    {{ $dp->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if ($dp->periksas->isNotEmpty())
                                    <span class="badge bg-green-100 text-green-700 border border-green-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-circle-check mr-1"></i>Sudah Diperiksa
                                    </span>
                                @else
                                    <a href="{{ route('periksa-pasien.create', $dp->id) }}"
                                        class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4">
                                        <i class="fas fa-stethoscope"></i> Periksa
                                    </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-14 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-inbox text-3xl"></i>
                                    <span>Tidak ada data pasien periksa</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.3s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 3000);
    </script>

</x-layouts.app>