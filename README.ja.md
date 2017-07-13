Grasshopper, PHP HTTP Multi Request Client
=======================

## 説明

GrasshopperはもうひとつのPHP用cURLライブラリで、HTTPリクエストの送信を簡単にします。
このライブラリは一度に複数のリクエストを処理することができます。

## 特徴

- １度の呼び出しで複数のリクエストを処理できます。
- 簡単に使えます: インタフェースがシンプル
- 様々なエラーハンドリング: 手続き型とコールバックに対応

## デモ

```php
use Grasshopper\Grasshopper;
use \Grasshopper\event\SuccessEvent;
use \Grasshopper\event\ErrorEvent;
 
$hopper = new Grasshopper();

$url = 'http://example.com';

$hopper->addRequest($url);

$result = $hopper->waitForAll();

$res = $result[$url];
if ( $res instanceof SuccessEvent ){
    // success
    $status = $res->getResponse()->getStatusCode();
    $body = $res->getResponse()->getBody();
    echo "success: status=$status" . PHP_EOL;
    echo $body . PHP_EOL;
}
elseif ( $res instanceof ErrorEvent ){
    // error
    echo "error: " . $res->getError()->getMessage() . PHP_EOL;
}
 
```

## 使い方

1. Grashopperオブジェクトを作成します。
2. HttpGet/HttpPostRequestオブジェクトを生成し、Grashopperオブジェクトに追加します。
3. Grasshopper#waitforAll()メソッドを実行します。
4. 返却された配列からレスポンスを取得します。配列のキーはリクエスト時のURLになっています。
5. レスポンスがSuccessEventであるかErrorEventであるか確認します。SuccessEventはリクエストが成功したことを表し, ErrorEventは失敗を表します。
6. SuccessEventからはレスポンスオブジェクトを取得できます。これはステータスコードとレスポンスボディを含んでいます。
7. ErrorEventからはエラー情報を取得できます。エラー情報はエラーコードやエラーメッセージを含んでいます。

## 前提条件

PHP 5.5 かまたはそれ以上

## Grasshopperのインストール方法

GrasshopperのインストールはComposerからのインストールが便利です。

[Composer](http://getcomposer.org).

```bash
composer require stk2k/grasshopper
```

インストール後、Composerのオートローダーを以下のようにrequireしてください。

```php
require 'vendor/autoload.php';
```

## ライセンス
[MIT](https://github.com/stk2k/grasshopper/blob/master/LICENSE)

## 作者

[stk2k](https://github.com/stk2k)


## 免責事項

このソフトウェアは無保証です。
私たちはこのソフトウェアによるいかなる結果も責任を負いません。
あなた自身の責任においてこのソフトウェアを使用するようにしてください。



