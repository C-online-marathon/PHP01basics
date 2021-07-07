<?php

require_once './functions.php';
require_once './pdo_ini.php';

$airports = require '../web/airports.php';
$airports = array_chunk($airports, 5, true);

    if(isset($_GET['filter_by_first_letter'])) {
        echo 'Filter ' . $_GET['filter_by_first_letter'];
    }

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $ulr_filter_by_state = '';
    $url_sort = '';
    $url_filter_by_first_letter = '';
    $url_page = '';

    foreach($_GET as $key => $value) {
        $function_name = snakeCaseToCamelCase('get_' . $key);
        if (function_exists($function_name)) {
            $sql = $function_name($sql, $value);
            $url = 'url_' . $key;
            $$url = "&$key=$value";
        }
    }

    $airports = getAirports($pdo, $sql);


    if($page < 10) {
        $start_page = 1;
        $end_page = (ceil(count($airports)/5)>$page + 5) ? $page + 5 : ceil(count($airports)/5);
    } else {
        $start_page = $page - 5;
        $end_page = $end_page = (ceil(count($airports)/5)>$page + 5) ? $page + 5 : ceil(count($airports)/5);
    }

    //$airports = array_chunk($airports, 5, true);
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Airports</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<main role="main" class="container">

    <h1 class="mt-5">US Airports</h1>

    <div class="alert alert-dark">
        Filter by first letter:

        <?php foreach (getUniqueFirstLetters($pdo) as $letter): ?>
            <a href="index.php?filter_by_first_letter=<?= $letter . $ulr_filter_by_state . $url_sort ?>"><?= $letter ?></a>
        <?php endforeach; ?>

        <a href="./" class="float-right">Reset all filters</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col"><a href="./?sort=name&page=<?= $page . $ulr_filter_by_state . $url_filter_by_first_letter?>">Name</a></th>
            <th scope="col"><a href="./?sort=code&page=<?= $page . $ulr_filter_by_state . $url_filter_by_first_letter?>">Code</a></th>
            <th scope="col"><a href="./?sort=state&page=<?= $page . $ulr_filter_by_state . $url_filter_by_first_letter?>">State</a></th>
            <th scope="col"><a href="./?sort=city&page=<?= $page . $ulr_filter_by_state . $url_filter_by_first_letter?>">City</a></th>
            <th scope="col">Address</th>
            <th scope="col">Timezone</th>
        </tr>
        </thead>
        <tbody>
       
        <?php 
        if($airports) {
        foreach ($airports[$page-1] as $airport){ ?>
        <tr>
            <td><?= $airport['name'] ?></td>
            <td><?= $airport['code'] ?></td>
            <td><a href="./?filter_by_state=<?=$airport['state'] . $url_filter_by_first_letter?>"><?=$airport['state'] ?></a></td>
            <td><?= $airport['city'] ?></td>
            <td><?= $airport['address'] ?></td>
            <td><?= $airport['timezone'] ?></td>
        </tr>
        <?php 
        }
    } 
    ?>
        </tbody>
    </table>

    <nav aria-label="Navigation">
        <ul class="pagination justify-content-center">
            <?php
            for ($i = $start_page; $i <= $end_page; $i++) {
                if (($i) == $page) {
                    echo '<li class="page-item active"><a class="page-link" href="./?page=' . $i . '">' . ($i) . '</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="./?page=' . $i . $url_filter_by_first_letter . $ulr_filter_by_state . $url_sort . '">' . ($i) . '</a></li>';
                }
            }
            ?>
        </ul>
    </nav>

</main>
</html>
