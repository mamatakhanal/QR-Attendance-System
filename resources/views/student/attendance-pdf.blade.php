<!DOCTYPE html>
<html>

<head>
    <title>Attendance Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #eee;
        }

        h2,
        h4 {
            text-align: center;
        }
    </style>

</head>

<body>

    <h2>Student Attendance Report</h2>
    <br>
    <p><strong>Student:</strong> {{ $student->name }}</p>
    <p><strong>Semester:</strong> {{ $student->current_semester }}</p>

    <p>
        <strong>Generated Date:</strong>
        {{ \Carbon\Carbon::parse($realDateTime['date'])->format('d M Y') }}
    </p>

    <p>
        <strong>Generated Time:</strong>
        {{ \Carbon\Carbon::parse($realDateTime['time'])->format('h:i A') }}
    </p>

    @if (request('from_date') || request('to_date'))
        <p>
            <strong>Date Range:</strong>
            {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'All' }}
            -
            {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'All' }}
        </p>
    @endif

    <table>
        <thead>
            <tr>
                <th>S.N</th>
                <th>Date</th>
                <th>Subject</th>
                <th>Teacher</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($attendances as $attendance)
                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                    </td>

                    <td>
                        {{ $attendance->subject->subject_name ?? '-' }}
                    </td>

                    <td>
                        {{ $attendance->teacher->name ?? '-' }}
                    </td>

                    <td>
                        {{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : '-' }}
                    </td>

                    <td>
                        {{ $attendance->status }}
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>


</body>

</html>
