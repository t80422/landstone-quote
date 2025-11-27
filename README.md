# LandStone 報價系統

一個基於 CodeIgniter 4 + MySQL + Bootstrap 5 開發的內部報價管理系統。

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.3-red)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange)

## 📋 系統簡介

LandStone 報價系統是一個專為內部使用設計的報價管理平台，提供完整的報價流程管理、產品管理、客戶管理等功能。

### 主要功能（規劃中）

- 📝 報價單管理
- 📦 產品管理
- 👥 客戶管理
- 📊 報表分析
- 👤 使用者權限管理
- ⚙️ 系統設定

## 🛠️ 技術棧

### 後端
- **框架**: CodeIgniter 4.6.3
- **語言**: PHP 8.1+
- **資料庫**: MySQL 8.0+
- **架構**: MVC

### 前端
- **UI 框架**: Bootstrap 5.3.2
- **圖示**: Bootstrap Icons 1.11.1
- **JavaScript**: Vanilla JS (ES6+)

## 📦 系統需求

- PHP >= 8.1
- MySQL >= 8.0
- Composer
- Apache/Nginx with mod_rewrite

### PHP 擴充套件

- intl
- mbstring
- mysqli
- json
- curl

## 🚀 安裝步驟

### 1. 克隆專案（如果從 Git）

```bash
git clone <repository-url> landstone-quote
cd landstone-quote
```

### 2. 安裝依賴

```bash
composer install
```

### 3. 環境設定

複製 `.env` 檔案並進行設定：

```bash
# Windows
copy env .env

# Linux/Mac
cp env .env
```

### 4. 設定環境變數

編輯 `.env` 檔案，設定以下關鍵參數：

```ini
# 環境設定
CI_ENVIRONMENT = development

# 應用程式設定
app.baseURL = 'http://localhost:8080/'

# 資料庫設定
database.default.hostname = localhost
database.default.database = landstone_quote
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.port = 3306

# 時區設定
app.timezone = 'Asia/Taipei'
```

### 5. 建立資料庫

```sql
CREATE DATABASE landstone_quote CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. 執行遷移（待實作）

```bash
php spark migrate
```

### 7. 產生加密金鑰

```bash
php spark key:generate
```

### 8. 啟動開發伺服器

```bash
php spark serve
```

系統將在 `http://localhost:8080` 上運行。

## 📁 專案結構

```
landstone-quote/
├── app/
│   ├── Config/          # 設定檔
│   ├── Controllers/     # 控制器
│   ├── Models/          # 模型
│   ├── Views/           # 視圖
│   │   ├── layouts/     # 佈局模板
│   │   ├── partials/    # 共用元件
│   │   └── home/        # 首頁視圖
│   ├── Filters/         # 過濾器
│   ├── Helpers/         # 輔助函數
│   └── Libraries/       # 自訂類庫
├── public/              # 公開資料夾
│   ├── css/            # 樣式表
│   ├── js/             # JavaScript
│   ├── images/         # 圖片
│   └── index.php       # 入口點
├── writable/            # 可寫入目錄
│   ├── cache/          # 快取
│   ├── logs/           # 日誌
│   └── uploads/        # 上傳檔案
├── tests/               # 測試檔案
├── .env                 # 環境變數
├── composer.json        # Composer 設定
└── README.md           # 說明文件
```

## 🎨 UI 設計

系統採用現代化的後台管理介面設計：

- **導航欄**: 固定頂部，包含通知和使用者選單
- **側邊欄**: 主要功能導航
- **儀表板**: 統計卡片和快速操作
- **響應式設計**: 支援桌面和行動裝置

## 🔧 開發指南

### 新增控制器

```bash
php spark make:controller QuotationController
```

### 新增模型

```bash
php spark make:model QuotationModel
```

### 新增遷移

```bash
php spark make:migration CreateQuotationsTable
```

### 程式碼風格

- 遵循 PSR-12 編碼標準
- 使用有意義的變數和函數命名
- 適當的註解和文件
- 保持程式碼簡潔和可維護性

## 📝 開發規範

1. **版本控制**: 使用 Git 進行版本控制
2. **分支策略**: 
   - `main`: 生產環境
   - `develop`: 開發環境
   - `feature/*`: 功能開發
   - `hotfix/*`: 緊急修復
3. **提交訊息**: 使用清晰的提交訊息
4. **程式碼審查**: 所有變更需經過審查

## 🧪 測試

```bash
# 執行所有測試
./vendor/bin/phpunit

# 執行特定測試
./vendor/bin/phpunit --filter testMethodName
```

## 📚 相關文件

- [CodeIgniter 4 官方文件](https://codeigniter.com/user_guide/)
- [Bootstrap 5 官方文件](https://getbootstrap.com/docs/5.3/)
- [MySQL 官方文件](https://dev.mysql.com/doc/)

## 🐛 問題回報

如有問題或建議，請聯繫開發團隊。

## 📄 授權

內部專案 - 保留所有權利

## 👥 開發團隊

LandStone Development Team

---

**版本**: 1.0.0  
**更新日期**: 2024-11-24  
**開發環境**: PHP 8.1+ / CodeIgniter 4.6.3 / MySQL 8.0+
