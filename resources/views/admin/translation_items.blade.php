@foreach($translations as $translation)
    <tr>
        <td class="base-key">{{ $translation->trans_key }}</td>
        <td class="base-message">
            @if ($translation->trans_source_trans)
                <span href="#">{{ $translation->trans_source_trans }}</span>
            @else
                <a href="#">Establecer</a>
            @endif
        </td>
        <td>
            @if($translation->translated_phrase)
                <a class="trans-popover-trigger" data-phrase-id="{{ $translation->trans_key }}" href="#">{{ $translation->translated_phrase }}</a>
            @else
                <a class="trans-popover-trigger" data-phrase-id="{{ $translation->trans_key }}" href="#"><i class="fa fa-flag"></i> Traducir</a>
            @endif
        </td>
    </tr>
@endforeach