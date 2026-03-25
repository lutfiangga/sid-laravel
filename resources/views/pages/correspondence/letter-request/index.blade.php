<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterRequestService;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function export(LetterRequestService $service)
    {
        $data = $service->export(search: $this->search, with: ['penduduk', 'type']);

        return \App\Core\Support\Exporter::csv($data, [
            'created_at' => 'Tanggal',
            'nomor_surat' => 'Nomor Surat',
            'penduduk.nama' => 'Nama Pemohon',
            'penduduk.nik' => 'NIK Pemohon',
            'type.nama' => 'Jenis Surat',
            'workflow_status' => 'Status',
        ], 'data-permohonan-surat-' . now()->format('Y-m-d') . '.csv');
    }

    #[Computed]
    public function letterRequests()
    {
        return app(LetterRequestService::class)->getPaginated(
            search: $this->search,
            perPage: 10
        );
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Permohonan Surat') }}</flux:heading>
            <flux:subheading>{{ __('Daftar permohonan surat layanan warga.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('correspondence.letter-request.create') }}" variant="primary" icon="plus">
            {{ __('Buat Permohonan') }}
        </flux:button>
    </div>

    <flux:card>
        <div class="mb-4 flex items-center justify-between gap-4">
            <div class="w-full max-w-sm">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Cari nomor surat/status...') }}" icon="magnifying-glass" />
            </div>

            <flux:button wire:click="export" icon="arrow-down-tray">
                {{ __('Export CSV') }}
            </flux:button>
        </div>

            <flux:table :paginate="$this->letterRequests">
                <flux:table.columns>
                    <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                    <flux:table.column>{{ __('No. Surat') }}</flux:table.column>
                    <flux:table.column>{{ __('Pemohon') }}</flux:table.column>
                    <flux:table.column>{{ __('Jenis Surat') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->letterRequests as $request)
                        @php
                            $statusConfig = [
                                'draft' => ['color' => 'zinc', 'label' => 'Draft'],
                                'submitted' => ['color' => 'blue', 'label' => 'Diajukan'],
                                'rt_review' => ['color' => 'cyan', 'label' => 'Review RT'],
                                'rw_review' => ['color' => 'teal', 'label' => 'Review RW'],
                                'admin_review' => ['color' => 'amber', 'label' => 'Verifikasi Admin'],
                                'approved' => ['color' => 'emerald', 'label' => 'Selesai'],
                                'rejected' => ['color' => 'red', 'label' => 'Ditolak'],
                            ];
                            $config = $statusConfig[$request->workflow_status] ?? ['color' => 'zinc', 'label' => $request->workflow_status];
                        @endphp
                        <flux:table.row :key="$request->id">
                            <flux:table.cell>{{ $request->created_at->format('d/m/Y H:i') }}</flux:table.cell>
                            
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $request->nomor_surat ?? '-' }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex flex-col">
                                    <span>{{ $request->penduduk->nama ?? '-' }}</span>
                                    <span class="text-xs text-zinc-400">{{ $request->penduduk->nik ?? '' }}</span>
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>{{ $request->type->nama }}</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge :color="$config['color']" size="sm" class="uppercase" inset="top bottom">
                                    {{ $config['label'] }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                <flux:button href="{{ route('correspondence.letter-request.detail', $request->id) }}" size="sm" variant="ghost" icon="eye" inset="top bottom" />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
    </flux:card>
</section>
