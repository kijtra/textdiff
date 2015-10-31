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
