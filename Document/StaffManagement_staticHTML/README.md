# StaffManagement

「派遣管理アプリ 画面案」に含まれる全35画面を、個別に表示・遷移できるHTML5/CSS3/Vanilla JavaScriptプロジェクトです。

## 起動

`index.html` を標準ブラウザで直接開いてください。サーバー、Node.js、ビルド、パッケージインストールは不要です。

## 構成

- `index.html`: 全35画面の一覧とデバイス絞り込み
- `pages/`: デザインから抽出した各画面
- `css/common.css`: 基本スタイルと共通状態
- `css/layout.css`: 共通レイアウト
- `css/components.css`: 全画面で共有するコンポーネントスタイル
- `css/pages/`: 各画面だけで使用するスタイル
- `css/catalog.css`: 画面一覧専用スタイル
- `js/common.js`: 共通のVanilla JavaScript
- `js/navigation.js`: 画面遷移とフォーム操作
- `js/catalog.js`: 画面一覧の生成と絞り込み

HTML内にインラインスタイルやインラインスクリプトはありません。React、Vue、JSX、Node.jsなどの実行時依存もありません。
