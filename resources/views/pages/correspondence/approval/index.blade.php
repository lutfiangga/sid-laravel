<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Correspondence\Services\LetterRequestService;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    
    public function export(LetterRequestService $service)
    {
        $data = $service->export(search: $this->search);
        
        if ($data->isEmpty()) {
            $this->dispatch('notify', message: __('Tidak ada data untuk diekspor.'));
            return;
        }

        $firstItem = collect($data->first()->toArray())->except(['id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'password', 'remember_token'])->toArray();
        $columns = [];
        foreach (array_keys($firstItem) as $key) {
            if (!is_array($firstItem[$key])) {
                $columns[$key] = ucwords(str_replace('_', ' ', $key));
            }
        }

        return \App\Core\Support\Exporter::csv($data, $columns, 'export-' . now()->format('Y-m-d') . '.csv');
    }


    #[Computed]
    public function pendingRequests()
    {
        // Officials only see what's pending their review
        // For now, let's show all that are NOT approved/rejected/draft
        return app(LetterRequestService::class)->getPaginated(
            filters: ['workflow_status' => ['submitted', 'rt_review', 'rw_review', 'admin_review']],
            perPage: 10
        );
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Kotak Masuk Persetujuan') }}</flux:heading>
            <flux:subheading>{{ __('Daftar permohonan surat yang memerlukan tindakan Anda.') }}</flux:subheading>
        </div>
    </div>

    <flux:card>
        <flux:table :paginate="$this->pendingRequests">
            <flux:table.columns>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Pemohon') }}</flux:table.column>
                <flux:table.column>{{ __('Jenis Surat') }}</flux:table.column>
                <flux:table.column>{{ __('Status Saat Ini') }}</flux:table.column>
                <flux:table.column align="right">{{ __('Aksi') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->pendingRequests as $request)
                    <flux:table.row :key="$request->id">
                        <flux:table.cell>{{ $request->created_at->diffForHumans() }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $request->penduduk->nama }}</div>
                            <div class="text-xs text-zinc-400">{{ $request->penduduk->nik }}</div>
                        </flux:table.cell>

                        <flux:table.cell>{{ $request->type->nama }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:badge variant="neutral" size="sm" class="uppercase" inset="top bottom">
                                {{ str_replace('_', ' ', $request->workflow_status) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="right">
                            <flux:button href="{{ route('correspondence.letter-request.detail', $request->id) }}" variant="primary" size="sm" inset="top bottom">
                                {{ __('Tinjau') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
