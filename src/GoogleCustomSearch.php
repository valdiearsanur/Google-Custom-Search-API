<?php
namespace goog;

/**
 * Google Custom Search
 *
 * A PHP interface to the Google Custom Search JSON API
 *
 * Documentation for the Google Custom Search JSON API
 * https://developers.google.com/custom-search/json-api/v1/overview
 *
 * Usage:
 * define("SEARCH_ENGINE_ID", "[paste search engine ID here]");
 * define("API_KEY", "[paste api key here]");
 * $search = new goog\GoogleCustomSearch(SEARCH_ENGINE_ID, API_KEY);
 * $results = $search->search('komputer');
 *
**/
class GoogleCustomSearch
{
    /**
     * Google Search Engine ID
     *
     * @var string
     **/
    protected $search_engine_id;

    /**
     * Google API Key
     *
     * @var string
     **/
    protected $api_key;

    /**
     * Constructor
     *
     * @param string search_engine_id Search Engine ID
     * @param string api_key API Key
     * @return void
     **/
    public function __construct($search_engine_id, $api_key)
    {
        $this->search_engine_id = $search_engine_id;
        $this->api_key = $api_key;
    }

    /**
     * Sends search request to Google
     *
     * @param array params The parameters of the search request
     * @return object The raw results of the search request
     **/
    private function request($params) 
    {
        $params = array_merge(
            $params,
            [
                'key' => $this->api_key,
                'cx' => $this->search_engine_id
            ]
        );
        
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true
            ]
        ]);

        return json_decode(
            file_get_contents('https://www.googleapis.com/customsearch/v1?' . http_build_query($params), false, $context)
        );
    }

    /**
     * Perform search
     *
     * Returns an object with the following properties:
     *
     *   page
     *   perPage
     *   start
     *   end
     *   totalResults
     *   results
     *     title
     *     snippet
     *     htmlSnippet
     *     link
     *     image
     *     thumbnail
     *
     * @param string terms The search terms
     * @param integer page The page to return
     * @param integer per_page How many results to dispaly per page
     * @param array extra Extra parameters to pass to Google
     * @return object The results of the search
     * @throws Exception If error is returned from Google
     **/
    public function search($terms, $page=1, $per_page=10, $extra=[])
    {
        // Google only allows 10 results at a time
        $per_page = ($per_page > 10) ? 10 : $per_page;
        
        $params = [
            'q' => $terms,
            'start' => (($page - 1) * $per_page) + 1,
            'num' => $per_page
        ];
        if (sizeof($extra)) {
            $params = array_merge($params, $extra);
        }

        $response = $this->request($params);

        if (isset($response->error)) {
            throw new \Exception($response->error->message);
        }
        
        $request_info = $response->queries->request[0];
        
        $results = new \stdClass();
        $results->page = $page;
        $results->perPage = $per_page;
        $results->start = $request_info->startIndex;
        $results->end = ($request_info->startIndex + $request_info->count) - 1;
        $results->totalResults = $request_info->totalResults;
        $results->results = [];

        if (isset($response->items)) {
            foreach ($response->items as $result) {
                $results->results[] = (object) [
                    'title' => $result->title,
                    'snippet' => $result->snippet,
                    'htmlSnippet' => $result->htmlSnippet,
                    'link' => $result->link,
                    'image' => isset($result->pagemap->cse_image) ? $result->pagemap->cse_image[0]->src : '',
                    'thumbnail' => isset($result->pagemap->cse_thumbnail) ? $result->pagemap->cse_thumbnail[0]->src : '',
                ];
            }   
        }
        
        return $results;
    }
}
