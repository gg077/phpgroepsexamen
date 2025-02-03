<?php
// Haal de ingelogde gebruiker op via de session
$user = $session->get_logged_in_user();
?>
</div>
<div class="col-12 col-lg-3">
    <div class="card">
        <div class="card-body py-4 px-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="avatar avatar-xl">
                    <img src="./assets/compiled/jpg/1.jpg" alt="Face 1">
                </div>
                <div class="ms-3 name">
	                <h5 class="font-bold"><?php echo $user->first_name . " " . $user->last_name; ?></h5>
	                <h6 class="text-muted mb-0">@<?php echo $user->username; ?></h6>
                </div>
	            <a class="text-danger display-3 text-center font-bold" href="../logout.php"><i class="bi bi-power"></i></a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Recent Messages</h4>
        </div>
        <div class="card-content pb-4">
            <div class="recent-message d-flex px-4 py-3">
                <div class="avatar avatar-lg">
                    <img src="./assets/compiled/jpg/4.jpg">
                </div>
                <div class="name ms-4">
                    <h5 class="mb-1">Hank Schrader</h5>
                    <h6 class="text-muted mb-0">@johnducky</h6>
                </div>
            </div>
            <div class="recent-message d-flex px-4 py-3">
                <div class="avatar avatar-lg">
                    <img src="./assets/compiled/jpg/5.jpg">
                </div>
                <div class="name ms-4">
                    <h5 class="mb-1">Dean Winchester</h5>
                    <h6 class="text-muted mb-0">@imdean</h6>
                </div>
            </div>
            <div class="recent-message d-flex px-4 py-3">
                <div class="avatar avatar-lg">
                    <img src="./assets/compiled/jpg/1.jpg">
                </div>
                <div class="name ms-4">
                    <h5 class="mb-1">John Dodol</h5>
                    <h6 class="text-muted mb-0">@dodoljohn</h6>
                </div>
            </div>
            <div class="px-4">
                <button class='btn btn-block btn-xl btn-outline-primary font-bold mt-3'>Start Conversation</button>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Visitors Profile</h4>
        </div>
        <div class="card-body">
            <div id="chart-visitors-profile"></div>
        </div>
    </div>
</div>
</section>
</div>