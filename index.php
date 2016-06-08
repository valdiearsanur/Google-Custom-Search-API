<?php
include 'goog/GoogleCustomSearch.php';

define("SEARCH_ENGINE_ID", "[paste search engine ID here]");
define("API_KEY", "[paste api key here]");

$search = new goog\GoogleCustomSearch(SEARCH_ENGINE_ID, API_KEY);
$results = $search->search('komputer');

echo json_encode($results);

?>