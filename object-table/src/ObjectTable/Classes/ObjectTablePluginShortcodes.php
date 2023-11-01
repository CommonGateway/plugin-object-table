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
        $endpoint = $atts['endpoint'] ?? '';

        $key      = get_option('objecttable_api_key', '');
        $domain   = get_option('objecttable_api_domain', '');

        if (empty($key) || empty($domain)|| empty($endpoint)) {
            return '';
        }

        $url = $domain.$endpoint.'?_limit=1000';

        $data = wp_remote_get($url, [
            'headers'     => ['Content-Type' => 'application/json;', 'Authorization' => $key]
        ]);

        if (is_wp_error($data)) {
            return '';
        }

        $responseBody = wp_remote_retrieve_body($data);

        if (is_wp_error($responseBody)) {
            return '';
        }

        $decodedBody = json_decode($responseBody, true);

        var_dump($decodedBody);

        return $this->shortcodeResult($decodedBody);
    }

    /**
     * Create html table for the result of the shortcode.
     *
     * @param array $objects
     *
     * @return string
     */
    private function shortcodeResult(?array $objects = []): string
    {
        if (empty($objects) || isset($objects[0]) === false) {
            return '<div style="text-align: center">' . esc_html__('Er ging iets fout met het ophalen van data.', 'objecttableaddon') . '</div>';
        }

        $headers = array_keys($objects[0]);
        $filteredHeaders = array_filter($headers, function($key) {
            return strpos($key, '_') !== 0;
        });
    
        $tableHeaderRow = '<tr>';
        foreach ($filteredHeaders as $header) {
            $tableHeaderRow .= "<th>{$header}</th>";
        }
        $tableHeaderRow .= '</tr>';
    
        $tableBodyRows = '';
        foreach ($objects as $object) {
            $tableBodyRow = '<tr>';
            foreach ($filteredHeaders as $header) {
                $tableBodyRow .= "<td>{$object[$header]}</td>";
            }
            $tableBodyRow .= '</tr>';
            $tableBodyRows .= $tableBodyRow;
        }
    
        $tableHtml = '<table>' . $tableHeaderRow . $tableBodyRows . '</table>';
        
        return '<div style="text-align: center">' . $tableHtml . '</div>';
    }
}
