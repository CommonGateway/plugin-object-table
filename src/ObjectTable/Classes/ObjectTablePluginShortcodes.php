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

        // Use css
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    public function enqueue_styles()
    {
        wp_enqueue_style(
            'objecttable-styles',
            plugin_dir_url(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/css/table-styles.css',
            array(),
            filemtime(plugin_dir_path(dirname(__FILE__, 3)) . 'src/ObjectTable/Assets/css/table-styles.css') 
        );
    }

    private function add_shortcode(): void
    {
        add_shortcode('object-table', [$this, 'objecttable_result_shortcode']);
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

        $correctConfig = null;
        $configs = get_option('objecttable_configs', []);
        foreach ($configs as $config) {
            if ($config['id'] == $configId) { 
                $correctConfig = $config;
                break;
            }
        }

        if (isset($correctConfig) === false) {
            return '<p>Could not find a configuration with given id.</p>';
        }

        $config = $correctConfig;        

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

        return $this->shortcodeResult($decodedBody['results'], $mapping, $tableCSSClass);
    }

    private function fetchData(string $url, string $apiKey)
    {
        $url .= '?_limit=1000';

        $data = wp_remote_get($url, [
            'headers'     => ['Content-Type' => 'application/json;', 'Authorization' => $apiKey]
        ]);

        if (is_wp_error($data)) {
            return '<p>Error retrieving data.</p>';
        }

        $responseBody = wp_remote_retrieve_body($data);

        if (is_wp_error($responseBody)) {
            return '<p>Error retrieving data.</p>';
        }

        $decodedBody = json_decode($responseBody, true);

        if (array_key_exists('results', $decodedBody) === false) {
            return '<p>Error decoding data.</p>';
        }

        return $decodedBody;
    }

    /**
     * Create html table for the result of the shortcode.
     *
     * @param array $objects
     *
     * @return string
     */
    private function shortcodeResult(?array $objects = [], ?array $mapping = null, ?string $tableCSSClass = null): string
    {
        if (empty($objects) || isset($objects[0]) === false) {
            return '<div>' . esc_html__('Er ging iets fout met het ophalen van data.', 'objecttableaddon') . '</div>';
        }

        $filteredHeaders = [];
        $tableHeaderRow  = $this->createTableHeader($objects, $mapping, $filteredHeaders);
        $tableBodyRows   = $this->createTableRows($objects, $filteredHeaders);

        if ($tableCSSClass === null) {
            $tableCSSClass = "table-container";
        }

        $tableHtml = "<div class=\"$tableCSSClass\"><table><thead>$tableHeaderRow </thead><tbody> $tableBodyRows</tbody></table></div>";

        return '<div>' . $tableHtml . '</div>';
    }

    private function createTableHeader(?array &$objects = [], ?array $mapping = null, array &$filteredHeaders): string
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
        foreach ($filteredHeaders as $header) {
            $tableHeaderRow .= "<th>{$header}</th>";
        }
        $tableHeaderRow .= '</tr>';

        return $tableHeaderRow;
    }

    private function createTableRows(?array $objects = [], array $filteredHeaders): string
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
