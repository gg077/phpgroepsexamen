<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
	            <?php
	                if($database->connection){
						echo "ok, connectie gemaakt met de database";
	                }
	            ?>
                <h4>Profile Visit</h4>
            </div>
            <div class="card-body">
                <div id="chart-profile-visit"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4>Profile Visit</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-primary" width="32" height="32" fill="blue"
                                 style="width:10px">
                                <use
                                    xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3">Europe</h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="mb-0 text-end">862</h5>
                    </div>
                    <div class="col-12">
                        <div id="chart-europe"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-success" width="32" height="32" fill="blue"
                                 style="width:10px">
                                <use
                                    xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3">America</h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="mb-0 text-end">375</h5>
                    </div>
                    <div class="col-12">
                        <div id="chart-america"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7">
                        <div class="d-flex align-items-center">
                            <svg class="bi text-danger" width="32" height="32" fill="blue"
                                 style="width:10px">
                                <use
                                    xlink:href="assets/static/images/bootstrap-icons.svg#circle-fill" />
                            </svg>
                            <h5 class="mb-0 ms-3">Indonesia</h5>
                        </div>
                    </div>
                    <div class="col-5">
                        <h5 class="mb-0 text-end">1025</h5>
                    </div>
                    <div class="col-12">
                        <div id="chart-indonesia"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4>Latest Comments</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-lg">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Comment</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="./assets/compiled/jpg/5.jpg">
                                    </div>
                                    <p class="font-bold ms-3 mb-0">Si Cantik</p>
                                </div>
                            </td>
                            <td class="col-auto">
                                <p class=" mb-0">Congratulations on your graduation!</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="./assets/compiled/jpg/2.jpg">
                                    </div>
                                    <p class="font-bold ms-3 mb-0">Si Ganteng</p>
                                </div>
                            </td>
                            <td class="col-auto">
                                <p class=" mb-0">Wow amazing design! Can you make another tutorial for
                                    this design?</p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>