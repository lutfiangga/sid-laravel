<?php

declare(strict_types=1);

namespace Modules\Correspondence\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Modules\Correspondence\Models\LetterRequest;

class LetterPdfController
{
    /**
     * Generate and download an approved letter as a PDF.
     */
    public function __invoke(LetterRequest $letterRequest): Response
    {
        // Only approved letters can be downloaded
        abort_unless($letterRequest->workflow_status === 'approved', 403, 'Surat belum disetujui.');

        $letterRequest->load(['penduduk.kartuKeluarga', 'penduduk.rt', 'penduduk.rw', 'penduduk.dusun', 'type.letterTemplate']);

        $penduduk = $letterRequest->penduduk;
        $type = $letterRequest->type;
        $template = $type->letterTemplate;

        // Use new LetterTemplate or fallback to legacy template
        $contentHtml = $template ? $template->content : ($type->template ?? '');

        $replacements = [
            '{nama}' => $penduduk->nama ?? '-',
            '{nik}' => $penduduk->nik ?? '-',
            '{tempat_lahir}' => $penduduk->tempat_lahir ?? '-',
            '{tanggal_lahir}' => $penduduk->tanggal_lahir?->isoFormat('D MMMM Y') ?? '-',
            '{jenis_kelamin}' => $penduduk->jenis_kelamin ?? '-',
            '{pekerjaan}' => $penduduk->pekerjaan ?? '-',
            '{agama}' => $penduduk->agama ?? '-',
            '{status_perkawinan}' => $penduduk->status_perkawinan ?? '-',
            '{alamat}' => optional($penduduk->kartuKeluarga)->alamat ?? '-',
            '{nomor_surat}' => $letterRequest->nomor_surat ?? '-',
            '{tanggal_sekarang}' => now()->isoFormat('D MMMM Y'),
            '{keperluan}' => $letterRequest->keperluan ?? '-',
            '{nama_rt}' => optional($penduduk->rt)->nama ?? '-',
            '{nama_rw}' => optional($penduduk->rw)->nama ?? '-',
            '{nama_dusun}' => optional($penduduk->dusun)->nama ?? '-',
            '{nama_kades}' => config('app.village_head', 'Kepala Desa'),
        ];

        $contentHtml = str_replace(array_keys($replacements), array_values($replacements), $contentHtml);

        $pdf = Pdf::loadView('correspondence.letter-template', [
            'letterRequest' => $letterRequest,
            'type' => $type,
            'penduduk' => $penduduk,
            'content' => $contentHtml,
            'template' => $template,
        ]);

        if ($template) {
            $pdf->setPaper('a4', $template->orientation ?? 'portrait');
            // DOMPDF options are usually set via config or globally,
            // but we can pass paper size and orientation.
            // Actual margins are handled in the @page CSS inside the blade.
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        $filename = 'surat-'.($type->kode ?? 'keterangan').'-'.($penduduk->nik ?? now()->timestamp).'.pdf';

        return $pdf->download($filename);
    }
}
