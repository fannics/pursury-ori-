<h2>{{ trans('product.import_results.import_results') }}</h2>
<table class="table">
    <thead>
        <tr>
            <th class="text-center">
                @if ($import->type == 'products')
                  {{ trans('product.import_results.new_products') }}
                @else
                  {{ trans('product.import_results.new_categories') }}
                @endif
            </th>
            <th class="text-center">
                @if ($import->type == 'products')
                  {{ trans('product.import_results.updated') }}
                @else
                  {{ trans('product.import_results.updated_f') }}
                @endif
            </th>
            <th class="text-center">
                @if ($import->type == 'products')
                  {{ trans('product.import_results.removed') }}
                @else
                  {{ trans('product.import_results.removed_f') }}
                @endif
            </th>
            <th class="text-center">{{ trans('product.import_results.total') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">{{ $totals['added']['done'] }} / {{ $totals['added']['count'] }}</td>
            <td class="text-center">{{ $totals['updated']['done'] }} / {{ $totals['updated']['count'] }}</td>
            <td class="text-center">{{ $totals['removed']['done'] }} / {{ $totals['removed']['count'] }}</td>
            <td class="text-center">{{ $totals['added']['done'] + $totals['updated']['done'] + $totals['removed']['done'] }} / {{ $totals['added']['count'] + $totals['updated']['count'] + $totals['removed']['count'] }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">
                <p>{{ trans('product.import_results.imported_file') }}: <a target="_blank" href="{{ route('feed_url', ['type' => $import->type, 'feed' => $import->filename]) }}">{{ urldecode($import->filename) }}</a></p>
                <p>{{ trans('product.import_results.errors_log') }}: <a target="_blank" href="{{ route('admin_import_log_file', ['id' => $import->id]) }}">{{ $import->log_file }}</a></p>
            </td>
        </tr>
    </tfoot>
</table>
