<?php
function conexion() {
    return new PDO('mysql:host=localhost;dbname=kallijag_inventory_stage', 'kallijag_stage', 'uNtiL.horSe@5');
}