<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Sync Laravel clients into Bytez-ERP clients table.
     *
     * Upsert logic:
     * - Match by email (case-insensitive)
     * - If exists -> PUT update
     * - If not -> POST create
     */
    public function syncClientsToBytezERP(Request $request)
    {
        // Bytez-ERP API base
        $bytezBase = rtrim(env('BYTEZ_ERP_API_BASE', 'http://127.0.0.1:8080/Codebytez/api'), '/');

        // Bytez-ERP auth (fixed temp admin credentials)
        $adminEmail = env('BYTEZ_ERP_SYNC_ADMIN_EMAIL', 'sync-admin@bytez.com');
        $adminPassword = env('BYTEZ_ERP_SYNC_ADMIN_PASSWORD', 'sync-admin-password');


        // Get token from Bytez-ERP API
        // Bytez-ERP login endpoint is served as: POST /Codebytez/api/?url=auth
        // Bytez-ERP login endpoint (see bytez-erp/api/index.php):
        // POST /Codebytez/api/?url=auth
        $loginUrl = $bytezBase . '/?url=auth';



        // DEBUG: if auth fails, we want to see the exact URL being called.
        // (Doesn't affect sync result)



        $loginPayload = [
            'email' => $adminEmail,
            'password' => $adminPassword,
        ];

        $ch = curl_init($loginUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($loginPayload),
            CURLOPT_TIMEOUT => 15,
        ]);

        $loginResp = curl_exec($ch);
        $loginErr = curl_error($ch);
        $loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($loginResp === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to call Bytez-ERP login API',
                'error' => $loginErr,
            ], 500);
        }

        $loginData = json_decode($loginResp, true);
        if ($loginCode < 200 || $loginCode >= 300 || empty($loginData['data']['token'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bytez-ERP login failed',
                'http_code' => $loginCode,
                'response' => $loginData,
            ], 401);
        }

        $token = $loginData['data']['token'];

        // Fetch all clients from Laravel
        $clients = Client::all();

        $results = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($clients as $lc) {
            // Bytez-ERP create/update payload
            $payload = [
                'company' => $lc->company ?? ($lc->name ?? ''),
                'industry' => $lc->industry ?? null,
                'contact_name' => $lc->contact_name ?? null,
                'phone' => $lc->phone ?? null,
                'email' => $lc->email ?? null,
                'address' => $lc->address ?? null,
                'status' => $lc->status ?? 'active',
            ];

            if (empty($payload['company']) || empty($payload['email'])) {
                $results['skipped']++;
                continue;
            }

            // Bytez-ERP API doesn't expose search-by-email; fetch all and match.
            $listUrl = $bytezBase . '/clients';
            $listCh = curl_init($listUrl);
            curl_setopt_array($listCh, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
                CURLOPT_TIMEOUT => 15,
            ]);
            $listResp = curl_exec($listCh);
            $listCode = curl_getinfo($listCh, CURLINFO_HTTP_CODE);
            $listErr = curl_error($listCh);
            curl_close($listCh);

            if ($listResp === false || $listCode < 200 || $listCode >= 300) {
                $results['errors'][] = [
                    'client' => $lc->id,
                    'stage' => 'list',
                    'http_code' => $listCode,
                    'error' => $listErr,
                ];
                continue;
            }

            $listData = json_decode($listResp, true);

            $found = null;
            foreach (($listData['data'] ?? []) as $bc) {
                if (!empty($bc['email']) && strtolower($bc['email']) === strtolower($payload['email'])) {
                    $found = $bc;
                    break;
                }
            }

            if ($found && !empty($found['id'])) {
                // Update existing
                $putUrl = $bytezBase . '/clients/' . $found['id'];
                $putCh = curl_init($putUrl);
                curl_setopt_array($putCh, [
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token,
                        'Content-Type: application/json',
                    ],
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_TIMEOUT => 15,
                ]);

                $putResp = curl_exec($putCh);
                $putCode = curl_getinfo($putCh, CURLINFO_HTTP_CODE);
                $putErr = curl_error($putCh);
                curl_close($putCh);

                if ($putResp !== false && $putCode >= 200 && $putCode < 300) {
                    $results['updated']++;
                } else {
                    $results['errors'][] = [
                        'client' => $lc->id,
                        'stage' => 'update',
                        'http_code' => $putCode,
                        'error' => $putErr,
                    ];
                }
            } else {
                // Create new
                $postUrl = $bytezBase . '/clients';
                $postCh = curl_init($postUrl);
                curl_setopt_array($postCh, [
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $token,
                        'Content-Type: application/json',
                    ],
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_TIMEOUT => 15,
                ]);

                $postResp = curl_exec($postCh);
                $postCode = curl_getinfo($postCh, CURLINFO_HTTP_CODE);
                $postErr = curl_error($postCh);
                curl_close($postCh);

                if ($postResp !== false && $postCode >= 200 && $postCode < 300) {
                    $results['created']++;
                } else {
                    $results['errors'][] = [
                        'client' => $lc->id,
                        'stage' => 'create',
                        'http_code' => $postCode,
                        'error' => $postErr,
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * Pull Bytez-ERP clients into Laravel.
     * Use this to update Laravel when Bytez-ERP has the "master" list.
     */
    public function syncClientsFromBytezERP(Request $request)
    {
        $bytezBase = rtrim(env('BYTEZ_ERP_API_BASE', 'http://127.0.0.1:8080/Codebytez/api'), '/');
        $adminEmail = env('BYTEZ_ERP_SYNC_ADMIN_EMAIL', 'sync-admin@bytez.com');
        $adminPassword = env('BYTEZ_ERP_SYNC_ADMIN_PASSWORD', 'sync-admin-password');

        // 1. Login to Bytez-ERP to get a token
        $loginUrl = $bytezBase . '/?url=auth';
        $ch = curl_init($loginUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['email' => $adminEmail, 'password' => $adminPassword]),
            CURLOPT_TIMEOUT => 15,
        ]);
        $loginResp = json_decode(curl_exec($ch), true);
        $loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($loginCode !== 200 || empty($loginResp['data']['token'])) {
            return response()->json(['status' => 'error', 'message' => 'Auth failed'], 401);
        }
        $token = $loginResp['data']['token'];

        // 2. Fetch all clients from Bytez-ERP
        $listUrl = $bytezBase . '/clients';
        $ch = curl_init($listUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
            CURLOPT_TIMEOUT => 15,
        ]);
        $listData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $bytezClients = $listData['data'] ?? [];
        $results = ['created' => 0, 'updated' => 0, 'errors' => []];

        foreach ($bytezClients as $bc) {
            if (empty($bc['email'])) continue;

            // Try to find existing client in Laravel by email
            $client = Client::where('email', $bc['email'])->first();

            // Map Bytez-ERP fields back to Laravel fields
            // Note: Bytez 'name' field is usually the Company Name in ERP
            $data = [
                'name' => $bc['contact_name'] ?? $bc['name'], 
                'company' => $bc['name'], 
                'email' => $bc['email'],
                'phone' => $bc['phone'] ?? null,
                'address' => $bc['address'] ?? null,
                'industry' => $bc['industry'] ?? null,
                'status' => $bc['status'] ?? 'active',
            ];

            try {
                $syncedClient = Client::updateOrCreate(['email' => $bc['email']], $data);
                $syncedClient->wasRecentlyCreated ? $results['created']++ : $results['updated']++;
            } catch (\Exception $e) {
                \Log::error("Sync Error for " . $bc['email'] . ": " . $e->getMessage());
                $results['errors'][] = ['email' => $bc['email'], 'error' => $e->getMessage()];
            }
        }

        return response()->json(['status' => 'success', 'data' => $results]);
    }
}
