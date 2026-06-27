# 06 Project Structure

## 目的

このドキュメントは、プロジェクト横断で共通に使うフォルダ構成と責務分担の標準を定義する。
個別プロジェクトの `06_project_structure.md` は、この標準を前提に差分だけを補足する。

## 標準構成

```text
app/
  Http/
    Controllers/
      Web/        # 会員向け画面
      Admin/      # 管理画面
      Api/        # API
    Middleware/   # 権限・共通制御
    Requests/     # FormRequest
  Models/         # Eloquent Model
  Services/       # 業務ロジック
  Actions/        # 単発処理や再利用可能な操作
  Support/        # 表示補助、変換、共通関数
resources/
  views/
    web/
    admin/
    components/
    layouts/
  css/
  js/
routes/
  web.php
  api.php
database/
  migrations/
  seeders/
tests/
  Feature/
  Unit/
docs/
  spec-driven/
  common/
```

## 責務分担

### Controller

- HTTP request を受ける。
- 認証・認可・入力検証の起点となる。
- Service を呼び出して結果を返す。
- 複雑な業務判定を置かない。

### Service

- 業務ルールを集約する。
- Controller から共通で呼ばれる処理を置く。
- トランザクション境界を扱う。

### Model

- テーブル、関連、スコープ、簡単な表示補助を持つ。
- ワークフローの本体は持たない。

### Actions

- 単一の業務操作を分離したいときに置く。
- Service で肥大化しそうな処理を小さく切る。

### Support

- 表示変換、定数の解決、共通の軽い補助を置く。
- 業務状態の更新はしない。

### View

- 画面表示とフォームのみを担当する。
- 業務ロジックは持たない。

### Test

- Unit は Service、Action、Model の振る舞いを検証する。
- Feature は HTTP、認可、遷移、画面表示を検証する。

## 標準ルール

- Controller に業務判定を増やしすぎない。
- DB 更新は Service または Action に寄せる。
- View から DB を直接触らない。
- 共通化できる処理は Support へ寄せる。

