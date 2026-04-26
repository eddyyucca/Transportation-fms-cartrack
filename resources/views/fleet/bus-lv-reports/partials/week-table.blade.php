<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0 text-nowrap">
        <thead class="bg-light">
            <tr>
                <th style="min-width: 210px">{{ $title }}</th>
                @foreach($labels as $index => $label)
                    <th class="text-center" data-day-index="{{ $index }}">{{ $label }}</th>
                @endforeach
                <th class="text-center">Total</th>
                <th class="text-center">Avg</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <th data-row-name="{{ $row['key'] }}">{{ $row['label'] }}</th>
                    @foreach($row['values'] as $index => $value)
                        <td>
                            <input
                                type="number"
                                min="0"
                                class="form-control form-control-sm text-center"
                                name="{{ $row['key'] }}[]"
                                value="{{ $value }}"
                                data-row="{{ $row['key'] }}"
                            >
                        </td>
                    @endforeach
                    <td class="text-center font-weight-bold" data-total="{{ $row['key'] }}">{{ collect($row['values'])->sum() }}</td>
                    <td class="text-center font-weight-bold" data-avg="{{ $row['key'] }}">{{ number_format(collect($row['values'])->sum() / 7, 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
