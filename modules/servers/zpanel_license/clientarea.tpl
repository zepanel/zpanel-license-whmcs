{* zPanel Resmi Lisans WHMCS Müşteri Alanı Şablonu *}
{* Dokümantasyon: https://console.zepanel.com/partner/docs *}

<div class="zp-license-wrap" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a; max-width: 960px; margin: 0 auto;">
  
  <!-- Üst Başlık Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <div>
      <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
        <h2 style="font-size: 20px; font-weight: 700; margin: 0; color: #0f172a;">{$package_name}</h2>
        <span style="background: #e0f2fe; color: #0369a1; font-size: 11.5px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; text-transform: uppercase;">zPanel v1.0</span>
      </div>
      <div style="font-size: 13.5px; color: #64748b;">
        Hizmet Durumu: 
        {if $status eq 'Active'}
          <span style="background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 9999px; font-weight: 600; font-size: 12px;">Aktif</span>
        {elseif $status eq 'Suspended'}
          <span style="background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 9999px; font-weight: 600; font-size: 12px;">Askıda</span>
        {else}
          <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 9999px; font-weight: 600; font-size: 12px;">{$status}</span>
        {/if}
      </div>
    </div>
    
    {if $is_issued}
    <div>
      <button type="button" onclick="navigator.clipboard.writeText('{$license_key}'); this.innerText = 'Kopyalandı!'; setTimeout(() => this.innerText = 'Lisans Anahtarını Kopyala', 2500);" style="background: #2563eb; color: #ffffff; font-weight: 600; font-size: 13px; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; transition: background 0.15s ease;">
        Lisans Anahtarını Kopyala
      </button>
    </div>
    {/if}
  </div>

  <!-- Lisans Detay Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 14px 0; color: #1e293b;">Resmi Lisans Bilgileri</h3>
    
    {if $is_issued}
      <div style="background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; padding: 16px 20px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 16px; font-weight: 700; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; color: #38bdf8;">
        <span>{$license_key}</span>
        <span style="font-size: 11.5px; color: #94a3b8; font-weight: normal; font-family: sans-serif; background: #1e293b; padding: 3px 8px; border-radius: 4px;">Partner API Onaylı</span>
      </div>

      <!-- IP ve Alan Adı Yönetimi -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
          <div>
            <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Lisansa Bağlı Sunucu IP / Host</div>
            <div style="font-weight: 700; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 15px; color: #0f172a;">
              {if $license_ip}{$license_ip}{else}<span style="color: #94a3b8; font-style: italic; font-weight: normal;">Tanımlanmamış (İlk kurulumda otomatik kilitlenir)</span>{/if}
            </div>
          </div>
          
          <form method="post" action="clientarea.php?action=productdetails&id={$service_id}" style="display: flex; gap: 8px; align-items: center;">
            <input type="hidden" name="modop" value="custom">
            <input type="hidden" name="a" value="update_ip">
            <input type="text" name="new_ip" value="{$license_ip}" placeholder="Yeni IP (Örn: 185.120.45.67)" required style="border: 1px solid #cbd5e1; padding: 8px 12px; border-radius: 6px; font-family: ui-monospace, monospace; font-size: 13px; width: 210px;">
            <button type="submit" style="background: #0f172a; color: #ffffff; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: background 0.15s ease;">
              IP Güncelle
            </button>
          </form>
        </div>
      </div>
    {else}
      <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; color: #92400e; font-size: 13.5px;">
        Lisans anahtarınız henüz oluşturulmadı veya provizyon aşamasındadır. Lütfen birkaç dakika sonra sayfayı yenileyiniz veya destek talebi açınız.
      </div>
    {/if}
  </div>

  {if $is_issued}
  <!-- Kurulum Kılavuzu Kartı -->
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 26px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 10px 0; color: #1e293b;">Hızlı Sunucu Kurulum Komutu</h3>
    <p style="font-size: 13.5px; color: #64748b; margin-bottom: 14px; line-height: 1.5;">
      Temiz Ubuntu 22.04 / 24.04, Debian 12 veya AlmaLinux 9 sunucunuzun root SSH terminaline bağlanarak aşağıdaki komutu yapıştırın:
    </p>
    
    <div style="position: relative;">
      <pre style="background: #0f172a; color: #38bdf8; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; padding: 16px 20px; border-radius: 8px; font-size: 13px; overflow-x: auto; margin: 0; line-height: 1.5;">curl -fsSL https://get.zepanel.com/install.sh | bash -s -- --license={$license_key}</pre>
    </div>

    <div style="margin-top: 14px; font-size: 12.5px; color: #64748b; display: flex; gap: 20px; flex-wrap: wrap;">
      <span>&check; <strong>Web Sunucusu:</strong> zMaxServer OpenLiteSpeed (LSAPI)</span>
      <span>&check; <strong>Yönetim Portu:</strong> 8026 (HTTPS)</span>
      <span>&check; <strong>Webmail Portu:</strong> 8046</span>
    </div>
  </div>
  {/if}

</div>
