# API仕様書

本ドキュメントは、現在の Web（Blade）実装をベースに、
将来的な React / Next.js 移行を見据えて API 観点で整理した仕様書である。

---

## 1. 認証・認可

### 認証方式
- 現状：Laravel Breeze（セッション認証）
- 将来想定：Laravel Sanctum（SPA/API）

### 権限
| 種別 | 条件 |
|---|---|
| 会員 | ログイン済み |
| 管理者 | ログイン済み ＋ users.is_admin = true |

---

## 2. 段位定義

### 段位定義取得（プレビュー用）
- Method: GET
- Path: `/rank-definitions/{rank_id}`
- 認証: 要（会員）

#### Response
```json
{
  "id": 6,
  "level": 4,
  "label": "四段（A級）"
}
## 3. 大会

### 大会一覧取得
- Method: GET
- Path: `/tournaments`
- 認証: 要（会員）

#### Response（想定）
```json
[
  {
    "id": 1,
    "title": "第10回○○大会",
    "event_date": "2026-01-20",
    "min_rank_level": 3,
    "min_rank_display": "A,B級",
    "entry_deadline": "2026-01-10"
  }
]

### 大会詳細取得
- Method: GET
- Path: `/tournaments/{tournament_id}`
- 認証: 要（会員）

---

## 4. 大会エントリー

### エントリー
- Method: POST
- Path: `/tournaments/{tournament_id}/entry`
- 認証: 要（会員）

#### 制約
- ユーザー段位 level >= 大会 min_rank_level
- 同一大会への重複エントリー不可
- 締切超過時は不可

---

## 5. 成績

### 自身の成績一覧
- Method: GET
- Path: `/results`
- 認証: 要（会員）

---

## 6. 段位申請（会員）

### 段位申請作成
- Method: POST
- Path: `/rank-requests`
- 認証: 要（会員）

#### Request
```json
{
  "requested_rank_id": 6,
  "note": "申請理由（任意）"
}
#### 制約
- 現在の段位以上のみ申請可能
- 未処理申請がある場合は申請不可

---

### 段位申請履歴取得
- Method: GET
- Path: `/rank-requests/history`
- 認証: 要（会員）

---

## 7. 段位申請管理（管理者）

### 段位申請一覧
- Method: GET
- Path: `/admin/rank-requests`
- 認証: 要（管理者）

---

### 承認
- Method: POST
- Path: `/admin/rank-requests/{id}/approve`
- 認証: 要（管理者）

#### Request
```json
{
  "admin_comment": "承認コメント（任意）"
}

#### 処理内容
- status を「承認」に更新
- 承認者・承認日時を保存
- users.rank_id を申請段位へ更新

---

### 却下
- Method: POST
- Path: `/admin/rank-requests/{id}/reject`
- 認証: 要（管理者）

---

## 8. ステータス定義

### rank_requests.status
| 値 | 意味 |
|---|---|
| 0 | 未処理 |
| 1 | 承認 |
| 2 | 却下 |

