# 03 Detailed Design

## 参照方針

- 業務ルールと状態遷移は `docs/common/08_implementation_policy.md` を前提に記述する。
- 書式と粒度は `docs/common/09_spec_document_standards.md` を前提に記述する。
- エラーの返し方は `docs/common/07_error_logging_policy.md` を前提に記述する。
- フォルダ責務は `docs/common/06_project_structure.md` を前提に記述する。

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
  - 成功時は大会詳細へ戻す。
  - 失敗時は大会詳細へ戻し、`error` フラッシュを付ける。

### Web\ResultController

- `index`
  - ログインユーザーの成績を大会情報付きで取得する。
  - 変更操作は行わず、閲覧専用とする。

### Web\MembershipController

- `create`
  - `MembershipService::preview()` で更新対象期間を取得する。
  - 年間登録画面へ `user` と `membershipPreview` を渡す。
- `store`
  - `MembershipService::renew()` を呼ぶ。
  - 失敗時は `HttpException` のメッセージを表示する。
  - 成功時は `membership.create` に戻す。

### Web\RankRequestController

- `create`
  - 現在段位と全段位を取得し、申請画面を表示する。
  - `RankLabel` 変換に必要な `currentLevel` を渡す。
- `store`
  - `requested_rank_id` と `note` を validate する。
  - `RankRequestService::request()` を呼ぶ。
  - 失敗時は `requested_rank_id` のエラーとして画面へ戻す。
- `history`
  - ログインユーザーの申請履歴を関連情報付きで取得する。

### Web\RankDefinitionController

- `show(Rank $rank)`
  - 段位レベル、段位ラベル、参加条件表示を JSON で返す。
  - 画面遷移は持たず、プレビュー用途の API として扱う。

### Web\OmikujiController

- `draw`
  - 当日結果があれば新規作成しない。
  - トランザクションとロックで同日二重作成を抑止する。
  - DB 制約違反時も既に引いた扱いで返す。
  - 成功時はダッシュボードへ戻し、当日の結果をフラッシュメッセージで通知する。

### Admin\AdminDashboardController

- `index`
  - `pending` 段位申請数と、`entry` 状態で結果未入力の件数を集計する。
  - `admin.dashboard` view を返す。

### Admin\AdminTournamentController

- 大会 CRUD を担当する。
- 入力項目:
  - `title`
  - `description`
  - `event_date`
  - `entry_deadline`
  - `capacity`
  - `min_rank_level`
- `show`
  - 現行では編集画面へリダイレクトする。
- `index`
  - 大会を開催日降順でページングして返す。
- `store`
  - バリデーション後に `Tournament::create()` する。
  - 成功時は編集画面へ遷移する。
- `update`
  - バリデーション後に既存大会を更新する。
  - 成功時は編集画面へ遷移する。
- `destroy`
  - 大会を削除し、一覧へ戻す。

### Admin\AdminResultController

- `edit`
  - 大会のエントリー済み参加者と既存成績を表示する。
- `update`
  - `results` 配列を validate する。
  - 空行は保存しない。
  - `Result::updateOrCreate()` で登録・更新する。
  - トランザクション内で保存する。

### Admin\AdminRankRequestController

- `index`
  - 全申請を関連情報付きで表示する。
- `approve`
  - 管理者コメントを validate し、`RankRequestService::approve()` を呼ぶ。
  - 処理後は同一一覧へ戻る。
- `reject`
  - 管理者コメントを validate し、`RankRequestService::reject()` を呼ぶ。
  - 処理後は同一一覧へ戻る。

### Api Controllers

- `MeController::show()`
  - `users` と `rank` を JSON で返す。
  - `membership_expires_at` と `is_admin` を含める。
- `TournamentController::index()`
  - 大会一覧をページングして返す。
- `TournamentController::show()`
  - 大会 1 件を JSON で返す。
- `EntryController::store()`
  - `EntryService::entry()` を呼ぶ。
  - 成功時は `201` と `message`, `entry` を返す。
  - `HttpException` は JSON `message` と HTTP status へ変換する。
