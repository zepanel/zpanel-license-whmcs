<?php

namespace WHMCS\Module\Server\ZPanelLicense\ZPanel;

use WHMCS\Database\Capsule;

/**
 * zPanel Partner API Entegrasyon Yöneticisi (v1 M2M REST API)
 * Resmi Dokümantasyon: https://console.zepanel.com/partner/docs
 */
class Manager
{
    private string $apiUrl;
    private string $apiToken;
    private string $orderId;
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;

        // API URL belirleme (Varsayılan: https://console.zepanel.com/partner-api/v1)
        $configuredUrl = trim($params['configoption3'] ?? '');
        if (empty($configuredUrl)) {
            $serverHost = trim($params['serverhostname'] ?? $params['serverip'] ?? '');
            if (!empty($serverHost) && $serverHost !== 'console.zepanel.com') {
                $configuredUrl = "https://{$serverHost}/partner-api/v1";
            } else {
                $configuredUrl = "https://console.zepanel.com/partner-api/v1";
            }
        }
        $this->apiUrl = rtrim($configuredUrl, '/');

        // API Anahtarı (zp_partner_...)
        $this->apiToken = trim($params['configoption2'] ?? $params['serverpassword'] ?? $params['serveraccesshash'] ?? '');

        // Lisans Havuz Sipariş ID (Örn: 1042)
        $this->orderId = trim($params['configoption1'] ?? '');
    }

    /**
     * Partner API'sine HTTP isteği gönderir
     */
    public function call(string $method, string $path, array $data = []): array
    {
        if (empty($this->apiToken)) {
            return [
                'success' => false,
                'message' => 'Partner API Anahtarı (API Token) tanımlanmamış.',
                'code' => 401,
            ];
        }

        $url = $this->apiUrl . '/' . ltrim($path, '/');
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: WHMCS-zPanel-License-Module/1.0',
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $method = strtoupper($method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'message' => 'Partner API bağlantı hatası: ' . $curlError,
                'code' => 0,
            ];
        }

        $json = json_decode($response, true);
        $isSuccess = ($httpCode >= 200 && $httpCode < 300);

        return [
            'success' => $isSuccess,
            'code' => $httpCode,
            'data' => $json,
            'message' => $json['error'] ?? ($json['message'] ?? ($isSuccess ? 'İşlem başarılı' : "HTTP Hata Kodu: {$httpCode}")),
        ];
    }

    /**
     * Bağlantı ve Kimlik Doğrulama Testi
     */
    public function testConnection(): array
    {
        return $this->call('GET', 'packages');
    }

    /**
     * Müşteriye havuzdan lisans üretir / atar
     */
    public function createLicense(string $customerName, string $customerDomain): array
    {
        if (empty($this->orderId)) {
            return [
                'success' => false,
                'message' => 'Lisans Havuz Sipariş No (Order ID) ürün ayarlarında belirtilmemiş.',
            ];
        }

        $res = $this->call('POST', "orders/{$this->orderId}/licenses", [
            'customer_name' => $customerName,
            'customer_domain' => $customerDomain ?: null,
        ]);

        if (!$res['success']) {
            return $res;
        }

        $data = $res['data'];
        $licenseKey = $data['license_key'] ?? '';
        $licenseRowId = (string)($data['id'] ?? '');
        $serviceId = (int)($this->params['serviceid'] ?? 0);

        if ($serviceId > 0 && !empty($licenseKey)) {
            try {
                // WHMCS tblhosting tablosuna lisans anahtarını kaydet
                Capsule::table('tblhosting')
                    ->where('id', $serviceId)
                    ->update([
                        'password' => encrypt($licenseKey),
                        'username' => 'zp_' . substr(md5($licenseKey), 0, 8),
                    ]);

                // Varsa custom fields alanlarını da güncelle
                $this->saveCustomFieldValue($serviceId, 'license_key', $licenseKey);
                $this->saveCustomFieldValue($serviceId, 'license_row_id', $licenseRowId);
                if (!empty($customerDomain)) {
                    $this->saveCustomFieldValue($serviceId, 'server_ip', $customerDomain);
                }
            } catch (\Exception $e) {
                // WHMCS DB log
                logModuleCall('zpanel_license', 'createLicense_db_save', $data, $e->getMessage());
            }
        }

        return [
            'success' => true,
            'license_key' => $licenseKey,
            'license_row_id' => $licenseRowId,
            'data' => $data,
        ];
    }

    /**
     * Lisans IP veya Domain bilgisini günceller
     */
    public function updateLicenseInfo(string $licenseRowId, string $customerName, string $newDomainOrIp): array
    {
        if (empty($this->orderId) || empty($licenseRowId)) {
            return [
                'success' => false,
                'message' => 'Sipariş veya Lisans ID eksik.',
            ];
        }

        $res = $this->call('POST', "orders/{$this->orderId}/licenses/{$licenseRowId}/info", [
            'customer_name' => $customerName,
            'customer_domain' => $newDomainOrIp,
        ]);

        if ($res['success']) {
            $serviceId = (int)($this->params['serviceid'] ?? 0);
            if ($serviceId > 0) {
                try {
                    Capsule::table('tblhosting')->where('id', $serviceId)->update(['domain' => $newDomainOrIp]);
                    $this->saveCustomFieldValue($serviceId, 'server_ip', $newDomainOrIp);
                } catch (\Exception $e) {
                    // Ignore DB update error
                }
            }
        }

        return $res;
    }

    /**
     * Lisansı serbest bırakır / havuza geri kazandırır
     */
    public function releaseLicense(string $licenseRowId): array
    {
        if (empty($this->orderId) || empty($licenseRowId)) {
            return [
                'success' => false,
                'message' => 'Sipariş veya Lisans ID eksik.',
            ];
        }

        return $this->call('POST', "orders/{$this->orderId}/licenses/{$licenseRowId}/release");
    }

    /**
     * Müşteri paneli değişkenlerini hazırlar
     */
    public function getClientAreaParams(): array
    {
        $serviceId = (int)($this->params['serviceid'] ?? 0);
        $licenseKey = '';
        $licenseRowId = '';
        $serverIp = $this->params['domain'] ?? '';

        // 1. Önce tblhosting password alanını oku
        if (!empty($this->params['password'])) {
            $licenseKey = $this->params['password'];
        }

        // 2. Custom fields'dan oku
        $customLicenseKey = $this->getCustomFieldValue($serviceId, 'license_key');
        if (!empty($customLicenseKey)) {
            $licenseKey = $customLicenseKey;
        }

        $licenseRowId = $this->getCustomFieldValue($serviceId, 'license_row_id');
        $customIp = $this->getCustomFieldValue($serviceId, 'server_ip');
        if (!empty($customIp)) {
            $serverIp = $customIp;
        }

        return [
            'license_key' => $licenseKey,
            'license_row_id' => $licenseRowId,
            'license_ip' => $serverIp,
            'status' => $this->params['status'] ?? 'Active',
            'package_name' => $this->params['package'] ?? 'zPanel Sunucu Lisansı',
            'service_id' => $serviceId,
            'order_id' => $this->orderId,
            'is_issued' => !empty($licenseKey),
        ];
    }

    /**
     * WHMCS Özel Alan (Custom Field) Değeri Yazar
     */
    private function saveCustomFieldValue(int $serviceId, string $fieldName, string $value): void
    {
        $field = Capsule::table('tblcustomfields')
            ->where('type', 'product')
            ->where('relid', $this->params['packageid'] ?? 0)
            ->where('fieldname', 'LIKE', $fieldName . '%')
            ->first();

        if ($field) {
            Capsule::table('tblcustomfieldsvalues')->updateOrInsert(
                ['fieldid' => $field->id, 'relid' => $serviceId],
                ['value' => $value]
            );
        }
    }

    /**
     * WHMCS Özel Alan (Custom Field) Değeri Okur
     */
    private function getCustomFieldValue(int $serviceId, string $fieldName): string
    {
        $field = Capsule::table('tblcustomfields')
            ->where('type', 'product')
            ->where('relid', $this->params['packageid'] ?? 0)
            ->where('fieldname', 'LIKE', $fieldName . '%')
            ->first();

        if ($field) {
            $val = Capsule::table('tblcustomfieldsvalues')
                ->where('fieldid', $field->id)
                ->where('relid', $serviceId)
                ->value('value');
            return (string)$val;
        }

        return '';
    }
}
