<?php
/**
 * Created by PhpStorm.
 * User: yangy
 * Date: 2019/2/25
 * Time: 19:50
 */

include_once("RouteCalculator.php");
echo "test start\n";

$start = "2 Drum Rd, Chester le Street, DH2 1AB";
//$addresses = array("Seaton Burn Services, Fisher Lane, Newcastle upon Tyne NE13 6BP",
//    "Alnwick Town Centre, 19 Lagny Street, Alnwick, NE66 1LA",
//    "Newton Aycliffe, 4 Northfield Way, Newton Aycliffe, DL5 6EJ",
//    "Thirsk Town Centre, 26 Market Place, Thirsk YO7 1LB",
//    "Whitby Town Centre, Station Square, Whitby YO21 1DX"
//);
$addresses = array("Seaton Burn Services, Fisher Lane, Newcastle upon Tyne NE13 6BP",
    "Alnwick Town Centre, 19 Lagny Street, Alnwick, NE66 1LA",
    "Newton Aycliffe, 4 Northfield Way, Newton Aycliffe, DL5 6EJ",
    "Thirsk Town Centre, 26 Market Place, Thirsk YO7 1LB",
    "Whitby Town Centre, Station Square, Whitby YO21 1DX"
);
$r = new RouteCalculator($start, $addresses);
$r->calculateRoute();

