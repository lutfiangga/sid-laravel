<?php

declare(strict_types=1);

use Modules\Population\Contracts\Services\PendudukServiceInterface;
use Modules\Population\Contracts\Services\KartuKeluargaServiceInterface;
use Modules\Population\Models\Penduduk;
use Modules\Population\Http\Requests\Penduduk\StorePendudukRequest;
use Modules\Population\Http\Requests\Penduduk\UpdatePendudukRequest;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;

new #[Title('Form Penduduk')] class extends Component {
    public ?Penduduk $penduduk = null;

    public string $kartu_keluarga_id = '';
    public string $nik = '';
    public string $nama = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $jenis_kelamin = 'L';
    public string $agama = 'Islam';
    public string $status_perkawinan = 'Belum Kawin';
    public string $pekerjaan = '';
    public string $pendidikan_terakhir = 'SMA';
    public string $golongan_darah = 'O';
    public string $status_dalam_keluarga = 'Anak';
    public string $kewarganegaraan = 'WNI';
    public ?string $telepon = '';
    public ?string $email = '';
    public string $status = 'Aktif';

    public function mount(?Penduduk $penduduk = null): void
    {
        if ($penduduk && $penduduk->exists) {
            $this->penduduk = $penduduk;
            $this->kartu_keluarga_id = $penduduk->kartu_keluarga_id;
            $this->nik = $penduduk->nik;
            $this->nama = $penduduk->nama;
            $this->tempat_lahir = $penduduk->tempat_lahir;
            $this->tanggal_lahir = $penduduk->tanggal_lahir->format('Y-m-d');
            $this->jenis_kelamin = $penduduk->jenis_kelamin;
            $this->agama = $penduduk->agama;
            $this->status_perkawinan = $penduduk->status_perkawinan;
            $this->pekerjaan = $penduduk->pekerjaan;
            $this->pendidikan_terakhir = $penduduk->pendidikan_terakhir;
            $this->golongan_darah = $penduduk->golongan_darah;
            $this->status_dalam_keluarga = $penduduk->status_dalam_keluarga;
            $this->kewarganegaraan = $penduduk->kewarganegaraan;
            $this->telepon = $penduduk->telepon;
            $this->email = $penduduk->email;
            $this->status = $penduduk->status;
        }
    }

    #[Computed]
    public function kartuKeluargas()
    {
        return app(KartuKeluargaServiceInterface::class)->getAll();
    }

    public function save(PendudukServiceInterface $service)
    {
        $request = $this->penduduk ? new UpdatePendudukRequest() : new StorePendudukRequest();
        
        $validated = $this->validate($request->rules());

        if ($this->penduduk) {
            $service->update($this->penduduk->id, $validated);
        } else {
            $service->create($validated);
        }

        return redirect()->route('population.penduduk.index');
    }
}; ?>

