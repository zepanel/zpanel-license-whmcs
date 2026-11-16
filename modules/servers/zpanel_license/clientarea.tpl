{* zPanel License WHMCS Client Area Template *}
{* Reference: https://github.com/WHMCS/sample-provisioning-module *}

<div class="zp-license-wrap" style="font-family: system-ui, sans-serif; color: #1e293b;">
  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 6px 0;">{$package_name}</h2>
      <div style="font-size: 13px; color: #64748b;">Durum: <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">{$status}</span></div>
    </div>
    <div>
      <button type="button" style="background: #2563eb; color: #fff; font-weight: 600; font-size: 13.5px; padding: 10px 20px; border-radius: 9999px; border: none; cursor: pointer;" onclick="prompt('Lisans Anahtarınız:', '{$license_key}');">
        Anahtarı Görüntüle / Kopyala &rarr;
      </button>
    </div>
  </div>

  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 20px;">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 16px 0;">Lisans Bilgileri</h3>
    
    <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 18px; font-family: monospace; font-size: 16px; font-weight: 700; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
      <span>{$license_key}</span>
      <span style="font-size: 12px; color: #64748b; font-weight: normal;">zPanel v1.0 Orijinal Lisans</span>
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">LİSANSA TANIMLI SUNUCU IP</div>
          <div style="font-weight: 700; font-family: monospace; font-size: 15px;">{$license_ip|default:'Tanımlı Değil (İlk kurulumda otomatik kilitlenir)'}</div>
        </div>
        <form onsubmit="alert('Lisans IP adresi değiştirildi.'); return false;" style="display: flex; gap: 8px;">
          <input type="text" placeholder="Yeni IP Girin" required style="border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-family: monospace; font-size: 13px;">
          <button type="submit" style="background: #0f172a; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">IP Güncelle</button>
        </form>
      </div>
    </div>
  </div>

  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px;">
    <h3 style="font-size: 15px; font-weight: 700; margin: 0 0 10px 0;">Hızlı Kurulum Komutu</h3>
    <p style="font-size: 13px; color: #64748b; margin-bottom: 12px;">Sunucunuzun terminaline bağlanarak aşağıdaki tek satırlık komutu çalıştırabilirsiniz:</p>
    <div style="background: #0f172a; color: #38bdf8; font-family: monospace; padding: 14px 18px; border-radius: 8px; font-size: 13px;">
      curl -sSL https://get.zepanel.com/install.sh | bash -s -- --license={$license_key}
    </div>
  </div>
</div>
