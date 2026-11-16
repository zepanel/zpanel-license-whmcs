<?php
/**
 * zPanel Partner API Lisans Otomasyon Modülü (WHMCS)
 * Resmi Dokümantasyon: https://console.zepanel.com/partner/docs
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/ZPanel/Manager.php';
use WHMCS\Module\Server\ZPanelLicense\ZPanel\Manager;

function zpanel_license_MetaData() {
    return [
        'DisplayName' => 'zPanel Resmi Lisans Otomasyonu',
        'APIVersion' => '1.1',
        'RequiresServer' => false,
    ];
}

function zpanel_license_ConfigOptions() {
    return [
        'order_id' => [
            'FriendlyName' => 'Lisans Havuz Sipariş ID',
            'Type' => 'text',
            'Size' => '25',
            'Description' => 'Partner Konsolundaki aktif lisans havuz sipariş numarası (Örn: 1042)',
        ],
        'api_token' => [
            'FriendlyName' => 'Partner API Anahtarı',
            'Type' => 'password',
            'Size' => '50',
            'Description' => 'console.zepanel.com/partner/api adresinden üretilen Bearer Token (zp_partner_...)',
        ],
        'api_url' => [
            'FriendlyName' => 'Partner API Base URL',
            'Type' => 'text',
            'Size' => '60',
            'Default' => 'https://console.zepanel.com/partner-api/v1',
            'Description' => 'Varsayılan: https://console.zepanel.com/partner-api/v1',
        ],
    ];
}

function zpanel_license_TestConnection(array $params) {
    try {
        $mgr = new Manager($params);
        $res = $mgr->testConnection();
        if ($res['success']) {
            return ['success' => true];
        }
        return ['error' => $res['message'] ?: 'Bağlantı kurulamadı. API Anahtarınızı kontrol edin.'];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

function zpanel_license_CreateAccount(array $params) {
    try {
        $mgr = new Manager($params);
        $customerName = trim(($params['clientsdetails']['firstname'] ?? '') . ' ' . ($params['clientsdetails']['lastname'] ?? ''));
        if (!empty($params['clientsdetails']['companyname'])) {
            $customerName .= ' (' . $params['clientsdetails']['companyname'] . ')';
        }
        $domainOrIp = trim($params['customfields']['server_ip'] ?? $params['domain'] ?? '');

        $res = $mgr->createLicense($customerName, $domainOrIp);
        if ($res['success']) {
            return 'success';
        }
        return $res['message'] ?: 'Lisans havuzundan lisans tahsis edilemedi.';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_license_SuspendAccount(array $params) {
    // Partner API mimarisinde lisans askıya alma
    return 'success';
}

function zpanel_license_UnsuspendAccount(array $params) {
    return 'success';
}

function zpanel_license_TerminateAccount(array $params) {
    try {
        $mgr = new Manager($params);
        $licenseRowId = trim($params['customfields']['license_row_id'] ?? '');
        if (empty($licenseRowId)) {
            // Eğer row id yoksa işlem başarılı sayılarak WHMCS tarafı sonlandırılır
            return 'success';
        }

        $res = $mgr->releaseLicense($licenseRowId);
        if ($res['success']) {
            return 'success';
        }
        return $res['message'] ?: 'Lisans serbest bırakılamadı.';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_license_ClientAreaCustomButtonArray() {
    return [
        'IP Adresini Güncelle' => 'update_ip',
    ];
}

function zpanel_license_AdminCustomButtonArray() {
    return [
        'IP Adresini Güncelle' => 'update_ip',
    ];
}

function zpanel_license_update_ip(array $params) {
    try {
        $newIp = trim($_POST['new_ip'] ?? '');
        if (empty($newIp)) {
            return 'Lütfen geçerli bir IP veya sunucu alan adı giriniz.';
        }

        $mgr = new Manager($params);
        $licenseRowId = trim($params['customfields']['license_row_id'] ?? '');
        $customerName = trim(($params['clientsdetails']['firstname'] ?? '') . ' ' . ($params['clientsdetails']['lastname'] ?? ''));

        $res = $mgr->updateLicenseInfo($licenseRowId, $customerName, $newIp);
        if ($res['success']) {
            return 'success';
        }
        return $res['message'] ?: 'IP adresi güncellenemedi.';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function zpanel_license_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    return [
        'tabOverviewReplacementTemplate' => 'clientarea.tpl',
        'templateVariables' => $tplVars,
    ];
}
