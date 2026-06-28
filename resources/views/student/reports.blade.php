<head>
    <title>Notice - Student</title>
    @include('layouts.link')
    @include('layouts.style')
</head>


<body>

<div class="main-wrapper">

    @include('student.sidebar')


    <div class="main-area">

        @include('student.navbar')


        <div class="main-content">


            <div class="card shadow-sm border-0 rounded-4 mx-2 my-2">


                <div class="card-body p-4">


                    <h5 class="fw-semibold mb-4">
                        Notice
                    </h5>



                    <!-- Notice Item -->

                    <div class="bg-light rounded-3 p-3 mb-3 shadow-sm">


                        <div class="d-flex justify-content-between">


                            <h6 class="fw-semibold mb-1">

                                Exam Notice

                            </h6>


                            <small class="text-muted">

                                28 June 2026

                            </small>


                        </div>



                        <p class="text-muted mb-0">

                            Final exam will start from 10 July.

                        </p>


                    </div>





                    <div class="bg-light rounded-3 p-3 mb-3 shadow-sm">


                        <div class="d-flex justify-content-between">


                            <h6 class="fw-semibold mb-1">

                                Class Update

                            </h6>


                            <small class="text-muted">

                                25 June 2026

                            </small>


                        </div>



                        <p class="text-muted mb-0">

                            Database class timing changed.

                        </p>


                    </div>





                    <!-- Empty -->

                    <!--
                    <p class="text-center text-muted">
                        No notice available
                    </p>
                    -->


                </div>


            </div>


        </div>


    </div>

</div>


</body>