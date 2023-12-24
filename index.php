<?php
###################################################################################################
## ↓ 下記変更 OK
###################################################################################################

/* 読み込みたいCSVファイル */
$read_file = 'Book1.csv';

/* 一致させたい項目・値 */
$item_match = "完了日";
$val_match = "";

/* 分類したい項目 */
$item_sort = "担当者";

/* 新CSVファイル名（接頭辞） */
$fine_name = "test";

###################################################################################################
## ↓ 下記変更 NG
###################################################################################################

/* 文字コード */
$prev_format = "utf-8";
$next_format = "sjis";

$fopen_file = fopen($read_file, 'r');
$array_match = array();
$array_sort = array();
$line_first;
$int_match; //一致させたい項目の配列番号
$int_sort; //分類したい項目の配列番号

###################################################################################################
## ↓ 処理
###################################################################################################

/* 全体的な流れ */
if(catch_int() && match_val() && sort_item() && write_file()) {
	fclose($fopen_file);
	print("\n\n#### 完了しました。########\n\n");
}
// catch_int()  一致させたい項目と分類したい項目の配列番号の取得
// match_val()  一致した物件を検出
// sort_item()  分類
// write_file() 新CSVファイルに書き出し


/* 一致させたい項目と分類したい項目の配列番号の取得 */
function catch_int() {
	global $fopen_file;
	global $item_match;
	global $item_sort;
	global $prev_format;
	global $next_format;
	global $line_first;
	global $int_match;
	global $int_sort;

	$line_first = mb_convert_encoding(fgetcsv($fopen_file), $prev_format, $next_format);
	// ↑ 指定のCSVから1行取得し配列化

	foreach($line_first as $key => $item) {
		$item == $item_match ? $int_match = $key : null;
		$item == $item_sort ? $int_sort = $key : null;
	}
	// ↑ 一致させたい項目と分類したい項目の配列番号を変数に代入

	/*
	$line = mb_convert_encoding(fgetcsv($fopen_file), $prev_format, $next_format);
	$catch1 = preg_grep("/{$item_match}/", $line);
	$catch2 = preg_grep("/{$item_sort}/", $line);
	$int_match = array_search(end($catch1), $line);
	$int_sort = array_search(end($catch2), $line);
	*/

	$result = "・{$item_match}\n"."・{$item_sort}\n";
	// ↑ 一致させたい項目と分類したい項目を変数に代入

	print("\n\n#### 取得したい項目 #######\n\n");
	print($result);
	return true;
}

/* 一致した物件を検出 */
function match_val() {
	global $fopen_file;
	global $val_match;
	global $prev_format;
	global $next_format;
	global $array_match;
	global $int_match;

	$result = '';

	while($line = mb_convert_encoding(fgetcsv($fopen_file), $prev_format, $next_format)){
			if($line[$int_match] == $val_match) {
				$array_match[] = $line;
				// ↑ 一致した物件を配列に代入

				foreach($line as $item) {
					$result .= "{$item},";
				}
				$result .= "\n";
				// ↑ 一致した物件を1行にして変数に代入
			}
	}

	// print_r($array_match);

	print("\n\n#### 一致した物件を検出 #######\n\n");
	print($result);
	return true;
}

/* 分類 */
function sort_item() {
	global $array_match;
	global $array_sort;
	global $int_sort;

	$result = '';

	foreach($array_match as $array) {
		if($array[$int_sort] != "") {

			$count = 0;
			$int = 0;
			$line = "";

			if(count($array_sort) > 0) {
				foreach($array_sort as $item) {
					$catch = preg_grep("/{$array[$int_sort]}/", $item);
					$count += count($catch);
					if($count == 0) ++$int;
				}
			};
			/* ↑ テゴリの配列番号の取得 */
			
			foreach($array as $item) {
				$line .= $item.",";
			}
			$line .= "\n";
			/* ↑ 配列の内容をカンマ区切りに変換して変数に代入 */

			if($count == 0) {
				$array_sort[] = array($array[$int_sort], $line);
			} else {
				$array_sort[$int][1] .=  $line;
			}
			/* ↑ カンマ区切りの1行と担当者名を配列に代入 */

		}
	}

	foreach($array_sort as $item){
		$result .= "・{$item[0]}\n";
	}
	// ↑ 分類したい項目のリストを変数に代入

	// print_r($array_sort);

	print("\n\n#### 分類 #######\n\n");
	print($result);
	return true;
}

/* 新CSVファイルに書き出し */
function write_file() {
	global $fine_name;
	global $prev_format;
	global $next_format;
	global $array_sort;
	global $line_first;

	$int = 1;
	$result = '';
	$line = '';

	foreach($line_first as $item) {
		$line .= $item.",";
	}
	$line .= "\n";
	// ↑ 指定のCSVの最初の1行をカンマ区切りに変換して変数に代入

	foreach($array_sort as $item){
		$fw = fopen("{$fine_name}_{$int}.csv", 'w');
		fwrite($fw,mb_convert_encoding($line.$item[1], $next_format, $prev_format));
		fclose($fw);
		// ↑ 新CSVファイルに書き出し（連番化） → 新CSVを閉じる

		$result .= "{$fine_name}_{$int}.csv\n";
		// ↑ CSVファイル名を変数に代入
		
		++$int;
	}

	print("\n\n#### 新CSVファイルに書き出し #######\n\n");
	print($result);
	return true;
}
?>
