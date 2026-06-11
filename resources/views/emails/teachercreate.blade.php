<h2>Welcome to PadmaKanya Multiple Campus</h2>

<p>Hello {{ $teacher->name }},</p>
<p>Your account has been successfully created as a <b>Teacher</b> in the QR Attendance System.</p>

<hr>
<p><b>Account Details:</b></p>
<p><b>Email:</b> {{ $teacher->email }}</p>
@if(isset($password))
<p><b>Temporary Password:</b> {{ $password }}</p>
@endif
<hr>

<p>
You can now login to your account using the link below:
</p>
<a href="{{ url('/home') }}">Login Here</a>
