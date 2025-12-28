# DB仕様書（テーブル定義）

## 共通
- DB: MySQL
- 文字コード: utf8mb4
- 主要な時刻カラムは datetime を想定（created_at/updated_at 等）

---

## 1. users（会員/管理者）
| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | ユーザーID |
| name | varchar | NO |  | 氏名 |
| email | varchar | NO | UQ | メール |
| password | varchar | NO |  | ハッシュ |
| is_admin | tinyint(1) | NO |  | 管理者フラグ |
| rank_id | bigint | YES | FK | 現在段位（ranks.id） |
| membership_expires_at | datetime | YES |  | 年間登録期限 |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

リレーション
- users.rank_id → ranks.id

---

## 2. ranks（段位マスタ）
| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | 段位ID |
| level | tinyint | NO | IDX | 0〜10（数値段位） |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

表示ルール（アプリ側の変換）
- 0: 無段（F〜E級）
- 1: 初段（D級）
- 2: 弐段（C級）
- 3: 参段（B級）
- 4〜10: A級（四段〜十段）

---

## 3. tournaments（大会）
| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | 大会ID |
| title | varchar | NO |  | 大会名 |
| event_date | date/datetime | NO | IDX | 開催日 |
| description | text | YES |  | 説明 |
| min_rank_level | tinyint | NO |  | 参加可能最低段位（0〜10） |
| entry_deadline | datetime | YES |  | 締切 |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

---

## 4. entries（大会エントリー）
| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | エントリーID |
| tournament_id | bigint | NO | FK | 大会ID |
| user_id | bigint | NO | FK | 会員ID |
| status | varchar | NO |  | entry/cancel など |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

制約
- (tournament_id, user_id) で一意（同一大会に重複エントリー防止）

---

## 5. results（成績）
| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | 成績ID |
| tournament_id | bigint | NO | FK | 大会ID |
| user_id | bigint | NO | FK | 会員ID |
| placing | int | YES |  | 順位 |
| score | int | YES |  | スコア（任意） |
| note | text | YES |  | 備考 |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

制約
- (tournament_id, user_id) で一意（同一大会で成績は1つ）

---

## 6. rank_requests（段位申請）
※既存DBの事情によりカラムが多い構成。アプリ側は下記を使用。

| カラム | 型 | Null | Key | 説明 |
|---|---|---:|---|---|
| id | bigint | NO | PK | 申請ID |
| user_id | bigint | NO | FK | 申請者 |
| status | int | NO | IDX | 0=未処理, 1=承認, 2=却下 |
| requested_at | datetime | YES/NO | IDX | 申請日時 |
| rank_id | bigint | NO | FK | 申請段位（DB必須カラムとして使用） |
| requested_rank_id | bigint | YES | FK | 申請段位（互換/将来用） |
| requested_level | tinyint | YES | IDX | 申請段位level（表示/互換） |
| note | text | YES |  | 会員備考 |
| admin_comment | text | YES |  | 管理者コメント |
| approved_by | bigint | YES | FK | 承認担当者 |
| approved_at | datetime | YES | IDX | 承認日時 |
| rejected_by | bigint | YES | FK | 却下担当者 |
| rejected_at | datetime | YES | IDX | 却下日時 |
| created_at | datetime | YES |  | |
| updated_at | datetime | YES |  | |

リレーション
- rank_requests.user_id → users.id
- rank_requests.rank_id → ranks.id
- rank_requests.requested_rank_id → ranks.id（存在する場合）
- rank_requests.approved_by → users.id（管理者）
- rank_requests.rejected_by → users.id（管理者）

運用ルール
- 承認時：users.rank_id を申請段位へ更新
- 表示用日付：approved_at / rejected_at / requested_at の優先順で YYMMDD 表示
- 担当者：approved_by or rejected_by を表示

---

## 7. 主要インデックス（推奨）
- ranks: level
- tournaments: event_date, min_rank_level
- entries: (tournament_id, user_id) unique
- results: (tournament_id, user_id) unique
- rank_requests: status, requested_at, approved_at, rejected_at, requested_level

