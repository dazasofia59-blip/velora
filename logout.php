<?php
include_once 'includes/session.php';

Session::logout();
header("Location: login.php");
exit();
