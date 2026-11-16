<?php
/**
 * zPanel License Automation Module for WHMCS
 * Architecture inspired by Plesk WHMCS Plugin (https://github.com/plesk/whmcs-plugin)
 * 
 * zPanel Partner API v1 Lisans Satış ve Doğrulama
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/ZPanel/Manager.php';
use WHMCS\Module\Server\ZPanelLicense\ZPanel\Manager;

function zpanel_license_MetaData() {
    return [
        'DisplayName' => 'zPanel Lisans Otomasyonu',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
    ];
}

function zpanel_license_ConfigOptions() {
    return [
        'license_tier' => [
            'FriendlyName' => 'Lisans Paketi / Tipi',
            'Type' => 'dropdown',
            'Options' => 'starter,pro,unlimited,partner',
            'Default' => 'unlimited',
        ],
    ];
}

function zpanel_license_CreateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->issueLicense();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Lisans üretilemedi');
}

function zpanel_license_SuspendAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->suspendLicense();
    return $res['success'] ? 'success' : ($res['message'] ?: 'Askıya alınamadı');
}

function zpanel_license_UnsuspendAccount(array $params) {
    return 'success';
}

function zpanel_license_TerminateAccount(array $params) {
    $mgr = new Manager($params);
    $res = $mgr->terminateLicense();
    return $res['success'] ? 'success' : ($res['message'] ?: 'İptal edilemedi');
}

function zpanel_license_ClientArea(array $params) {
    $mgr = new Manager($params);
    $tplVars = $mgr->getClientAreaParams();

    return [
        'tabOverviewReplacementTemplate' => 'clientarea.tpl',
        'templateVariables' => $tplVars,
    ];
}