<section class="w-full">
    <div class="mb-6">
        <flux:heading size="xl">{{ $penduduk ? __('Edit Penduduk') : __('Tambah Penduduk') }}</flux:heading>
        <flux:subheading>{{ __('Lengkapi data biografi penduduk di bawah ini.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Section 1: Identitas Dasar -->
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>{{ __('Kartu Keluarga') }}</flux:label>
                        <flux:select wire:model="kartu_keluarga_id" placeholder="{{ __('Pilih KK...') }}">
                            @foreach ($this->kartuKeluargas as $kk)
                                <flux:select.option value="{{ $kk->id }}">
                                    {{ $kk->nomor_kk }} - {{ $kk->kepala_keluarga }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="kartu_keluarga_id" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('NIK') }}</flux:label>
                        <flux:input wire:model="nik" placeholder="16 digit NIK" />
                        <flux:error name="nik" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Nama Lengkap') }}</flux:label>
                        <flux:input wire:model="nama" placeholder="Nama lengkap sesuai identitas" />
                        <flux:error name="nama" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Tempat Lahir') }}</flux:label>
                            <flux:input wire:model="tempat_lahir" />
                            <flux:error name="tempat_lahir" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Tanggal Lahir') }}</flux:label>
                            <flux:input wire:model="tanggal_lahir" type="date" />
                            <flux:error name="tanggal_lahir" />
                        </flux:field>
                    </div>

                    <flux:radio.group wire:model="jenis_kelamin" label="{{ __('Jenis Kelamin') }}">
                        <flux:radio value="L" label="Laki-laki" />
                        <flux:radio value="P" label="Perempuan" />
                    </flux:radio.group>
                    <flux:error name="jenis_kelamin" />
                </div>

                <!-- Section 2: Informasi Tambahan -->
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>{{ __('Agama') }}</flux:label>
                        <flux:select wire:model="agama">
                            <flux:select.option value="Islam">Islam</flux:select.option>
                            <flux:select.option value="Kristen">Kristen</flux:select.option>
                            <flux:select.option value="Katolik">Katolik</flux:select.option>
                            <flux:select.option value="Hindu">Hindu</flux:select.option>
                            <flux:select.option value="Budha">Budha</flux:select.option>
                            <flux:select.option value="Konghucu">Konghucu</flux:select.option>
                        </flux:select>
                        <flux:error name="agama" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Status Perkawinan') }}</flux:label>
                        <flux:select wire:model="status_perkawinan">
                            <flux:select.option value="Belum Kawin">Belum Kawin</flux:select.option>
                            <flux:select.option value="Kawin">Kawin</flux:select.option>
                            <flux:select.option value="Cerai Hidup">Cerai Hidup</flux:select.option>
                            <flux:select.option value="Cerai Mati">Cerai Mati</flux:select.option>
                        </flux:select>
                        <flux:error name="status_perkawinan" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Pekerjaan') }}</flux:label>
                        <flux:input wire:model="pekerjaan" />
                        <flux:error name="pekerjaan" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>{{ __('Pendidikan') }}</flux:label>
                            <flux:select wire:model="pendidikan_terakhir">
                                <flux:select.option value="Tidak Sekolah">Tidak Sekolah</flux:select.option>
                                <flux:select.option value="SD">SD</flux:select.option>
                                <flux:select.option value="SMP">SMP</flux:select.option>
                                <flux:select.option value="SMA">SMA</flux:select.option>
                                <flux:select.option value="Diploma">Diploma</flux:select.option>
                                <flux:select.option value="S1">S1</flux:select.option>
                                <flux:select.option value="S2">S2</flux:select.option>
                                <flux:select.option value="S3">S3</flux:select.option>
                            </flux:select>
                            <flux:error name="pendidikan_terakhir" />
                        </flux:field>
                        <flux:field>
                            <flux:label>{{ __('Gol. Darah') }}</flux:label>
                            <flux:select wire:model="golongan_darah">
                                <flux:select.option value="A">A</flux:select.option>
                                <flux:select.option value="B">B</flux:select.option>
                                <flux:select.option value="AB">AB</flux:select.option>
                                <flux:select.option value="O">O</flux:select.option>
                                <flux:select.option value="-">-</flux:select.option>
                            </flux:select>
                            <flux:error name="golongan_darah" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Hubungan Keluarga') }}</flux:label>
                        <flux:select wire:model="status_dalam_keluarga">
                            <flux:select.option value="Kepala Keluarga">Kepala Keluarga</flux:select.option>
                            <flux:select.option value="Suami">Suami</flux:select.option>
                            <flux:select.option value="Istri">Istri</flux:select.option>
                            <flux:select.option value="Anak">Anak</flux:select.option>
                            <flux:select.option value="Menantu">Menantu</flux:select.option>
                            <flux:select.option value="Cucu">Cucu</flux:select.option>
                            <flux:select.option value="Orang Tua">Orang Tua</flux:select.option>
                            <flux:select.option value="Mertua">Mertua</flux:select.option>
                        </flux:select>
                        <flux:error name="status_dalam_keluarga" />
                    </flux:field>
                </div>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <flux:field>
                    <flux:label>{{ __('Telepon / HP') }}</flux:label>
                    <flux:input wire:model="telepon" />
                    <flux:error name="telepon" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Email') }}</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8">
                <flux:button href="{{ route('population.penduduk.index') }}" variant="ghost">Batal</flux:button>
                <flux:button type="submit" variant="primary">Simpan Penduduk</flux:button>
            </div>
        </form>
    </flux:card>
</section>
