# 05 Development Tasks

## 基本ルール

作業は以下の順序で行う。

1. `01_specification.md` を更新する。
2. `02_basic_design.md` を更新する。
3. `03_detailed_design.md` を更新する。
4. `04_test_specification.md` を更新する。
5. `05_development_tasks.md` に実装タスクを分解する。
6. 必要なら `06_project_structure.md` と `07_error_logging_policy.md` を更新する。
7. テストを追加または更新する。
8. ソースコードを実装する。
9. `php artisan test` と `npm run build` を実行する。
10. 変更履歴が必要な場合は既存の `out-line.md`、または本体系に追加する履歴ファイルへ追記する。

## 現在完了済み

- 会員ポータル UI を `karuta-hub` 寄りに調整。
- SPEC 駆動の初期ドキュメントを作成。
- 年間登録を年度単位へ変更。
- 年間登録の Unit/Feature テストを更新。

## 年間登録改修タスク

- [x] 仕様定義: 年度期間、更新開始日、重複更新不可を定義。
- [x] 基本設計: 画面、DB、処理概要を定義。
- [x] 詳細設計: `MembershipService` と `MembershipController` の責務を定義。
- [x] テスト仕様: 3/9、3/10、未登録、期限切れ、重複を定義。
- [x] 実装: 年数選択を廃止。
- [x] 実装: `MembershipService::preview()` を追加。
- [x] 実装: `MembershipService::renew()` を年度単位に変更。
- [x] 実装: 年間登録画面に対象期間と不可理由を表示。
- [x] 検証: `php artisan test`。
- [x] 検証: `npm run build`。

## 次に整理すべき候補

### 会員管理

- 管理者が会員一覧を閲覧できる画面。
- 管理者が会員の段位、年間登録期限、管理者権限を確認できる画面。
- 手動修正を許可するか検討。

### 年間登録の承認制

- 年会費支払い後に管理者承認するか検討。
- `memberships.status` の追加要否を検討。
- 決済ID、入金日、承認者の追加要否を検討。

### 大会公開制御

- 大会の公開/非公開。
- 募集中/締切/終了のステータス表示。
- 会場、外部申込フォーム URL。

### エントリーキャンセル

- 会員によるキャンセル可否。
- 締切後キャンセル可否。
- 管理者キャンセル可否。

### API 方針

- Web と API の機能差分を整理。
- API Resource 導入要否。
- エラー形式の統一。
