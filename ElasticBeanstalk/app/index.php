<?php
session_start();


echo implode('<br>', [
    'SESSION ID:' . session_id(),
    'SERVER_NAME:'.$_SERVER['SERVER_NAME'],
    'REMOTE_ADDR:'.$_SERVER['REMOTE_ADDR']
]);
?>