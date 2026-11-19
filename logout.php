<?php
require 'config/koneksi.php';
require 'includes/functions.php';
unset($_SESSION['penjual']);
session_regenerate_id(true);
redirect('/index.php');
