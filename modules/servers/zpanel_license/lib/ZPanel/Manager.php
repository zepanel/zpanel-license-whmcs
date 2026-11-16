<?php

namespace WHMCS\Module\Server\ZPanelLicense\ZPanel;

/**
 * zPanel License Partner API Manager for WHMCS
 * Architecture inspired by Plesk WHMCS Plugin
 */
class Manager
{
    private string $host;
    private string $apiKey;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->host = trim($params['serverip'] ?: 'console.zepanel.com', '/');
        $this->apiKey = trim($params['serverpassword'] ?: $params['serveraccesshash']);
    }

    private function call(string $method, string $uri, array $data = []): array
    {
        $url = "https://{$this->host}:8056/api/v1/" . ltrim($uri, '/');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($res, true);
        return [
            'success' => ($code >= 200 && $code < 300),
            'data' => $json['data'] ?? $json,
            'message' => $json['message'] ?? '',
        ];
    }

    public function issueLicense(): array
    {
        $ip = $this->params['customfields']['server_ip'] ?? $this->params['domain'] ?? '';
        return $this->call('POST', 'partner/license/issue', [
            'ip' => $ip,
            'package_type' => $this->params['configoption1'] ?? 'unlimited',
        ]);
    }

    public function suspendLicense(): array
    {
        $licenseKey = $this->params['customfields']['license_key'] ?? '';
        return $this->call('POST', 'partner/license/suspend', ['license_key' => $licenseKey]);
    }

    public function terminateLicense(): array
    {
        $licenseKey = $this->params['customfields']['license_key'] ?? '';
        return $this->call('POST', 'partner/license/cancel', ['license_key' => $licenseKey]);
    }

    public function getClientAreaParams(): array
    {
        $licenseKey = $this->params['customfields']['license_key'] ?? ('ZPNL-' . strtoupper(substr(md5($this->params['serviceid']), 0, 12)));
        $licenseIp  = $this->params['customfields']['server_ip'] ?? $this->params['domain'] ?? '';

        return [
            'license_key'  => $licenseKey,
            'license_ip'   => $licenseIp,
            'status'       => $this->params['status'] ?? 'Active',
            'package_name' => $this->params['package'] ?? 'zPanel Sınırsız Sunucu Lisansı',
            'service_id'   => $this->params['serviceid'],
        ];
    }
}

