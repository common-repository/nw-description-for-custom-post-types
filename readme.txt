=== NW Description For Custom Post Types ===
Contributors: NAKWEB
Tags: description, meta, custom post type, nakweb, nw
Requires at least: 4.9.13
Tested up to: 5.8.3
Stable tag: 1.3.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0

== Description ==

WPコアの投稿タイプおよびカスタム投稿タイプのアーカイブページ、シングルページのメタディスクリプションを設定できます。

* メタディスクリプションは</head>の直前に自動的に挿入されます。

* アーカイブページ用メタディスクリプションの優先度
     1. プラグインの設定内容
     2. ウェブサイトのキャッチフレーズ

* シングルページ用メタディスクリプションの優先度
     1. 記事の本文から抜粋
     2. プラグインの設定内容
     3. ウェブサイトのキャッチフレーズ

* NW系プラグインについて
     * 本プラグイン以外にもNW系として複数のプラグインを作成しています。
     * すべてのNW系プラグインの設定画面は同一のトップレベルメニューのサブメニューとして追加されます。
     * トップレベルメニューのラベルはプラグイン上で変更が可能です。


== Installation ==

1. From the WP admin panel, click “Plugins” -> “Add new”.
2. In the browser input box, type “NW Description For Custom Post Types”.
3. Select the “NW Description For Custom Post Types” plugin and click “Install”.
4. Activate the plugin.

OR…

1. Download the plugin from this page.
2. Save the .zip file to a location on your computer.
3. Open the WP admin panel, and click “Plugins” -> “Add new”.
4. Click “upload”.. then browse to the .zip file downloaded from this page.
5. Click “Install”.. and then “Activate plugin”.

== Changelog ==
= 1.3.7 =
* 不具合修正

= 1.3.6 =
* 軽度な修正

= 1.3.5 =
* ディスクリプションがない場合は出力しない

= 1.3.2 =
* シングルページにて出力されるディスクリプションの内容変更
　※いずれかが出力　上から順に優先度高 
　１）AIO SEOディスクリプション
  ２）当プラグイン 共通ディスクリプション
　３）ACF カスタムフィールドの値（テキスト、テキストエリア、wysiwyg）
  
= 1.3.1 =
* シングルページにてACFカスタムフィールドの値を自動取得して
　ディスクリプションとして自動出力するよう調整。
　※いずれかが出力　上から順に優先度高 
　１）AIO SEOディスクリプション
　２）ACF カスタムフィールドの値（テキスト、テキストエリア、wysiwyg）
  ３）当プラグイン 共通ディスクリプション

= 1.2.3 =
* 軽度な修正

= 1.2.2 =
* 軽度な修正

= 1.2.1 =
* 軽度な修正

= 1.2.0 =
* 出力するメタタグ追加
* ALL IN ONE SEOプラグインとの競合調整

= 1.1.1 =
* 管理権限の不具合を改修。

= 1.1.0 =
* 対象を拡張。

= 1.0.1 =
* 軽微な変更。

= 1.0.0 =
* Initial release.

== Upgrade notice ==

= 1.3.2 =
* シングルページにて出力されるディスクリプションの内容変更
　※いずれかが出力　上から順に優先度高 
　１）AIO SEOディスクリプション
  ２）当プラグイン 共通ディスクリプション
　３）ACF カスタムフィールドの値（テキスト、テキストエリア、wysiwyg）

= 1.3.1 =
* シングルページにてACFカスタムフィールドの値を自動取得して
　ディスクリプションとして自動出力するよう調整。
　※いずれかが出力　上から順に優先度高 
　１）AIO SEOディスクリプション
　２）ACF カスタムフィールドの値（テキスト、テキストエリア、wysiwyg）
  ３）当プラグイン 共通ディスクリプション

= 1.2.3 =
* 軽度な修正

= 1.2.2 =
* シングルページで以下を満たしている場合はNW側のディスクリプションは出力されないよう調整。
　１）ALL IN ONE SEOプラグインが有効で克「〇〇コンテンツ」のみ設定されている。
　２）デフォルトのコンテントエディタに文字が設定されている。

= 1.2.1 =
* 軽度な修正

= 1.2.0 =
* シングルページではALL IN ONE SEOプラグインのメタタグと同時に出力されないよう調整。
　* ALL IN ONE SEO 側を優先
   (「〇〇コンテンツ」のみ設定されている場合を除く。)
* メタタグ「og:description」「twitter:description」の出力。

= 1.1.1 =
* 他のプラグインにより作成された管理権限グループでは、本プラグインの設定画面を開けない不具合を解消。

= 1.1.0 =
* タームアーカイブ用ディスクリプションの入力欄を追加。
* メタディスクリプション自動挿入の是非を設定できるように変更。

= 1.0.1 =
* アーカイブページにおいて、記事が０件でもプラグインの設定値を取得できるように変更。
