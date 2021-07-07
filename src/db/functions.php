<?php
/**
 * The $airports variable contains array of arrays of airports (see airports.php)
 * What can be put instead of placeholder so that function returns the unique first letter of each airport name
 * in alphabetical order
 *
 * Create a PhpUnit test (GetUniqueFirstLettersTest) which will check this behavior
 *
 * @param  array  $airports
 * @return string[]
 */

$sql = [
    'where' => [],
    'order' => '',
    'prepare' => [],
    'limit' => '',
];

function getUniqueFirstLetters($pdo) : array
{
    $sql = 'SELECT DISTINCT(LEFT(`name`, 1)) as letter FROM `airports` ORDER BY `letter`';
    $result = [];
    foreach ($pdo->query($sql) as $value) {
        $result[] = $value['letter'];
    }
    asort($result);
    return $result;
}

function snakeCaseToCamelCase(string $input)
{
    return preg_replace_callback('/_(.?)/', function ($matches){
        return ucfirst($matches[1]);
    }, $input);
}

function getFilterByState($airports, $state) {
    $sql['where'][] = "`states.name` = :state";
    $sql['prepare'] = array_merge($sql['prepare'], ['state' => $state]);
    return $sql;
}

function getSort($airports, $value) {
    $sql['order'] = $value;
    return $sql;
}

function getFilterByFirstLetter($airports, $letter) {
    $sql['where'][] = "`airports.name` LIKE :letter";
    $sql['prepare'] = array_merge($sql['prepare'], ['letter' => $letter]);
    return $sql;
}
    
function getPage($sql, $page) {
    $start = $page - 1;
    $sql['limit'] = $start . ', 5';
    return $sql;
}

function getAirports($pdo, $sql) {
    $query = "SELECT COUNT(`airports`, `name`) as `count`
        FROM `airports`
        LEFT JOIN `cities`
        ON `airports` .`city_id` = `cities` . `id`
        LEFT JOIN `states`
        ON `airports` . `state_id` = `states` . `id`";
    $query .= ($sql['where']) ? 'WHERE' . implode(' AND ', $sql['where']) : '';
    $sth = $pdo->prepare($query);
    $sth->setFetchMode(\PDO::FETCH_ASSOC);
    foreach ($sql['prepare'] as $key => $value) {
        $sth->binParam(":$key", $sql['prepare'][$key]);
    }
    $sth->execute();
    $result = $sth->fetch();
    $count = $result['count'];
    echo $count;
    echo '<br>';
    echo $query;
    die();
}