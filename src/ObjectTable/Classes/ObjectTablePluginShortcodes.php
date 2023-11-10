<?php

namespace CommonGateway\ObjectTable\Classes;

use CommonGateway\ObjectTable\Foundation\Plugin;

class ObjectTablePluginShortcodes
{
    /** @var Plugin */
    protected $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
        $this->add_shortcode();
        // For logged-in users:
        add_action('wp_ajax_object_handle_sort', [$this, 'objecttable_handle_sort']);
        // For non-logged-in users:
        add_action('wp_ajax_nopriv_object_handle_sort', [$this, 'objecttable_handle_sort']);

        // Use js and css
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'objecttable-styles',
            plugin_dir_url(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/css/table-styles.css',
            array(),
            time()
        );
        wp_enqueue_style('dashicons');
    
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'myplugin-script',
            plugin_dir_url(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/js/object-table.js',
            array('jquery'),
            time(),
            true
        );
    }

    private function add_shortcode(): void
    {
        add_shortcode('object-table', [$this, 'objecttable_result_shortcode']);
    }

    public function objecttable_handle_sort() {
        if ((isset($_POST['sort_column']) === false && isset($_POST['sort_order']) === false) && isset($_POST['search_term']) === false 
        && isset($_POST['page']) === false || isset($_POST['config_id']) === false) {
            wp_send_json_error(['message' => "No sort column, order, search term or page given"]);
        }

        $search     = $_POST['search_term'];
        $column     = $_POST['sort_column'];
        $order      = $_POST['sort_order'];
        $configId   = $_POST['config_id'];
        $page       = $_POST['page'];

        $config = $this->getConfig($configId);
        $mapping = isset($config['mapping']) ? json_decode($config['mapping'], true) : null;

        // Map the column back;
        foreach ($mapping as $key => $value) {
            if ($key === $column) {
                $column = $value;
            }
        }

        $sort = "_order[$column]=$order";
        $search = "_search=$search";

        $decodedBody = $this->fetchData($config['url'], $config['key'], $sort, $search, $page);
        // Return error.
        if (is_string($decodedBody) === true) {
            wp_send_json_error(['message' => $decodedBody]);
        }
        $tableCSSClass = isset($config['cssclass']) ? $config['cssclass'] : null;

        $html = $this->generateHTMLTable($configId, $decodedBody, $mapping, $tableCSSClass, true);

        // For when no results are found with given options.
        if ($html === ' ') {
            wp_send_json_success(['html' => ' ']);
        }

        $newRows = explode('<tbody>', $html);
        if (isset($newRows[1]) === false) {
            wp_send_json_error(['message' => 'Cant explode tbody from table html']);
        }
        $newRows = $newRows[1];
        $newRows = explode('</tbody>', $newRows);
        if (isset($newRows[0]) === false) {
            wp_send_json_error(['message' => 'Cant explode tbody from table html']);
        }
        $newRows = $newRows[0];

        // Send JSON response back to AJAX call
        wp_send_json_success(['html' => $newRows]);
    }

    /**
     * Callback for shortcode [object-table].
     *
     * @return string
     */
    public function objecttable_result_shortcode($atts): string
    {
        $configId = $atts['configid'] ?? '';

        if (empty($configId) === true) {
            return '<p>No configId given to shortcode.</p>';
        }
        
        $config = $this->getConfig($configId);  
        if ($config === null) {
            return '<p>Could not find a configuration with given id.</p>';
        }      

        if (isset($config['url']) === false || isset($config['key']) === false) {
            return '<p>Configuration has no url or api key.</p>';
        }

        $decodedBody = $this->fetchData($config['url'],  $config['key']);
        // Return error.
        if (is_string($decodedBody) === true) {
            return $decodedBody;
        }

        $mapping = isset($config['mapping']) ? json_decode($config['mapping'], true) : null;
        $tableCSSClass = isset($config['cssclass']) ? $config['cssclass'] : null;

        return $this->generateHTMLTable($configId, $decodedBody, $mapping, $tableCSSClass);
    }

    private function getConfig(string $configId)
    {
        $configs = get_option('objecttable_configs', []);
        foreach ($configs as $config) {
            if ($config['id'] == $configId) { 
                return $config;
            }
        }

        return null;
    }

    private function fetchData(string $url, string $apiKey, ?string $order = null, ?string $search = null, ?int $page = 1)
    {
        // Add query params to url.
        $url .= '?_limit=20';
        if ($order) {
            $url .= "&$order";
        }
        if ($search) {
            $url .= "&$search";
        }
        $url .= "&page={$page}";

        $data = wp_remote_get($url, [
            'headers'     => ['Content-Type' => 'application/json;', 'Authorization' => $apiKey]
        ]);

        if (is_wp_error($data)) {
            error_log($data->get_error_message());
            return '<p>Error retrieving data.</p>';
        }

        $responseBody = wp_remote_retrieve_body($data);

        if (is_wp_error($responseBody)) {
            error_log($responseBody->get_error_message());
            return '<p>Error retrieving data.</p>';
        }

        $decodedBody = json_decode($responseBody, true);

        if (isset($decodedBody) === false || array_key_exists('results', $decodedBody) === false) {
            return '<p>Error decoding data.</p>';
        }

        return $decodedBody;
    }

    /**
     * Create a html pagination for the table.
     *
     * @param array $responseBody
     *
     * @return string
     */
    private function generatePaginationHTML(string $configId): string
    {
        $paginationHTML = "<div class=\"table-pagination\">";
        $paginationHTML .= "<a id=\"tablePaginationPrevious{$configId}\" class=\"table-pagination-previous\" href=\"#\">Vorige</a>";
        $paginationHTML .= "<a id=\"tablePaginationCurrent{$configId}\" class=\"table-pagination-current\" href=\"#\">1</a>";
        $paginationHTML .= "<a id=\"tablePaginationNext{$configId}\" class=\"table-pagination-next\" href=\"#\">Volgende</a>";
        $paginationHTML .= "</div>";

        return $paginationHTML;
    }

    /**
     * Create a html table for the fetched objects.
     *
     * @param array       $responseBody
     * @param array|null  $mapping
     * @param string|null $tableCSSClass
     * @param string      $configId
     *
     * @return string
     */
    private function generateHTMLTable(string $configId, ?array $responseBody = [], ?array $mapping = null, ?string $tableCSSClass = null, ?bool $refetchData = false): string
    {
        // For when refetching data and no results are found with given options.
        if ($refetchData === true && isset($responseBody['results'][0]) === false) {
            return ' ';
        }

        if (array_key_exists('results', $responseBody) === false || isset($responseBody['results'][0]) === false) {
            return '<div>' . esc_html__('Er ging iets fout met het ophalen van data.', 'objecttableaddon') . '</div>';
        }


        $filteredHeaders = [];
        $tableHeaderRow  = $this->createTableHeader($filteredHeaders, $configId, $responseBody['results'], $mapping);
        $tableBodyRows   = $this->createTableRows($filteredHeaders, $responseBody['results']);

        if ($tableCSSClass === null) {
            $tableCSSClass = "table-container";
        }

        $paginationHTML = $this->generatePaginationHTML($configId);

        $tableHTML = "<div class=\"$tableCSSClass\">";
        $tableHTML .= "<input type=\"text\" id=\"searchInput{$configId}\" class=\"search-input\" placeholder=\"Zoeken...\" \>";
        $tableHTML .= "<table class=\"object-table\" id=\"objectTable$configId\">";
        $tableHTML .= "<thead>$tableHeaderRow</thead>";
        $tableHTML .= "<tbody>$tableBodyRows</tbody>";
        $tableHTML .= "</table>{$paginationHTML}</div>";

        return $tableHTML;
    }

    private function createTableHeader(array &$filteredHeaders, string $configId, ?array &$objects = [], ?array $mapping = null): string
    {
        // Initial filtering of headers
        $headers = array_keys($objects[0]);
        $filteredHeaders = array_filter($headers, function($key) {
            return strpos($key, '_') !== 0 && $key !== 'id';
        });

        if ($mapping) {
            // If a mapping is provided, modify objects to only include mapped keys
            $objects = array_map(function($object) use ($mapping) {
                $newObject = [];
                foreach ($mapping as $newKey => $map) {
                    $keys = explode('.', $map);
                    $value = $object;
                    foreach ($keys as $key) {
                        if (!isset($value[$key])) {
                            $value = '';  // Set value to an empty string if key is not found
                            break;  // Break out of the inner loop
                        }
                        $value = $value[$key];
                    }
                    $newObject[$newKey] = $value;
                }
                return $newObject;
            }, $objects);
            // Update filteredHeaders to reflect the keys of the mapped objects
            $filteredHeaders = array_keys($objects[0]);
        }

        $tableHeaderRow = '<tr>';
        $thId = "table{$configId}Header_";
        foreach ($filteredHeaders as $header) {
            $tableHeaderRow .= "<th id=\"{$thId}{$header}\">{$header}<span class=\"dashicons dashicons-sort\"></span></th>";
        }
        $tableHeaderRow .= '</tr>';

        return $tableHeaderRow;
    }

    private function createTableRows(array $filteredHeaders, ?array $objects = []): string
    {
        $tableBodyRows = '';
        foreach ($objects as $object) {
            $tableBodyRow = '<tr>';
            foreach ($filteredHeaders as $header) {
                $value = isset($object[$header]) ? $object[$header] : '';  
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $tableBodyRow .= "<td>{$value}</td>";
            }
            $tableBodyRow .= '</tr>';
            $tableBodyRows .= $tableBodyRow;
        }
    
        return $tableBodyRows;
    }
    
}
