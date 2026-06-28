# 02 Basic Design

## 参照方針

- 業務ルールと状態遷移は `docs/common/08_implementation_policy.md` を前提に記述する。
- 書式と粒度は `docs/common/09_spec_document_standards.md` を前提に記述する。
- エラーの返し方は `docs/common/07_error_logging_policy.md` を前提に記述する。
- フォルダ責務は `docs/common/06_project_structure.md` を前提に記述する。

## システム構成

- Backend: Laravel 12
- UI: Blade + Tailwind CSS
- Auth: Laravel Breeze
- API Auth: Laravel Sanctum
- DB: MySQL 想定
- Test: Pest
- 業務ルールの真実は Service に置く
- 画面表示用の判定は Controller で整形し、保存・更新は Service に委譲する

## 画面一覧

| 区分 | 画面 | URL | 概要 |
| --- | --- | --- | --- |
| 公開 | トップ | `/` | 会員ポータルと公式案内への導線 |
| 公開 | 会員入口 | `/mypage` | ログイン・新規登録への導線 |
| 認証 | ログイン | `/login` | Breeze ログイン |
| 認証 | 新規登録 | `/register` | Breeze 登録 |
| 会員 | ダッシュボード | `/dashboard` | 会員状態、主要導線、おみくじ |
| 会員 | プロフィール | `/profile` | 氏名、メール、パスワード、退会 |
| 会員 | 大会一覧 | `/tournaments` | 大会一覧 |
| 会員 | 大会詳細 | `/tournaments/{tournament}` | 大会詳細、エントリー |
| 会員 | エントリーキャンセル | `/tournaments/{tournament}/entry` | 自分のエントリーキャンセル |
| 会員 | 成績一覧 | `/results` | 自分の成績一覧 |
| 会員 | 年間登録 | `/membership/renew` | 年度単位の更新画面 |
| 会員 | 段位申請 | `/rank-requests` | 段位申請フォーム |
| 会員 | 段位申請履歴 | `/rank-requests/history` | 自分の申請履歴 |
| 会員 | 段位定義プレビュー | `/rank-definitions/{rank}` | 段位ラベル確認用 JSON |
| 管理 | 管理ダッシュボード | `/admin` | 管理メニュー、未処理件数 |
| 管理 | 会員管理 | `/admin/users` | 会員一覧、詳細、編集 |
| 管理 | 会員詳細 | `/admin/users/{user}` | 会員詳細表示 |
| 管理 | 会員編集 | `/admin/users/{user}/edit` | 会員情報編集 |
| 管理 | 大会管理 | `/admin/tournaments` | 大会 CRUD |
| 管理 | 大会作成 | `/admin/tournaments/create` | 大会作成 |
| 管理 | 大会編集 | `/admin/tournaments/{tournament}/edit` | 大会編集 |
| 管理 | 大会表示 | `/admin/tournaments/{tournament}` | 編集画面へリダイレクト |
| 管理 | 成績入力 | `/admin/tournaments/{tournament}/results` | 大会別成績入力 |
| 管理 | 段位申請管理 | `/admin/rank-requests` | 段位申請の承認・却下 |

## Web ルート

### 公開

- `GET /`
- `GET /mypage`

### 会員

`auth` middleware 配下。

- `GET /dashboard`
- `GET /profile`
- `PATCH /profile`
- `DELETE /profile`
- `GET /tournaments`
- `GET /tournaments/{tournament}`
- `POST /tournaments/{tournament}/entry`
- `DELETE /tournaments/{tournament}/entry`
- `GET /results`
- `GET /membership/renew`
- `POST /membership/renew`
- `GET /rank-requests`
- `POST /rank-requests`
- `GET /rank-requests/history`
- `GET /rank-definitions/{rank}`
- `POST /omikuji/draw`

### 管理

`auth` + `admin` middleware、`/admin` prefix 配下。

