{* zPanel Resmi Lisans WHMCS Müşteri Alanı Şablonu *}
{* Dokümantasyon: https://console.zepanel.com/partner/docs *}

<link rel="stylesheet" href="modules/servers/zpanel_license/assets/css/client.css">

<div class="zp-license-wrap">
  
  <!-- Üst Başlık Kartı -->
  <div class="zp-header-card">
    <div>
      <div class="zp-header-title-row">
        <h2 class="zp-pkg-title">{$package_name|escape:'html'}</h2>
        <span class="zp-badge-ver">zPanel v1.0</span>
      </div>
      <div class="zp-meta-text">
        Hizmet Durumu: 
        {if $status eq 'Active'}
          <span class="zp-badge-active">Aktif</span>
        {elseif $status eq 'Suspended'}
          <span class="zp-badge-suspended">Askıda</span>
        {else}
          <span class="zp-badge-other">{$status|escape:'html'}</span>
        {/if}
      </div>
    </div>
    
    {if $is_issued}
    <div>
      <button type="button" class="zp-btn-primary" onclick="navigator.clipboard.writeText('{$license_key|escape:'javascript'}'); this.innerText = 'Kopyalandı!'; setTimeout(() => this.innerText = 'Lisans Anahtarını Kopyala', 2500);">
        Lisans Anahtarını Kopyala
      </button>
    </div>
    {/if}
  </div>

  <!-- Lisans Detay Kartı -->
  <div class="zp-card">
    <h3 class="zp-card-title">Resmi Lisans Bilgileri</h3>
    
    {if $is_issued}
      <div class="zp-license-key-box">
        <span>{$license_key|escape:'html'}</span>
        <span class="zp-partner-pill">Partner API Onaylı</span>
      </div>

      <!-- IP ve Alan Adı Yönetimi -->
      <div class="zp-ip-manage-box">
        <div class="zp-ip-manage-row">
          <div>
            <div class="zp-ip-label">Lisansa Bağlı Sunucu IP / Host</div>
            <div class="zp-ip-val">
              {if $license_ip}{$license_ip|escape:'html'}{else}<span class="zp-ip-unassigned">Tanımlanmamış (İlk kurulumda otomatik kilitlenir)</span>{/if}
            </div>
          </div>
          
          <form method="post" action="clientarea.php?action=productdetails&id={$service_id}" class="zp-form-row">
            <input type="hidden" name="token" value="{$csrfToken}">
            <input type="hidden" name="modop" value="custom">
            <input type="hidden" name="a" value="update_ip">
            <input type="text" name="new_ip" value="{$license_ip|escape:'html'}" placeholder="Yeni IP (Örn: 185.120.45.67)" required class="zp-input-text">
            <button type="submit" class="zp-btn-dark">
              IP Güncelle
            </button>
          </form>
        </div>
      </div>
    {else}
      <div class="zp-alert-warn">
        Lisans anahtarınız henüz oluşturulmadı veya provizyon aşamasındadır. Lütfen birkaç dakika sonra sayfayı yenileyiniz veya destek talebi açınız.
      </div>
    {/if}
  </div>

  {if $is_issued}
  <!-- Kurulum Kılavuzu Kartı -->
  <div class="zp-card">
    <h3 class="zp-card-title">Hızlı Sunucu Kurulum Komutu</h3>
    <p class="zp-desc-p">
      Temiz Ubuntu 22.04 / 24.04, Debian 12 veya AlmaLinux 9 sunucunuzun root SSH terminaline bağlanarak aşağıdaki komutu yapıştırın:
    </p>
    
    <div>
      <pre class="zp-code-pre">curl -fsSL https://get.zepanel.com/install.sh | bash -s -- --license={$license_key|escape:'html'}</pre>
    </div>

    <div class="zp-feature-list">
      <span>&check; <strong>Web Sunucusu:</strong> zMaxServer OpenLiteSpeed (LSAPI)</span>
      <span>&check; <strong>Yönetim Portu:</strong> 8026 (HTTPS)</span>
      <span>&check; <strong>Webmail Portu:</strong> 8046</span>
    </div>
  </div>
  {/if}

</div>
