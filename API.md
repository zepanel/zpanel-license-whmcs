# zPanel Resmi Partner API Lisans Otomasyonu Dokümantasyonu

zPanel Lisans modülü, resmi zPanel Partner REST API (`https://console.zepanel.com/partner-api/v1`) üzerinden Machine-to-Machine mimarisiyle çalışır.

---

## 1. Mimarisi ve Kimlik Doğrulama

Bayiler ve hosting sağlayıcıları, `console.zepanel.com/partner/api` üzerinden oluşturdukları kalıcı Bearer Token ile API'ye erişir.

```http
Authorization: Bearer zp_partner_live_...
Content-Type: application/json
Accept: application/json
```

---

## 2. Partner API Uç Noktaları

### 2.1. Havuzdan Yeni Lisans Üretme / Tahsis Etme
- **Uç Nokta:** `POST https://console.zepanel.com/partner-api/v1/orders/:orderId/licenses`
- **İstek Gövdesi:**
```json
{
  "customer_name": "Ahmet Yılmaz (Firma A.Ş.)",
  "customer_domain": "srv1.example.com"
}
```
- **Başarılı Yanıt (201 Created):**
```json
{
  "success": true,
  "license": {
    "id": "lic_9921448",
    "license_key": "ZP-PRO-8829-1142-9901",
    "ip_address": "srv1.example.com",
    "status": "active",
    "client_port": 8026,
    "admin_port": 8036,
    "api_port": 8056,
    "created_at": "2026-11-16T12:00:00Z"
  }
}
```

### 2.2. Lisans IP / Domain Güncelleme (Reissue / Info Update)
- **Uç Nokta:** `POST https://console.zepanel.com/partner-api/v1/orders/:orderId/licenses/:licenseId/info`
- **İstek Gövdesi:**
```json
{
  "customer_name": "Ahmet Yılmaz",
  "customer_domain": "185.120.45.67"
}
```
- **Başarılı Yanıt (200 OK):**
```json
{
  "success": true,
  "message": "Lisans IP adresi başarıyla güncellendi."
}
```

### 2.3. Lisansı Havuza İade Etme / Serbest Bırakma (Release / Terminate)
- **Uç Nokta:** `POST https://console.zepanel.com/partner-api/v1/orders/:orderId/licenses/:licenseId/release`
- **Başarılı Yanıt (200 OK):**
```json
{
  "success": true,
  "message": "Lisans başarıyla serbest bırakılarak havuza iade edildi."
}
```

---

## 3. Kurulum ve Doğrulama
Yeni kurulan sunucuda lisans aktivasyonu:
```bash
curl -fsSL https://get.zepanel.com/install.sh | bash -s -- --license=ZP-PRO-8829-1142-9901
```
Panel aktif edildiğinde web sunucusu (zMaxServer - OpenLiteSpeed LSAPI) otomatik olarak 8026 (Client), 8036 (Root) ve 8046 (Webmail) portlarında dinlemeye başlar.
