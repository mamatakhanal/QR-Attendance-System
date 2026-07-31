Set w = CreateObject("WScript.Shell")

w.CurrentDirectory = "C:\xampp\htdocs\QR-Attendance-System"

w.Run """C:\xampp\php\php.exe"" artisan schedule:work", 0, False