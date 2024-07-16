@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://crm.portobrasilconsorcios.com.br/build/assets/logo-40750fa6.png" class="logo" alt="Laravel Logo" style="width:36px;height:36px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
