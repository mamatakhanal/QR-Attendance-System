<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Attendance Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-bottom: 15px;
        }

        .info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>Admin Attendance Report</h2>

    <div class="info">

        <p>
            <strong>Generated Date:</strong>
            {{ \Carbon\Carbon::parse($realDateTime['date'])->format('Y-m-d') }}
        </p>

        <p>
            <strong>Generated Time:</strong>
            {{ \Carbon\Carbon::parse($realDateTime['time'])->format('h:i A') }}
        </p>

        @if($request->filled('from_date') || $request->filled('to_date'))
            <p>
                <strong>Date Range:</strong>

                {{ $request->from_date ?? 'All' }}

                to

                {{ $request->to_date ?? 'All' }}
            </p>
        @endif

    </div>

    <table>

        <thead>
            <tr>
                <th>S.N</th>
                <th>Student</th>
                <th>Student Code</th>
                <th>Semester</th>
                <th>Teacher</th>
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

                    <td>
                        {{ $attendance->student->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $attendance->student->student_code ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $attendance->semester }}
                    </td>

                    <td>
                        {{ $attendance->teacher->name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ $attendance->subject->subject_name ?? 'N/A' }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') }}
                    </td>

                    <td>
                        {{ $attendance->time
                            ? \Carbon\Carbon::parse($attendance->time)->format('h:i A')
                            : '-' }}
                    </td>

                    <td>
                        {{ $attendance->status }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="9">
                        No attendance records found.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</body>
</html>