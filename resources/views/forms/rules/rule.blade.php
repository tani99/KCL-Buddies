<tr>
    @php
        if (!isset($formColWidth)) $formColWidth = 15;
    @endphp
    <th width="{{ $formColWidth }}%" style="vertical-align: middle;">
        <label for="{{ $nameLc }}_input" title="{{ $description }}">{{ $name }}</label></th>
    <td>
        @include('forms.rules.' . $nameLc)
        <small class="error">{{ $errors->first($nameLc) }}</small>
    </td>
</tr>