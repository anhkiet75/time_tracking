@php
    $groupedData = $data
        ->groupBy(function ($item) {
            return substr($item['checkin_time'], 0, 10); // Group by date part of check_in
        })
        ->map(function ($group) {
            $sum = collect($group)->sum('log_time');
            return [
                'items' => $group,
                'sum' => $sum,
            ];
        });

    $max_datetime = new DateTime($data->pluck('checkin_time')->max());
    $max_date = $max_datetime->format('d-m-Y');
    $min_datetime = new DateTime($data->pluck('checkin_time')->min());
    $min_date = $min_datetime->format('d-m-Y');
    $total_log_time = $data->sum('log_time');
@endphp

<br>
<br>
<table>
    <tr>
        <th>Name</th>
        <td>{{ $user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>Period</td>
        <td>{{ $min_date }}-{{ $max_date }}</td>
    </tr>
    <tr>
        <th>Email</th>
        <td>{{ $user->email }}</td>
    </tr>
    <tr>
        <th>Birthdate</th>
        <td>{{ $user->birthdate }}</td>
    </tr>

</table>

<br>
<br>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Location</th>
            <th>From</th>
            <th>Break</th>
            <th>To</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($groupedData as $checkInOutGroup)
            @foreach ($checkInOutGroup['items'] as $index => $checkInOut)
                <tr>
                    @php
                        $checkin_time = new DateTime($checkInOut['checkin_time']);
                        $checkout_time = new DateTime($checkInOut['checkout_time']);
                        $log_time = round($checkInOut['log_time'] / 60 ,1);
                        $checkin_date = $checkin_time->format('d-m-Y');
                        $from = $checkin_time->format('H:i');
                        $to = $checkout_time->format('H:i');
                    @endphp
                    <td>
                        {{ $index == 0 ? $checkin_date : '' }}
                    </td>
                    <td>
                        {{ $checkInOut['location']['name'] }}
                    </td>
                    <td>
                        {{ $from }}
                    </td>
                    <td>
                        {{ $checkInOut['break_time'] }}
                    </td>
                    <td>
                        {{ $to }}
                    </td>
                    <td>
                        {{ $log_time }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                @php
                    $sum = round($checkInOutGroup['sum'] / 60, 1);
                @endphp
                <td>Total {{ $sum }}</td>
            </tr>
        @empty
            <p>No data available.</p>
        @endforelse
        <tr></tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total this period</td>
            <td>{{ round($total_log_time / 60, 1) }}</td>
        </tr>
    </tbody>
</table>
