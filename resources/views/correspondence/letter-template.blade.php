@php
    $marginTop = $template->margin_top ?? 20;
    $marginBottom = $template->margin_bottom ?? 20;
    $marginLeft = $template->margin_left ?? 25;
    $marginRight = $template->margin_right ?? 20;
    $orientation = $template->orientation ?? 'portrait';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $type->nama ?? 'Surat Keterangan' }}</title>
    <style>
        @page {
            margin: {{ $marginTop }}mm {{ $marginRight }}mm {{ $marginBottom }}mm {{ $marginLeft }}mm;
            size: A4 {{ $orientation }};
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .content {
            text-align: justify;
        }
        /* TinyMCE / Word-like adjustments */
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        table td, table th { border: 1px solid #ddd; padding: 8px; }
        .no-border td, .no-border th { border: none !important; }

        /* Legacy support for old templates */
        .page {
            /* padding no longer needed due to @page margin */
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header .village-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 11pt;
        }
        .letter-title {
            text-align: center;
            margin: 20px 0;
        }
        .letter-title h2 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0;
        }
        .letter-number {
            text-align: center;
            font-size: 12pt;
            margin-bottom: 15px;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .footer .signature-block {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        .footer .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="content">
        @if(!$template)
            {{-- Header / Kop Surat (Legacy) --}}
            <div class="header">
                <div class="village-name">PEMERINTAH DESA</div>
                <div class="subtitle">Kecamatan ... Kabupaten ... Provinsi ...</div>
                <div class="subtitle">Jl. Desa No. 1, Kode Pos XXXXX | Telp. 0000-000000</div>
            </div>

            {{-- Letter Title (Legacy) --}}
            <div class="letter-title">
                <h2>{{ $type->nama ?? 'Surat Keterangan' }}</h2>
            </div>
            <div class="letter-number">
                Nomor: {{ $letterRequest->nomor_surat ?? '___/___/' . now()->year }}
            </div>
        @endif

        {{-- Main Content --}}
        {!! $content !!}

        @if(!$template)
            {{-- Footer / Tanda Tangan (Legacy) --}}
            <div class="footer">
                <div class="signature-block">
                    <div>{{ now()->isoFormat('D MMMM Y') }}</div>
                    <div>Kepala Desa,</div>
                    <div class="signature-name">
                        ( _________________________ )
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