- `ResultController::index()`
  - ログインユーザーの成績一覧をページングして返す。
- `RankRequestController::store()`
  - `rank_id`、`note` を validate する。
  - `RankRequestService::request()` を呼ぶ。
  - `HttpException` は JSON `message` と HTTP status へ変換する。

## Service

### EntryService

- `entry(User $user, Tournament $tournament): Entry`
  - 年間登録期限を確認する。
  - ユーザー段位と大会最低段位を比較する。
  - エントリー締切を確認する。
  - トランザクション内で定員と重複を確認する。
  - 既存エントリーがある場合はそのまま返す。
  - 登録可能なら `Entry` を作成する。
- `cancel(User $user, Tournament $tournament): void`
  - 指定会員・指定大会のエントリーを `cancelled` に更新する。
  - 現行ではルート接続していないが、サービスとして実装済み。
  - ルート未接続のため、将来のキャンセル導線用の内部 API として扱う。

### MembershipService

- `preview(User $user): array`
  - 画面表示用に更新対象期間と実行可否を返す。
  - DB 更新は行わない。
- `renew(User $user, ?string $note = null): User`
  - 更新対象期間を解決する。
  - 更新不可なら `HttpException` を投げる。
  - `memberships` に履歴を作成する。
  - `users.membership_expires_at` を更新する。
- 年度判定:
  - 4/1 以降は同年 4/1 を年度開始とする。
  - 1/1〜3/31 は前年 4/1 を年度開始とする。
  - 年度終了は翌 3/31。
  - 翌年度更新開始日は年度終了年の 3/10。
  - 期限切れ/未登録会員は当年度を対象とする。
  - 同一年度が既に有効な場合は更新不可として返す。

### RankRequestService

- `request(User $user, Rank $targetRank, ?string $note = null): RankRequest`
  - 現在段位より下の申請を拒否する。
  - 未処理申請が既にある場合は拒否する。
  - `rank_requests` に `pending` で作成する。
  - `requested_rank_id` と `requested_level` を保存する。
- `approve(User $admin, RankRequest $request, ?string $comment = null): RankRequest`
  - 管理者以外を拒否する。
  - pending 以外を拒否する。
  - トランザクション内でユーザー段位と申請ステータスを更新する。
  - `approved_by`, `approved_at`, `admin_comment` を保存する。
- `reject(User $admin, RankRequest $request, ?string $comment = null): RankRequest`
  - 管理者以外を拒否する。
  - pending 以外を拒否する。
  - 却下情報を保存する。
  - `rejected_by`, `rejected_at`, `admin_comment` を保存する。

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
- `status` は `entry` / `cancelled` を想定するが、現行運用では `entry` が中心。
- `user_id + tournament_id` の一意制約で重複を防ぐ。

### Result

- 大会成績。
- `user`, `tournament` に属する。
- `placing`, `score`, `note` を扱う。
- `user_id + tournament_id` の一意制約で 1 件にする。

### Membership

- 年間登録履歴。
- `start_date`, `end_date` は `date` cast。
- `note` は任意の補足情報。
- `user_id + end_date` の index を持つ。

### RankRequest

- 段位申請。
- `pending` / `approved` / `rejected` を扱う。
- `user`, `rank`, `requestedRank`, `approver`, `rejector` を持つ。
- `requested_at`, `approved_at`, `rejected_at`, `admin_comment` を持つ。
- 表示補助:
  - `statusLabel()`
  - `handledByName()`
  - `displayDateYyMmDd()`

### OmikujiDraw

- 会員の日次おみくじ結果。
- 同一ユーザー・同一日付の重複作成を防ぐ。
- `user_id + drawn_on` の一意制約を前提にする。

## Support

### RankLabel

- `labelByLevel(int $level): string`
  - 段位レベルを段位/級ラベルへ変換する。
- `eligibleKyus(int $minLevel): string`
  - 大会参加条件表示へ変換する。
