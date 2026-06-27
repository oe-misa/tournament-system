# 06 Project Structure

## 参照

このファイルは共通標準の `docs/common/06_project_structure.md` を前提に、このプロジェクトでの配置と責務の補足だけを記載する。

## このプロジェクトの配置

```text
app/
  Http/
    Controllers/
      Auth/
      Web/
      Admin/
      Api/
    Middleware/
    Requests/
  Models/
  Services/
  Support/
database/
  migrations/
  seeders/
resources/
  views/
    auth/
    layouts/
    components/
    site/
    tournaments/
    results/
    membership/
    rank_requests/
    admin/
  css/
  js/
routes/
  web.php
  api.php
  auth.php
tests/
  Unit/
  Feature/
docs/
  spec-driven/
  common/
```

## このプロジェクト固有の補足

- `Auth` は Breeze 認証を維持する。
- `Web` は会員向け画面を担当する。
- `Admin` は管理者画面を担当する。
- `Api` は外部利用を想定するが、機能は段階的に拡張する。
- `Support` は表示変換や共通補助に限定する。

## 機能別配置の考え方

- 機能ごとの Controller と Service は、共通標準の責務分担に従って追加する。
- 新機能はまず `Service` に業務ルールを置き、Controller は薄く保つ。
- 画面単位で分かれる場合は `resources/views/<feature>/` 配下にまとめる。
