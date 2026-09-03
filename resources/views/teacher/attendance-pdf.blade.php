<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Teacher Attendance Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2,
        h4 {
            text-align: center;
            margin: 0;
        }

        p {
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th {
            background: #e9ecef;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Teacher Attendance Report</h2>
    <br>
    <p><strong>Teacher:</strong> {{ $teacher->name }}</p>
    <p><strong>Generated Date:</strong> {{ now()->format('d M Y') }}</p>
    <p><strong>Generated Time:</strong> {{ now()->format('h:i A') }}</p>

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
                <th>Student</th>
                <th>Student Code</th>
                <th>Semester</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody>

            @forelse($attendances as $attendance)
                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $attendance->student->name ?? '-' }}</td>

                    <td>{{ $attendance->student->student_code ?? '-' }}</td>

                    <td>{{ $attendance->semester }}</td>

                    <td>{{ $attendance->subject->subject_name ?? '-' }}</td>

                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>

                    <td>
                        {{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : '-' }}
                    </td>

                    <td>{{ $attendance->status }}</td>

                </tr>

            @empty

                <tr>
                    <td colspan="8">No attendance records found.</td>
                </tr>
            @endforelse

        </tbody>

    </table>

</body>

</html>
