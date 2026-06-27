# 03 Detailed Design

## Controller

### DashboardController

- `index(Request $request)`
  - ログインユーザーと段位を取得する。
  - 当日のおみくじ結果を取得する。
  - 管理者の場合、未処理段位申請数と成績未入力数を集計する。
  - `dashboard` view を返す。

### ProfileController

- `edit`
  - ログインユーザーを表示する。
- `update`
  - 氏名、メール、任意パスワードを更新する。
- `destroy`
  - 現在パスワード確認後にユーザーを削除する。

### Web\TournamentController

- `index`
  - 大会を開催日順に取得し、ページングする。
- `show`
  - Route Model Binding で取得した大会を表示する。

### Web\EntryController

- `store`
  - `EntryService::entry()` を呼ぶ。
  - `HttpException` は画面向けのエラーメッセージとして扱う。

### Web\ResultController

- `index`
  - ログインユーザーの成績を大会情報付きで取得する。

### Web\MembershipController

- `create`
  - `MembershipService::preview()` で登録対象期間を取得する。
  - 年間登録画面へ `user` と `membershipPreview` を渡す。
- `store`
  - 年数入力は受け取らない。
  - `MembershipService::renew()` を呼ぶ。
  - 失敗時は `HttpException` のメッセージを表示する。

### Web\RankRequestController

- `create`
  - 現在段位と全段位を取得し、申請画面を表示する。
- `store`
  - `requested_rank_id` と `note` を validate する。
  - `RankRequestService::request()` を呼ぶ。
- `history`
  - ログインユーザーの申請履歴を関連ユーザー・段位情報付きで取得する。

### Web\RankDefinitionController

- `show(Rank $rank)`
  - 段位レベル、段位ラベル、参加条件表示を JSON で返す。

### Web\OmikujiController

- `draw`
  - 当日結果があれば新規作成しない。
  - トランザクションとロックで同日二重作成を抑止する。
  - DB 制約違反時も既に引いた扱いで返す。

### Admin\AdminDashboardController

- 管理者向け未処理件数を集計し、管理ダッシュボードを表示する。

### Admin\AdminTournamentController

- 大会 CRUD を担当する。
- 入力項目:
  - `title`
  - `description`
  - `event_date`
  - `entry_deadline`
  - `capacity`
  - `min_rank_level`

### Admin\AdminResultController

- `edit`
  - 大会のエントリー済み参加者と既存成績を表示する。
- `update`
  - `results` 配列を validate する。
  - 空行は保存しない。
  - `Result::updateOrCreate()` で登録・更新する。

### Admin\AdminRankRequestController

- `index`
  - 全申請を関連情報付きで表示する。
- `approve`
  - 管理者コメントを validate し、`RankRequestService::approve()` を呼ぶ。
- `reject`
  - 管理者コメントを validate し、`RankRequestService::reject()` を呼ぶ。

### Api Controllers

- Web と同じ Service を使える処理は Service を再利用する。
- `HttpException` は JSON `message` と HTTP status へ変換する。

## Service

### EntryService

- `entry(User $user, Tournament $tournament): Entry`
  - 年間登録期限を確認する。
  - ユーザー段位と大会最低段位を比較する。
  - エントリー締切を確認する。
  - トランザクション内で定員と重複を確認する。
  - 登録可能なら `Entry` を作成する。
- `cancel(User $user, Tournament $tournament): void`
  - エントリー状態を `cancelled` にする。

### MembershipService

- `preview(User $user): array`
  - 画面表示用に登録対象期間と実行可否を返す。
  - DB 更新は行わない。
- `renew(User $user, ?string $note = null): User`
  - 登録対象期間を解決する。
  - 登録不可なら `HttpException` を投げる。
  - `memberships` に履歴を作成する。
  - `users.membership_expires_at` を対象年度末へ更新する。
- 年度判定:
  - 4/1 以降は同年 4/1 を年度開始とする。
  - 1/1〜3/31 は前年 4/1 を年度開始とする。
  - 年度終了は翌 3/31。
  - 翌年度更新開始日は年度終了年の 3/10。

### RankRequestService

- `request(User $user, Rank $targetRank, ?string $note = null): RankRequest`
  - 現在段位より下の申請を拒否する。
  - 未処理申請が既にある場合は拒否する。
  - `rank_requests` に `pending` で作成する。
- `approve(User $admin, RankRequest $request, ?string $comment = null): RankRequest`
  - 管理者以外を拒否する。
  - pending 以外を拒否する。
  - トランザクション内でユーザー段位と申請ステータスを更新する。
- `reject(User $admin, RankRequest $request, ?string $comment = null): RankRequest`
  - 管理者以外を拒否する。
  - pending 以外を拒否する。
  - 却下情報を保存する。

## Model

### User

- 認証主体。
- `rank`, `entries`, `results`, `memberships`, `rankRequests` を持つ。
- `membership_expires_at` は `date` cast。
- `password` は hashed cast。

### Rank

- 段位マスタ。
- `level` を持つ。

### Tournament

- 大会。
- `entries`, `results` を持つ。
- `event_date` は `date` cast。
- `entry_deadline` は `datetime` cast。

### Entry

- 会員の大会エントリー。
- `user`, `tournament` に属する。
- `status` は `entry` / `cancelled` を想定する。

### Result

- 大会成績。
- `user`, `tournament` に属する。

### Membership

- 年間登録履歴。
- `start_date`, `end_date` は `date` cast。
- 現在状態は `users.membership_expires_at` を参照する。

### RankRequest

- 段位申請。
- statuses:
  - `pending`
  - `approved`
  - `rejected`
- `user`, `rank`, `requestedRank`, `approver`, `rejector` を持つ。
- 表示補助:
  - `statusLabel()`
  - `handledByName()`
  - `displayDateYyMmDd()`

### OmikujiDraw

- 会員の日次おみくじ結果。
- 同一ユーザー・同一日付の重複作成を防ぐ。

## Support

### RankLabel

- `labelByLevel(int $level): string`
  - 段位レベルを段位/級ラベルへ変換する。
- `eligibleKyus(int $minLevel): string`
  - 大会参加条件表示へ変換する。
