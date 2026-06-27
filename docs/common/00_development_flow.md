# 00 Development Flow

## 目的

このドキュメントは、今後の開発を `Requirements.md` 起点で進めるための共通フローを定義する。

開発は次の順で進める。

1. `Requirements.md` を作成する
2. `SPECIFICATION.md` を作成する
3. `02_basic_design.md` を作成する
4. `03_detailed_design.md` を作成する
5. 詳細設計をもとにコードを書く

## 基本原則

- 各工程は直前工程の成果物を入力とする。
- 未確定事項は次工程へ勝手に持ち越さない。
- 仕様の変更は、必ず Requirements か SPECIFICATION に戻して整理する。
- 実装者は、上位ドキュメントにない判断を独断で追加しない。
- 各工程の成果物は、次工程がそのまま読める粒度で書く。

## 成果物の役割

### Requirements.md

- 何を作るかを整理する。
- 未確定事項、仮説事項、確定事項を分ける。
- 質問一覧を残す。
- 後工程の前提を固める。

### SPECIFICATION.md

- Requirements を実装可能な仕様に翻訳する。
- 機能単位で何を作るかを明確にする。
- 業務ルール、画面、API、データの関係を固定する。
- ここで仕様の抜けを潰す。

### 02_basic_design.md

- 画面、API、DB、処理の全体像をまとめる。
- 機能間の関係と責務分担を明記する。
- 実装方針の大枠を決める。

### 03_detailed_design.md

- Controller / Service / Model 単位の実装方針を定義する。
- 入出力、例外、分岐、保存単位を具体化する。
- コードを書く前に迷わない状態にする。

### コード

- 詳細設計に従って実装する。
- 実装中に仕様差分が出た場合は、先に上位ドキュメントを更新する。
- 仕様なしの場当たり実装はしない。

## フローのゲート

### 1. Requirements から SPECIFICATION へ

次を満たしたら進める。

- 主要な機能が列挙されている
- 未確定事項が明示されている
- 仮説事項が仮説として書かれている
- 主要な利用者と権限が整理されている

### 2. SPECIFICATION から 02_basic_design.md へ

次を満たしたら進める。

- 機能の振る舞いが固定されている
- 画面、API、DB の関係が整理されている
- 業務ルールが実装可能な粒度で書かれている

### 3. 02_basic_design.md から 03_detailed_design.md へ

次を満たしたら進める。

- 機能単位の責務が明確である
- 画面遷移、API 入出力、DB 更新の流れが固まっている
- 例外時の扱いが決まっている

### 4. 03_detailed_design.md から コードへ

次を満たしたら進める。

- 変更対象のクラスとファイルの責務が分かっている
- 入出力と副作用が定義されている
- テスト観点が決まっている

## 変更ルール

- 仕様変更が発生したら、下流の設計書だけを直すのではなく、必ず上流から見直す。
- 実装中の気づきで要件が変わる場合は、Requirements まで戻って記録する。
- 後工程が上書きしてよいのは、上流で未確定だった部分だけ。

## 共通参照

- `docs/spec-driven/01_specification.md`
- `docs/spec-driven/02_basic_design.md`
- `docs/spec-driven/03_detailed_design.md`
- `docs/spec-driven/04_test_specification.md`
- `docs/spec-driven/05_development_tasks.md`
- `docs/common/06_project_structure.md`
- `docs/common/07_error_logging_policy.md`

