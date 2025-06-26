<?php
function conexion() {
    return new PDO('mysql:host=localhost;dbname=KalliPos', 'root', 'root');
}