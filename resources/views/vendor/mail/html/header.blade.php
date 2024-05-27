<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('\image\bpms\BPMS.png'))) }}" alt="Logo BPMS" width=auto height="60">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