- `GET /admin`
- `Route::resource('/admin/tournaments')`
- `Route::resource('/admin/users')`
- `GET /admin/rank-requests`
- `POST /admin/rank-requests/{rankRequest}/approve`
- `POST /admin/rank-requests/{rankRequest}/reject`
- `DELETE /admin/tournaments/{tournament}/entries/{entry}`
- `GET /admin/tournaments/{tournament}/results`
- `POST /admin/tournaments/{tournament}/results`

## API

`auth:sanctum` middleware 配下。

| Method | Path | 概要 | 主な返却 |
| --- | --- | --- | --- |
| GET | `/api/me` | 自分自身 | 会員情報、段位、年間登録期限、管理者フラグ |
| GET | `/api/tournaments` | 大会一覧 | pagination 付き大会一覧 |
| GET | `/api/tournaments/{tournament}` | 大会詳細 | 大会 1 件の JSON |
| POST | `/api/tournaments/{tournament}/entries` | 大会エントリー | message, entry |
| DELETE | `/api/tournaments/{tournament}/entries` | 大会エントリーキャンセル | message, entry |
| GET | `/api/results` | 自分の成績一覧 | pagination 付き成績一覧 |
| POST | `/api/rank-requests` | 段位申請 | message, rank_request |

## DB 概要

| Table | 役割 |
| --- | --- |
| `users` | 会員、管理者、認証情報、段位、年間登録期限 |
| `ranks` | 段位定義 |
| `tournaments` | 大会 |
| `entries` | 大会エントリー |
| `results` | 大会成績 |
| `memberships` | 年間登録履歴 |
| `rank_requests` | 段位申請と処理履歴 |
| `omikuji_draws` | 日次おみくじ結果 |
| `personal_access_tokens` | Sanctum API token |

### 主要キーと制約

| Table | 主要キー / 制約 |
| --- | --- |
| `users` | `rank_id`, `membership_expires_at`, `is_admin` |
| `ranks` | `kyu + dan` 一意、`level` index |
| `tournaments` | `status`, `event_date`, `entry_deadline`, `min_rank_level` index |
| `entries` | `user_id + tournament_id` 一意、`tournament_id + status` index |
| `results` | `user_id + tournament_id` 一意 |
| `memberships` | `user_id + end_date` index |
| `rank_requests` | `status + requested_at` index、`user_id + status` index |
| `omikuji_draws` | `user_id + drawn_on` 一意 |

## 主要処理の全体像

### 大会エントリー

1. 会員が大会詳細からエントリーする。
2. `EntryController` がログインユーザーと大会を取得する。
3. `EntryService` が年間登録、段位、締切、定員、重複を検証する。
4. 問題なければ `entries` に登録する。
5. 既存エントリーがある場合は成功扱いで既存データを返す。
6. Web はリダイレクト、API は JSON を返す。

### 大会エントリーキャンセル

1. 会員または管理者がキャンセル操作を行う。
2. `EntryController` または `AdminTournamentController` が対象エントリーを取得する。
3. `EntryService` が操作主体、締切、権限を検証する。
4. 問題なければ `entries.status` を `cancelled` に更新する。
5. キャンセル済みのエントリーは再エントリーで `entry` に戻せる。
6. Web はリダイレクト、API は JSON を返す。

### 年間登録

1. 会員が年間登録画面を開く。
2. `MembershipService::preview()` が今回の更新対象期間と実行可否を返す。
3. 会員が更新ボタンを押す。
4. `MembershipService::renew()` が `memberships` に履歴を作成し、`users.membership_expires_at` を更新する。

### 段位申請

1. 会員が申請段位を選択する。
2. `RankDefinitionController` で段位ラベルをプレビューする。
3. `RankRequestService` が現在段位以上か、未処理申請がないか検証する。
4. `rank_requests` に `pending` で保存する。
5. 管理者が承認または却下する。
6. 承認時は `users.rank_id` も更新する。

### 管理者大会管理

