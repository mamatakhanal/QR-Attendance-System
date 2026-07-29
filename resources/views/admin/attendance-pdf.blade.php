<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>

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

    <h2>Attendance Report</h2>
    <br>
    <p><strong>Generated Date:</strong> {{ now()->format('d M Y') }}</p>
    <p><strong>Generated Time:</strong> {{ now()->format('h:i A') }}</p>

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

                    <td>{{ $attendance->student->name ?? '-' }}</td>

                    <td>{{ $attendance->student->student_code ?? '-' }}</td>

                    <td>{{ $attendance->semester }}</td>

                    <td>{{ $attendance->teacher->name ?? '-' }}</td>

                    <td>{{ $attendance->subject->subject_name ?? '-' }}</td>

                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>

                    <td>
                        {{ $attendance->time ? \Carbon\Carbon::parse($attendance->time)->format('h:i A') : '-' }}
                    </td>

                    <td>{{ $attendance->status }}</td>

                </tr>

            @empty

                <tr>
                    <td colspan="9">No attendance records found.</td>
                </tr>
            @endforelse

        </tbody>

    </table>

</body>

</html>
