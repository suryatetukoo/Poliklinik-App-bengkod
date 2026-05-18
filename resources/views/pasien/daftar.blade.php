<x-layouts.app title="Daftar Poli">

    <div class="flex items-center justify-center px-4">
        <div class="w-full max-w-3xl">
            <div class="card bg-base-100 shadow">
                <div class="card-body">

                    <h2 class="text-2xl font-bold text-center mb-6">
                        🏥 Pendaftaran Poli
                    </h2>

                    {{-- Toast Success --}}
                    @if (session('message') && session('type') == 'success')
                    <div id="toastSuccess" class="toast toast-top toast-end z-50">
                        <div class="alert alert-success shadow-lg">
                            <span>{{ session('message') }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Toast Error --}}
                    @if (session('message') && session('type') == 'error')
                    <div id="toastError" class="toast toast-top toast-end z-50">
                        <div class="alert alert-error shadow-lg">
                            <span>{{ session('message') }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                        {{-- Nomor RM --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1">Nomor Rekam Medis</label>
                            <input type="text" value="{{ $user->no_rm }}"
                                class="w-full border-2 rounded-lg p-2 bg-gray-100" disabled>
                        </div>

                        {{-- Pilih Poli --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1">Pilih Poli</label>
                            <select name="id_poli" id="poliSelect" class="w-full border-2 rounded-lg p-2" required>
                                <option value="">-- Pilih Poli --</option>
                                @foreach ($polis as $poli)
                                <option value="{{ $poli->id }}">{{ $poli->nama_poli }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Jadwal (dinamis sesuai poli) --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1">Pilih Jadwal Periksa</label>
                            <select name="id_jadwal" id="jadwalSelect" class="w-full border-2 rounded-lg p-2" required>
                                <option value="">-- Pilih poli terlebih dahulu --</option>
                                {{-- 
                                    Setiap option menyimpan id_poli dokternya di data-poli
                                    supaya bisa difilter via JavaScript saat poli dipilih
                                --}}
                                @foreach ($jadwals as $jadwal)
                                <option 
                                    value="{{ $jadwal->id }}" 
                                    data-poli="{{ $jadwal->dokter->id_poli }}"
                                    class="jadwal-option" 
                                    style="display:none">
                                    {{ $jadwal->hari }}, 
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} 
                                    — dr. {{ $jadwal->dokter->nama ?? '-' }}
                                    ({{ $jadwal->dokter->poli->nama_poli ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                            {{-- Pesan jika tidak ada jadwal tersedia --}}
                            <p id="noJadwalMsg" class="text-sm text-red-500 mt-1 hidden">
                                Tidak ada jadwal tersedia untuk poli ini.
                            </p>
                        </div>

                        {{-- Keluhan --}}
                        <div class="mb-6">
                            <label class="font-semibold block mb-1">Keluhan</label>
                            <textarea name="keluhan" rows="3" class="w-full border-2 rounded-lg p-2"
                                placeholder="Tulis keluhan anda..."></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                                Daftar Poli
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const poliSelect   = document.getElementById("poliSelect");
            const jadwalSelect = document.getElementById("jadwalSelect");
            const noJadwalMsg  = document.getElementById("noJadwalMsg");
            const allOptions   = jadwalSelect.querySelectorAll(".jadwal-option");

            poliSelect.addEventListener("change", function () {
                const poliId = this.value;

                // Reset dropdown jadwal
                jadwalSelect.value = "";
                let adaJadwal = false;

                allOptions.forEach(option => {
                    if (option.dataset.poli === poliId) {
                        option.style.display = "block";
                        adaJadwal = true;
                    } else {
                        option.style.display = "none";
                    }
                });

                // Tampilkan pesan jika tidak ada jadwal
                if (poliId && !adaJadwal) {
                    noJadwalMsg.classList.remove("hidden");
                } else {
                    noJadwalMsg.classList.add("hidden");
                }

                // Update placeholder dropdown jadwal
                jadwalSelect.options[0].text = adaJadwal
                    ? "-- Pilih Jadwal --"
                    : "-- Tidak ada jadwal tersedia --";
            });
        });
    </script>
    @endpush

</x-layouts.app>