1. 管理者が大会一覧を開く。
2. `AdminTournamentController` が `Tournament` を開催日降順で一覧化する。
3. 一覧は `all / upcoming / past / draft / recruiting / closed / finished` で絞り込める。
4. 新規作成、編集、削除はそれぞれ `store` / `update` / `destroy` で処理する。
5. `show` は独立した表示ページを持たず、編集画面へリダイレクトする。

### 会員管理

1. 管理者が会員管理画面を開く。
2. `AdminUserController` が会員を検索・ページングして一覧する。
3. 会員詳細では段位、年間登録期限、管理者権限、関連履歴を表示する。
4. 会員編集では氏名、メールアドレス、段位、年間登録期限、管理者権限を更新する。

### 管理者成績入力

1. 管理者が大会別成績入力画面を開く。
2. `Entry` の `entry` 状態のみを参加対象として取得する。
3. 既存 `Result` を大会・会員単位で読み込む。
4. 入力値を `Result::updateOrCreate()` で保存する。

### 管理者ダッシュボード

1. 管理者が `/admin` を開く。
2. `pending` の段位申請件数を集計する。
3. `entry` 状態のうち未採点の件数を集計する。
4. 管理者用リンクと件数を表示する。

### 会員成績閲覧

1. 会員が成績一覧画面を開く。
2. `ResultController` が自分の `results` を開催日降順で取得する。
3. 各成績には大会名、開催日、順位、スコア、メモを表示する。
4. 変更操作は行わず、閲覧専用とする。

### おみくじ

1. 会員がダッシュボードでおみくじを引く。
2. 当日分の既存結果を確認する。
3. 未作成ならランダムに結果を決めて `omikuji_draws` に保存する。
4. 同日重複は既に引いた扱いで返す。

### API

1. `auth:sanctum` で認証する。
2. `me` は会員情報と段位、年間登録期限を返す。
3. `tournaments` は一覧・詳細を JSON で返す。
4. `entries` は `EntryService` 経由で登録し、成功時は作成済み `Entry` を返す。
5. `results` は自分の成績のみ返す。
6. `rank-requests` は段位申請を作成し、作成済み `RankRequest` を返す。

## 画面とデータの対応

| 画面 | 主な参照データ | 主な更新データ |
| --- | --- | --- |
| トップ / 会員入口 | なし | なし |
| ダッシュボード | `users`, `omikuji_draws`, `rank_requests`, `entries`, `results` | `omikuji_draws` |
| プロフィール | `users`, `ranks` | `users` |
| 年間登録 | `users`, `memberships` | `memberships`, `users.membership_expires_at` |
| 大会一覧 / 詳細 | `tournaments`, `entries` | `entries` |
| エントリーキャンセル | `entries`, `tournaments` | `entries` |
| 成績一覧 | `results`, `tournaments` | なし |
| 段位申請 | `ranks`, `rank_requests`, `users.rank` | `rank_requests` |
| 段位申請履歴 | `rank_requests`, `ranks`, `users` | なし |
| 管理ダッシュボード | `rank_requests`, `entries`, `results`, `tournaments`, `users` | なし |
| 会員管理 | `users`, `ranks`, `rank_requests`, `entries`, `results`, `memberships` | `users` |
| 大会管理 | `tournaments` | `tournaments` |
| 成績入力 | `entries`, `results`, `tournaments` | `results` |

## 主要画面の役割

- トップ: ポータルへの入口と会員導線。
- 会員入口: ログイン、新規登録への入口。
- ダッシュボード: 会員状態の要約と主要機能導線。
- プロフィール: アカウント情報の更新。
- 年間登録: 更新可否の確認と登録実行。
- 大会一覧 / 詳細: 大会閲覧とエントリー。
- 成績一覧: 自分の成績閲覧。
- 段位申請: 段位申請作成。
- 段位申請履歴: 申請履歴閲覧。
- 管理ダッシュボード: 未処理件数、下書き大会件数、会員総数の確認と管理導線。
- 大会管理: 大会 CRUD。
- 成績入力: 大会別成績の更新。
- 段位申請管理: 承認・却下。
