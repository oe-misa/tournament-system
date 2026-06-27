# 06 Project Structure

## ドキュメント構成

SPEC 駆動の正本は `docs/spec-driven/` 配下とする。

```text
docs/spec-driven/
  01_specification.md       # 何を作るか
  02_basic_design.md        # 画面・API・DB・処理の大枠
  03_detailed_design.md     # Controller / Service / Model レベルの実装方針
  04_test_specification.md  # 完成条件・テストケース
  05_development_tasks.md   # 実装タスク分解
  06_project_structure.md   # フォルダ構成・責務分担
  07_error_logging_policy.md # エラー処理・ログ出力基準
```

既存の `docs/要件定義書.md`、`docs/API仕様書.md`、`docs/DB仕様書.md`、`docs/画面遷移.md`、`docs/技術スタック.md` は参考資料とする。
今後の仕様変更は `docs/spec-driven/` を先に更新する。

過去に作成したルート直下の `SPECIFICATION.md`、`Besic_Design.md`、`Dataild_design.md`、`out-line.md` は初期移行用資料であり、今後は `docs/spec-driven/` を正とする。

## アプリケーション構成

```text
app/
  Http/
    Controllers/
      Auth/       # Breeze 認証
      Web/        # 会員向け Web Controller
      Admin/      # 管理者向け Web Controller
      Api/        # Sanctum API Controller
    Middleware/   # 権限チェック
    Requests/     # FormRequest
  Models/         # Eloquent Model
  Services/       # 業務ロジック
  Support/        # 表示変換などの補助
database/
  migrations/     # DB schema
  seeders/        # 初期データ
resources/
  views/
    auth/         # 認証画面
    site/         # 公開画面
    layouts/      # 共通レイアウト
    components/   # Blade components
    tournaments/  # 会員向け大会
    results/      # 会員向け成績
    membership/   # 年間登録
    rank_requests/# 会員向け段位申請
    admin/        # 管理者画面
  css/            # Tailwind / 共通スタイル
  js/             # フロント JS
routes/
  web.php         # Web routes
  api.php         # API routes
  auth.php        # Breeze auth routes
tests/
  Unit/           # Service / Model / Support
  Feature/        # HTTP / Web / API
```

## 責務分担

### Controller

- HTTP request を受ける。
- validate を行う。
- Service または Model を呼び出す。
- Web では view または redirect を返す。
- API では JSON を返す。
- 複雑な業務判定を Controller に書かない。

### Service

- 業務ルールを集約する。
- Web/API で共通利用する処理を置く。
- トランザクション境界を管理する。
- 業務エラーは `HttpException` で表現する。

### Model

- DB table とリレーションを表現する。
- cast、fillable、scope、表示補助メソッドを持つ。
- 複雑なワークフローは Service に置く。

### Support

- DB 更新を伴わない表示変換や共通補助を置く。
- 例: `RankLabel`

### View

- 表示、フォーム、リンクを担当する。
- 業務判定は Service/Controller から渡された値を使う。
- DB を直接更新しない。

### Test

- Service の業務ルールは Unit テストで確認する。
- ルート、認証、リダイレクト、画面表示は Feature テストで確認する。
- API の status と JSON は Feature テストで確認する。

## 機能別配置

### 年間登録

- Controller: `app/Http/Controllers/Web/MembershipController.php`
- Service: `app/Services/MembershipService.php`
- Model: `app/Models/Membership.php`, `app/Models/User.php`
- View: `resources/views/membership/create.blade.php`
- Tests: `tests/Unit/Services/MembershipServiceTest.php`, `tests/Feature/Web/MemberControllersTest.php`

### 大会エントリー

- Controller: `app/Http/Controllers/Web/EntryController.php`, `app/Http/Controllers/Api/EntryController.php`
- Service: `app/Services/EntryService.php`
- Model: `app/Models/Entry.php`, `app/Models/Tournament.php`, `app/Models/User.php`
- Views: `resources/views/tournaments/*`
- Tests: `tests/Unit/Services/EntryServiceTest.php`, `tests/Feature/Web/EntryControllerTest.php`

### 段位申請

- Controller: `app/Http/Controllers/Web/RankRequestController.php`, `app/Http/Controllers/Admin/AdminRankRequestController.php`, `app/Http/Controllers/Api/RankRequestController.php`
- Service: `app/Services/RankRequestService.php`
- Model: `app/Models/RankRequest.php`, `app/Models/Rank.php`
- Views: `resources/views/rank_requests/*`, `resources/views/admin/rank_requests/*`
- Tests: `tests/Unit/Services/RankRequestServiceTest.php`, `tests/Feature/Web/MemberControllersTest.php`, `tests/Feature/Admin/AdminControllersTest.php`

### 成績

- Controller: `app/Http/Controllers/Web/ResultController.php`, `app/Http/Controllers/Admin/AdminResultController.php`, `app/Http/Controllers/Api/ResultController.php`
- Model: `app/Models/Result.php`
- Views: `resources/views/results/*`, `resources/views/admin/results/*`
- Tests: `tests/Feature/Web/MemberControllersTest.php`, `tests/Feature/Admin/AdminControllersTest.php`
