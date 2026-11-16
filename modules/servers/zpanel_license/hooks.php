<?php
/**
 * zPanel License Module Hooks for WHMCS
 * Reference & Standards: https://developers.whmcs.com/hooks/
 */

use WHMCS\View\Menu\Item as MenuItem;

add_hook('ClientAreaPrimarySidebar', 10, function (MenuItem $primarySidebar) {
    $service = Menu::context('service');
    if (!$service || $service->product->servertype !== 'zpanel_license') {
        return;
    }

    if ($service->domainstatus !== 'Active') {
        return;
    }

    $sidebar = $primarySidebar->addChild('zPanelLicenseActions', [
        'label' => 'Lisans İşlemleri',
        'order' => 15,
        'icon'  => 'fas fa-key',
    ]);

    $sidebar->addChild('zpanel_docs', [
        'label' => 'Kurulum Kılavuzu & Dokümanlar',
        'uri'   => 'https://console.zepanel.com/partner/docs',
        'order' => 10,
        'icon'  => 'fas fa-book',
        'attributes' => ['target' => '_blank'],
    ]);

    $sidebar->addChild('zpanel_downloads', [
        'label' => 'zPanel İndirme Merkezi',
        'uri'   => 'https://www.zepanel.com/downloads',
        'order' => 20,
        'icon'  => 'fas fa-download',
        'attributes' => ['target' => '_blank'],
    ]);
});
