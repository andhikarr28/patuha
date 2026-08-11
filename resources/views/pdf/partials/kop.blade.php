@php
    $logoPath = public_path('images/logo-patuha.png');
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
@endphp

<table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
    <tr>
        <td style="width:56px; vertical-align:middle; padding-right:12px;">
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" style="width:52px; height:52px; object-fit:contain;">
            @else
                <div style="width:52px; height:52px; background:#0f172a; color:#fff; text-align:center; line-height:52px; font-weight:bold; font-size:20px; border-radius:6px;">P</div>
            @endif
        </td>
        <td style="vertical-align:middle;">
            <div style="font-size:17px; font-weight:bold; letter-spacing:0.5px; color:#0f172a;">TOKO PATUHA OUTDOOR</div>
            <div style="font-size:10.5px; color:#475569; margin-top:2px;">Jl. Terusan Kopo No. 13, Bandung &middot; 0811-2278-811</div>
        </td>
        <td style="text-align:right; vertical-align:middle;">
            <div style="font-size:14px; font-weight:bold; text-transform:uppercase; color:#0f172a;">{{ $docTitle }}</div>
            @isset($docLines)
                @foreach($docLines as $line)
                    <div style="font-size:10.5px; color:#475569; margin-top:2px;">{{ $line }}</div>
                @endforeach
            @endisset
        </td>
    </tr>
</table>
<div style="border-bottom:3px solid #0f172a; margin-bottom:18px;"></div>