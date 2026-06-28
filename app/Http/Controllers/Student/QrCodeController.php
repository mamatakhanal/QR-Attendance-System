<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Admin\Students;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{

    public function qrcode()
    {

        $student = Students::find(session('student_id'));


        if(!$student){

            return redirect('/home');

        }



        $data = json_encode([

            'student_id' => $student->id,

            'student_code' => $student->student_code

        ]);



        $qr = QrCode::size(250)
            ->generate($data);



        return view('student.qrcode',[

            'pageTitle'=>'My QR Code',

            'student'=>$student,

            'qr'=>$qr

        ]);


    }

}