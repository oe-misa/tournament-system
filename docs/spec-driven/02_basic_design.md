# 02 Basic Design

## システム構成

- Backend: Laravel 12
- UI: Blade + Tailwind CSS
- Auth: Laravel Breeze
- API Auth: Laravel Sanctum
- DB: MySQL 想定
- Test: Pest

## 画面一覧

| 区分 | 画面 | URL | 概要 |
| --- | --- | --- | --- |
| 公開 | トップ | `/` | 会員ポータルと公式サイトへの導線 |
| 公開 | 会員入口 | `/mypage` | ログイン・新規登録への導線 |
| 認証 | ログイン | `/login` | Breeze ログイン |
| 認証 | 新規登録 | `/register` | Breeze 登録 |
| 会員 | ダッシュボード | `/dashboard` | 会員状態、主要導線、おみくじ |
| 会員 | プロフィール | `/profile` | 氏名、メール、パスワード、退会 |
| 会員 | 大会一覧 | `/tournaments` | 大会一覧 |
| 会員 | 大会詳細 | `/tournaments/{tournament}` | 大会詳細、エントリー |
| 会員 | 成績一覧 | `/results` | 自分の成績一覧 |
| 会員 | 年間登録 | `/membership/renew` | 年度単位の年間登録 |
| 会員 | 段位申請 | `/rank-requests` | 段位申請フォーム |
| 会員 | 段位申請履歴 | `/rank-requests/history` | 自分の申請履歴 |
| 管理 | 管理ダッシュボード | `/admin` | 管理メニュー、未処理件数 |
| 管理 | 大会管理 | `/admin/tournaments` | 大会 CRUD |
| 管理 | 成績入力 | `/admin/tournaments/{tournament}/results` | 大会別成績入力 |
| 管理 | 段位申請管理 | `/admin/rank-requests` | 申請承認・却下 |

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
- `GET /admin/rank-requests`
- `POST /admin/rank-requests/{rankRequest}/approve`
- `POST /admin/rank-requests/{rankRequest}/reject`
- `GET /admin/tournaments/{tournament}/results`
- `POST /admin/tournaments/{tournament}/results`

## API

`auth:sanctum` middleware 配下。

| Method | Path | 概要 |
| --- | --- | --- |
| GET | `/api/me` | 自分自身 |
| GET | `/api/tournaments` | 大会一覧 |
| GET | `/api/tournaments/{tournament}` | 大会詳細 |
| POST | `/api/tournaments/{tournament}/entries` | 大会エントリー |
| GET | `/api/results` | 自分の成績一覧 |
| POST | `/api/rank-requests` | 段位申請 |

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

## 主要処理の全体像

### 大会エントリー

1. 会員が大会詳細からエントリーする。
2. `EntryController` がログインユーザーと大会を取得する。
3. `EntryService` が年間登録、段位、締切、定員、重複を検証する。
4. 問題なければ `entries` に登録する。
5. Web はリダイレクト、API は JSON を返す。

### 年間登録

1. 会員が年間登録画面を開く。
2. `MembershipService::preview()` が今回登録対象の年度期間と実行可否を返す。
3. 会員が登録ボタンを押す。
4. `MembershipService::renew()` が年度単位で登録期間を確定する。
5. `memberships` に履歴を作成し、`users.membership_expires_at` を更新する。

### 段位申請

1. 会員が申請段位を選択する。
2. `RankDefinitionController` で段位ラベルをプレビューする。
3. `RankRequestService` が現在段位以上か、未処理申請がないか検証する。
4. `rank_requests` に `pending` で保存する。
5. 管理者が承認または却下する。
6. 承認時は `users.rank_id` も更新する。

### 成績入力

1. 管理者が大会ごとの成績入力画面を開く。
2. エントリー済み参加者と既存成績を表示する。
3. 管理者が順位、スコア、メモを入力する。
4. `Result::updateOrCreate()` で保存する。

### おみくじ

1. 会員がダッシュボードでおみくじを引く。
2. 当日分の既存結果を確認する。
3. 未作成ならランダムに結果を決めて `omikuji_draws` に保存する。
4. 同日重複は既に引いた扱いで返す。
