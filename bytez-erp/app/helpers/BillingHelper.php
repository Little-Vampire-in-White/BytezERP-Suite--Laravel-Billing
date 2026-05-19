<?php

class BillingHelper {
    private static $apiBase = "http://localhost:3000/api";

    /**
     * Fetch invoices from the Node.js Bridge API
     */
    public static function getExternalInvoices() {
        $url = self::$apiBase . "/invoices";
        $response = file_get_contents($url);
        
        if ($response === FALSE) {
            return [];
        }

        $data = json_decode($response, true);
        return $data['status'] === 'success' ? $data['data'] : [];
    }
}