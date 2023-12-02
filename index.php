<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/custom.css">

    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/RequirementEnum.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = $cHandler = $bdHandler = $cBookings = null;
    $isSessionExists = false;
    $isAdmin = 'isadmin';
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["accountEmail"]);
        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());

        $bdHandler = new BookingDetailHandler();
        $cBookings = $bdHandler->getCustomerBookings($cHandler);
        $isSessionExists = true;
        if ($isAdmin === '0'){
          $isAdmin = $_SESSION["authenticated"];  
        }else{

        }
        
        
    }
    if (isset($_SESSION["isAdmin"]) && isset($_SESSION["username"])) {
        $isSessionExists = true;
        $username = $_SESSION["username"];
        $isAdmin = $_SESSION["isAdmin"];
    }

    ?>
    <title>Homepage</title>
</head>
<body>

<header>
    <div class=" bg-dark collapse" id="navbarHeader" style="">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-md-7 py-4">
                    <h4 class="text-white">Welcome</h4>
                    <p class="text-muted">Embark on a seamless journey of luxury with our exclusive hotel
                         booking experience, designed to elevate your stay and create unforgettable moments.</p>
                </div>
                <div class="col-sm-4 offset-md-1 py-4 text-right">
                    <?php if ($isSessionExists) { ?>
                    <h4 class="text-white"><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
                        <?php if ($isAdmin[1] == "true" && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "true") { ?>
                        <li><a href="admin.php" class="text-white">Manage customer reservation(s)<i class="far fa-address-book ml-2"></i></a></li>
                        <?php } else { ?>
                        <li><a href="#" class="text-white my-reservations">View bookings<i class="far fa-address-book ml-2"></i></a></li>
                        <li>
                            <a href="#" class="text-white" data-toggle="modal" data-target="#myProfileModal">Update profile<i class="fas fa-user ml-2"></i></a>
                        </li>
                        <?php } ?>
                        <li><a href="#" id="sign-out-link" class="text-white">Sign out<i class="fas fa-sign-out-alt ml-2"></i></a></li>
                    </ul>
                    <?php } else { ?>
                        <h4>
                                            <a href="sign-in.php" class="text-white">Sign in</a>
                                            <span class="text-white">or</span>
                                            <a href="register.php" class="text-white">Register</a>
                                            </h4>
                    <p class="text-muted">You can Log in so you can take advantage with our hotel room prices.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-dark bg-secondary box-shadow pt-5 pb-5">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center text-lg">
            <i class="fas fa-calendar-alt mr-2"></i>
                <strong> Book your Hotel</strong>
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="container my-3" id="my-reservations-div">
        <h4>Reservations</h4>
        <table id="myReservationsTbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th class="text-hide p-0" data-bookId="12">12</th>
                <th scope="col">Start date</th>
                <th scope="col">End date</th>
                <th scope="col">Room type</th>
                <th scope="col">Requirements</th>
                <th scope="col">Adults</th>
                <th scope="col">Children</th>
                <th scope="col">Requests</th>
                <th scope="col">Timestamp</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
                <?php   foreach ($cBookings as $k => $v) { ?>
                    <tr>
                        <th scope="row"><?php echo ($k + 1); ?></th>
                        <td class="text-hide p-0"><?php echo $v["id"]; ?></td>
                        <td><?php echo $v["start"]; ?></td>
                        <td><?php echo $v["end"]; ?></td>
                        <td><?php echo $v["type"]; ?></td>
                        <td><?php echo $v["requirement"]; ?></td>
                        <td><?php echo $v["adults"]; ?></td>
                        <td><?php echo $v["children"]; ?></td>
                        <td><?php echo $v["requests"]; ?></td>
                        <td><?php echo $v["timestamp"]; ?></td>
                        <td style="background-color: <?php
    if ($v["status"] === "CONFIRMED") {
        echo "#28a745";
    } elseif ($v["status"] === "PENDING") {
        echo "orange";
    } elseif ($v["status"] === "CANCELLED") {
        echo "#dc3545";
    } else {
        echo "black"; // Default color or handle other statuses
    }
?>;"><?php echo $v["status"]; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</header>

<main role="main">

    <section class="jumbotron text-center">
        <div class="container pt-lg-5 pl-5 px-5">
            <h1 class="display-2">Welcome to a realm beyond ordinary . </h1>
            <p class="lead text-muted">Step into luxury at our brand-new hotel — where extraordinary meets elegance.</p>
            <p>
                <?php if ($isSessionExists) { ?>
                <a href="#" class="btn btn-dark bg-dark my-2" data-toggle="modal" data-target=".book-now-modal-lg">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                <?php } else { ?>
                <a href="#" class="btn btn-dark my-2" data-toggle="modal" data-target=".sign-in-to-book-modal">Book now<i class="fas fa-angle-double-right ml-1"></i></a>
                <?php } ?>
            </p>
        </div>
    </section>
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block w-95 mx-auto " style="height: 80vh; width: 70%; display: block;" src="image/deluxes.jpg" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-95 mx-auto" style="height: 80vh; width: 70%; display: block;" src="image/Double.jpg" alt="Second slide">
    </div>
    <div class="carousel-item">
      <img class="d-block w-95 mx-auto" style="height: 80vh; width: 70%; display: block;" src="image/single.jpg" alt="Third slide">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

    <div class="container pricing" style="margin-top: 100px;">
        <div class="pricing-header fixed px-3 py-3 pt-md-5 pb-md-4 mt-10 mx-auto text-center">
            <h1 class="display-5">Discover your perfect room with our preferred selection </h1>
            <p class="lead">Explore our hotel room pricing – simple, stylish, and tailored for every preference. From Deluxe Rooms to Suites, find the perfect fit for your stay. Your comfort, our priority</p>
        </div>
    </div>

    <div class="album py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-800 mx-auto text-center">Deluxe Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="image/deluxes.jpg" data-holder-rendered="true">
                        <div class="card-body">
                            <p class="card-text">The ultimate sanctuary to recharge the senses, the beautifully-appointed 24sqm Deluxe Room exudes sheer sophistication and elegance. Located on the higher floors, each Deluxe Room is characterised by elevated ceilings and full length bay windows, transforming your living space into an atmospheric abode.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <?php if ($isSessionExists) { ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-rtype="Deluxe" data-toggle="modal" data-target=".book-now-modal-lg">
                                        Book
                                    </button>
                                    <?php } else { ?>
                                    <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                        Book
                                    </button>
                                    <?php } ?>
                                </div>
                                <small class="text-muted">$250 / night</small>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-800 mx-auto text-center">Double Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="image/Double.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">
                                                Indulge in the comfort of our standard twin room, featuring two cozy single beds for a delightful stay. Experience top-notch facilities and optimal security in our fully air-conditioned twin room. Your perfect choice for a memorable trip. Book with us for a delightful and carefree travel experience!</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-rtype="Double" data-toggle="modal" data-target=".book-now-modal-lg">
                                    Book
                                </button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                    Book
                                </button>
                                <?php } ?>
                                <small class="text-muted">$180 / night</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-800 mx-auto text-center">Single Room</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="image/single.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">Discover elegance in our intimate single room, complete with a hairdryer and luxurious complimentary toiletries. Enjoy modern conveniences like free WiFi, a telephone, a well-stocked minibar, and a flat-screen TV offering a diverse selection of channels and films.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-rtype="Single" data-target=".book-now-modal-lg">
                                    Book
                                </button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                    Book
                                </button>
                                <?php } ?>
                                <small class="text-muted">$130 / night</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div class="row" >
            <div class="col-md-7 mx-auto">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <h5 class="my-0 font-weight-800 mx-auto text-center ">Hotel Building </h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="image/hotel.jpg" data-holder-rendered="true">
                        <div class="card-body">
                            <p class="card-text">Discover an architectural masterpiece that transcends the ordinary – our hotel building stands as a testament to luxury, blending seamlessly with its surroundings. A symphony of modern design and timeless elegance, it beckons guests with an impressive facade and meticulous attention to detail. From the moment you set eyes on our hotel, you'll be captivated by its distinct charm, promising a stay that goes beyond accommodation – a true visual and sensory delight that sets the stage for an unparalleled experience.</p>
                            <div class="d-flex justify-content-between align-items-center">
                              <h4 style="text-align: center;" >Make your reservation now</h4>
                            </div>
                        </div>
                    </div>
                    
             <div>
                
            </div>

        </div>

    </div>

    <?php if(isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal fade book-now-modal-lg" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLarge" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="reservationModalBody">
                    <?php if ($isSessionExists == 1 && $isAdmin[1] == "false") { ?>
                        <form role="form" autocomplete="off" method="post" id="multiStepRsvnForm">
                            <div class="rsvnTab">
                                <?php if ($isSessionExists) { ?>
                                    <input type="number" name="cid" value="<?php echo $cHandler->getId() ?>" hidden>
                                <?php } ?>
                                <div class="form-group row">
                                    <label for="startDate" class="col-sm-3 col-form-label">Check-in
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"
                                                   name="startDate"  min="<?php echo Util::dateToday('0'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="endDate" class="col-sm-3 col-form-label">Check-out
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"  min="<?php echo Util::dateToday('1'); ?>" name="endDate" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomType">Room type
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="roomType">
                                            <option value="<?php echo \models\RequirementEnum::DELUXE; ?>">Deluxe room</option>
                                            <option value="<?php echo \models\RequirementEnum::DOUBLE; ?>">Double room</option>
                                            <option value="<?php echo \models\RequirementEnum::SINGLE; ?>">Single room</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomRequirement">Room requirements</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="roomRequirement">
                                            <option value="no preference" selected>No preference</option>
                                            <option value="non smoking">Non smoking</option>
                                            <option value="smoking">Smoking</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="adults">Adults
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="adults">
                                            <option selected value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="children">Children</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="children">
                                            <option selected value="0">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="specialRequests">Special requirements</label>
                                    <div class="col-sm-9">
                                        <textarea rows="3" maxlength="500"  name="specialRequests" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <button type="button" class="btn btn-info" style="margin-left: 0.8em;" data-container="body" data-toggle="popover"
                                            data-placement="right" data-content="Check-in time starts at 3 PM. If a late check-in is planned, please contact our support department.">
                                        Check-in policies
                                    </button>
                                </div>
                            </div>

                            <div class="rsvnTab">
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="bookedDate">Booked Date</label>
                                    <div class="col-sm-9 bookedDateTxt">
                                        July 13, 2019
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="roomPrice">Room Price</label>
                                    <div class="col-sm-9 roomPriceTxt">235.75</div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights"><span class="numNightsTxt">3</span> nights </label>
                                    <div class="col-sm-9">
                                        $<span class="roomPricePerNightTxt">69.63</span> avg. / night
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights">From - to</label>
                                    <div class="col-sm-9 fromToTxt">
                                        Mon. July 4 to Wed. July 6
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Taxes </label>
                                    <div class="col-sm-9">
                                        $<span class="taxesTxt">0</span>
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Total </label>
                                    <div class="col-sm-9">
                                        $<span class="totalTxt">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div style="text-align:center;margin-top:40px;">
                                <span class="step"></span>
                                <span class="step"></span>
                            </div>

                        </form>
                        <div style="overflow:auto;">
                            <div style="float:right;">
                                <button type="button" class="btn btn-success" id="rsvnPrevBtn" onclick="rsvnNextPrev(-1)">Previous</button>
                                <button type="button" class="btn btn-success" id="rsvnNextBtn" onclick="rsvnNextPrev(1)" readySubmit="false">Next</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <p>Booking is reserved for customers.</p>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal sign-in-to-book-modal" tabindex="-1" role="dialog" aria-labelledby="signInToBookModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Sign in required</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>You have to <a href="sign-in.php">sign in</a> in order to reserve a room.</h4>
                </div>
            </div>
        </div>
    </div>

    <?php if(($isSessionExists == 1 && $isAdmin[1] == "false") && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal" id="myProfileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($isSessionExists) { ?>
                            <form class="form" role="form" autocomplete="off" id="update-profile-form" method="post">
                                <input type="number" id="customerId" hidden
                                       name="customerId" value="<?php echo $cHandler->getId(); ?>" >
                                <div class="form-group">
                                    <label for="updateFullName">Full Name</label>
                                    <input type="text" class="form-control" id="updateFullName"
                                           name="updateFullName" value="<?php echo $cHandler->getFullName(); ?>" >
                                </div>
                                <div class="form-group">
                                    <label for="updatePhoneNumber">Phone Number</label>
                                    <input type="text" class="form-control" id="updatePhoneNumber"
                                           name="updatePhoneNumber" value="<?php echo $cHandler->getPhone(); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateEmail">Email</label>
                                    <input type="email" class="form-control" id="updateEmail"
                                           name="updateEmail" value="<?php echo $cHandler->getEmail(); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="updatePassword">New Password</label>
                                    <input type="password" class="form-control" id="updatePassword"
                                           name="updatePassword"
                                           title="At least 4 characters with letters and numbers">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-md float-right"
                                           name="updateProfileSubmitBtn" value="Update">
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<footer class="container" style="text-align: center;">
    <p>&copy; Company 2020-2023</p>
</footer>
<script src="js/utilityFunctions.js"></script>
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script src="js/animatejscx.js"></script>
<script src="js/form-submission.js"></script>
<script>
    $(document).ready(function () {
      let reservationDiv = $("#my-reservations-div");
      reservationDiv.hide();
      $(".my-reservations").click(function () {
        reservationDiv.slideToggle("slow");
      });
      $('#myReservationsTbl').DataTable();

      // dynamically entered room type value on show modal
      $('.book-now-modal-lg').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let roomType = button.data('rtype');
        let modal = $(this);
        modal.find('.modal-body select[name=roomType]').val(roomType);
      });

      // check-in policies popover
      $('[data-toggle="popover"]').popover();

    });
</script>
<script src="js/multiStepsRsvn.js"></script>
</body>
</html>