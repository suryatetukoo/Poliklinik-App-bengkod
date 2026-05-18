<x-layouts.app title="Data Poli">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Data Poli</h2>
        <a href="{{ route('polis.create') }}" class="btn bg-[#2d4499] hover:bg-[#1e2d6b] text-white border-none rounded-lg px-5">
            <i class="fas fa-plus"></i> Tambah Poli
        </a>
    </div>

    @if(session('error'))
    <div class="alert alert-error mb-4 rounded-xl shadow-sm">
        <i class="fas fa-circle-xmark"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Poli</th>
                            <th class="px-6 py-4">Keterangan</th>
                            {{-- TAMBAHAN: kolom dokter beserta jadwal prakteknya --}}
                            <th class="px-6 py-4">Dokter & Jadwal Praktek</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polis as $poli)
                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $poli->nama_poli }}
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $poli->keterangan ?? '-' }}
                            </td>

                            {{-- 
                                Tampilkan semua dokter yang bertugas di poli ini
                                beserta jadwal prakteknya masing-masing.
                                Relasi: Poli → dokters (hasMany User) → jadwalPeriksa (hasMany JadwalPeriksa)
                            --}}
                            <td class="px-6 py-4">
                                @if($poli->dokters->isEmpty())
                                    <span class="text-slate-400 text-sm">Belum ada dokter</span>
                                @else
                                    <div class="flex flex-col gap-2">
                                        @foreach($poli->dokters as $dokter)
                                        <div class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2">
                                            <p class="font-semibold text-slate-700 text-sm mb-1">
                                                <i class="fas fa-user-doctor text-indigo-400 mr-1"></i>
                                                dr. {{ $dokter->nama }}
                                            </p>
                                            @if($dokter->jadwalPeriksa->isEmpty())
                                                <p class="text-xs text-slate-400">Belum ada jadwal praktek</p>
                                            @else
                                                <div class="flex flex-col gap-1">
                                                    @foreach($dokter->jadwalPeriksa as $jadwal)
                                                    <span class="text-xs text-slate-500">
                                                        <i class="fas fa-clock text-slate-300 mr-1"></i>
                                                        {{ $jadwal->hari }}, 
                                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} – 
                                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('polis.edit', $poli->id) }}"
                                        class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4">
                                        <i class="fas fa-pen-to-square"></i> Edit
                                    </a>
                                    <form action="{{ route('polis.destroy', $poli->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Yakin ingin menghapus poli ini?')"
                                            class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-lg px-4">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-14 text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                Belum ada data poli
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-layouts.app>