Set w=CreateObject("WScript.Shell")
w.CurrentDirectory="C:\xampp\htdocs\QR-Attendance-System"
w.Run """C:\xampp\php\php.exe"" artisan schedule:run",1,True
WScript.Sleep 1000