<?php
require_once '../bancol/config.php';

session_destroy();
header("Location: login_admin.php");
exit();