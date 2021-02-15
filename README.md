# TextDiff class

Simple Text Diff PHP class.  
日本語の場合は"ある程度"分かち書きしてわかりやすくしてます。

[DEMO PAGE](http://demo.kijtra.com/textdiff/)

## Usage

```php
<?php
include('TextDiff.php');

$old_text = "abcg efgh ijk";

$new_text = "abcg efg hijk";

$diff = new TextDiff($old_text, $new_text);

// Get raw data
$data = $diff->getData();

// Get HTML data (use table tag)
$html = $diff->getHtml();
```

## License

MIT

## ChangeLog

### v2.0.0

- とりあえず PHP5.6 以上対応とする
- **名前空間追加**
  ```
  <?php>
  include('TextDiff.php');
  $diff = new TextDiff($text1, $text2);
  ```
  ↓↓↓
  ```
  <?php
  use Kijtra\TextDiff\TextDiff;
  $diff = new TextDiff($text1, $text2);
  ```
- Composer 対応
- ヘルパー関数追加

  ```
  <?php
  // 配列で取得
  $diff = text_diff($text1, $text2);

  // HTMLで取得
  $html = text_diff_html($text1, $text2);
  ```

- テスト作成

### v1.0.1

- Warning エラー検証追加（[#4](https://github.com/kijtra/textdiff/pull/4)）

### v1.0.0

- 初回公開
