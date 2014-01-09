<?php

session_start();

// wow, peazo de código eh. Soy hacker.
$_SESSION['key'] = $_GET['key'];
header("Location: /");
