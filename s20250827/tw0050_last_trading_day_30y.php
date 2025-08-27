<?php
/**
 * 台灣 0050（0050.TW）近 30 年：每年最後一個交易日 Close / Adj Close、年報酬率、30 年 CAGR
 * - 以 Yahoo Finance 為主（先帶 Cookie 再抓 CSV）
 * - close/adj_close 四捨五入到小數點 2 位
 * - return 與 CAGR 以百分比字串到小數點 2 位
 * - 同時輸出 JSON 與 CSV
 */

date_default_timezone_set('Asia/Taipei');

// === 基本參數 ===
$ticker  = '0050.TW';                 // Yahoo Finance 台股代號
$period1 = strtotime('2003-01-01');   // 0050 上市後才有資料
$period2 = time();

$yahooHistoryPage = 'https://finance.yahoo.com/quote/0050.TW/history?p=0050.TW';
$yahooDownloadUrl = sprintf(
    'https://query1.finance.yahoo.com/v7/finance/download/%s?period1=%d&period2=%d&interval=1d&events=history&includeAdjustedClose=true',
    urlencode($ticker),
    $period1,
    $period2
);

// === 小工具：帶 Cookie 取檔 ===
function curl_get_with_cookies($url, $cookieFile, $referer = null)
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HEADER         => false,
        CURLOPT_COOKIEJAR      => $cookieFile,
        CURLOPT_COOKIEFILE     => $cookieFile,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'Accept: text/csv,application/csv,text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ],
    ]);
    if ($referer) curl_setopt($ch, CURLOPT_REFERER, $referer);
    $data = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return [$http, $data, $err];
}

function fetch_yahoo_csv($historyPage, $downloadUrl)
{
    $cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'yahoo_cookie_' . uniqid() . '.txt';
    // 1) 造訪歷史頁，取得 Cookie
    [$http1, $html, $err1] = curl_get_with_cookies($historyPage, $cookieFile);
    // 2) 帶 Cookie 下載 CSV
    [$http2, $csv, $err2] = curl_get_with_cookies($downloadUrl, $cookieFile, $historyPage);
    @unlink($cookieFile);
    return [$http2, $csv, $err2];
}

// === 嘗試 Yahoo ===
[$yHttp, $yData, $yErr] = fetch_yahoo_csv($yahooHistoryPage, $yahooDownloadUrl);
if ($yHttp !== 200 || $yData === false || stripos($yData, 'Date,Open,High,Low,Close') !== 0) {
    fwrite(STDERR, "下載 Yahoo CSV 失敗（HTTP $yHttp）。錯誤：$yErr\n");
    exit(1);
}

// === 解析 Yahoo CSV：Date,Open,High,Low,Close,Adj Close,Volume ===
$lastPerYear = [];

$fp = fopen('php://memory', 'r+');
fwrite($fp, $yData);
rewind($fp);

$header = fgetcsv($fp);
$idx = array_flip($header);
foreach (['Date', 'Close', 'Adj Close'] as $col) {
    if (!isset($idx[$col])) {
        echo "Yahoo CSV 欄位缺少：$col\n";
        exit(1);
    }
}

while (($row = fgetcsv($fp)) !== false) {
    if (count($row) < count($header)) continue;
    $rec = array_combine($header, $row);
    if (empty($rec['Date']) || $rec['Close'] === 'null') continue;

    $date = $rec['Date'];
    $ts   = strtotime($date);
    if ($ts === false) continue;

    $year = (int)date('Y', $ts);

    // 只保留該年「最大日期」那筆（年末最後交易日）
    if (!isset($lastPerYear[$year]) || $ts > strtotime($lastPerYear[$year]['Date'])) {
        $lastPerYear[$year] = $rec; // 保留原欄位
    }
}
fclose($fp);

// 只取最近 30 年（0050 不足 30 年也沒關係）
$years = array_keys($lastPerYear);
sort($years);
$years = array_slice($years, -30);

// 組結果
$result = [];
foreach ($years as $y) {
    $r = $lastPerYear[$y];
    $close     = isset($r['Close']) ? (float)$r['Close'] : null;
    $adjClose  = isset($r['Adj Close']) && $r['Adj Close'] !== '' ? (float)$r['Adj Close'] : null;

    $result[] = [
        'year'       => $y,
        'date'       => $r['Date'] ?? '',
        'close'      => is_numeric($close) ? round($close, 2) : null,
        'adj_close'  => is_numeric($adjClose) ? round($adjClose, 2) : null,
    ];
}

// 年報酬率 (以上一年為基準)
for ($i = 0; $i < count($result); $i++) {
    if ($i === 0) {
        $result[$i]['return_close'] = null;
        $result[$i]['return_adj_close'] = null;
        continue;
    }
    $prevClose = $result[$i - 1]['close'];
    $prevAdj   = $result[$i - 1]['adj_close'];
    $curClose  = $result[$i]['close'];
    $curAdj    = $result[$i]['adj_close'];

    $returnClose = (is_numeric($prevClose) && $prevClose != 0 && is_numeric($curClose))
        ? ($curClose - $prevClose) / $prevClose * 100
        : null;
    $returnAdj = (is_numeric($prevAdj) && $prevAdj != 0 && is_numeric($curAdj))
        ? ($curAdj - $prevAdj) / $prevAdj * 100
        : null;

    $result[$i]['return_close'] = is_null($returnClose) ? null : round($returnClose, 2) . "%";
    $result[$i]['return_adj_close'] = is_null($returnAdj) ? null : round($returnAdj, 2) . "%";
}

// CLI 表格輸出
echo "0050（$ticker）近 30 年：每年最後交易日收盤價與報酬率（Close / Adj Close）\n";
echo str_pad("Year", 6) .
    str_pad("Date", 14) .
    str_pad("Close", 12) .
    str_pad("AdjClose", 12) .
    str_pad("R_Close", 12) .
    str_pad("R_Adj", 12) . "\n";
echo str_repeat("-", 68) . "\n";

foreach ($result as $row) {
    echo str_pad($row['year'], 6) .
        str_pad($row['date'], 14) .
        str_pad(isset($row['close']) && $row['close'] !== null ? number_format($row['close'], 2, '.', '') : 'null', 12) .
        str_pad(isset($row['adj_close']) && $row['adj_close'] !== null ? number_format($row['adj_close'], 2, '.', '') : 'null', 12) .
        str_pad($row['return_close'] ?? 'null', 12) .
        str_pad($row['return_adj_close'] ?? 'null', 12) . "\n";
}

// 30 年 CAGR（用第一年 vs 最後一年，有值才算）
$N = count($result) - 1;
$firstClose = $result[0]['close'];
$lastClose  = $result[$N]['close'];
$firstAdj   = $result[0]['adj_close'];
$lastAdj    = $result[$N]['adj_close'];

$cagrClose = (is_numeric($firstClose) && is_numeric($lastClose) && $firstClose > 0)
    ? pow($lastClose / $firstClose, 1 / $N) - 1
    : null;
$cagrAdj = (is_numeric($firstAdj) && is_numeric($lastAdj) && $firstAdj > 0)
    ? pow($lastAdj / $firstAdj, 1 / $N) - 1
    : null;

echo "\n=== 30 年年化報酬率 (CAGR) ===\n";
echo "Close CAGR     : " . (is_null($cagrClose) ? 'null' : round($cagrClose * 100, 2) . "%") . "\n";
echo "Adj Close CAGR : " . (is_null($cagrAdj) ? 'null' : round($cagrAdj * 100, 2) . "%") . "\n";

// === JSON 輸出 ===
$jsonPath = __DIR__ . '/tw0050_last_trading_day_30y.json';
$output = [
    'data' => $result,
    'cagr' => [
        'close' => is_null($cagrClose) ? null : round($cagrClose * 100, 2) . "%",
        'adj_close' => is_null($cagrAdj) ? null : round($cagrAdj * 100, 2) . "%"]
];
file_put_contents($jsonPath, json_encode($output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "\nJSON 已輸出：$jsonPath\n";

// === CSV 輸出 ===
$csvPath = __DIR__ . '/tw0050_last_trading_day_30y.csv';
$fp = fopen($csvPath, 'w');
fputcsv($fp, ['year', 'date', 'close', 'adj_close', 'return_close', 'return_adj_close']);
foreach ($result as $row) {
    fputcsv($fp, [
        $row['year'],
        $row['date'],
        isset($row['close']) && $row['close'] !== null ? number_format($row['close'], 2, '.', '') : null,
        isset($row['adj_close']) && $row['adj_close'] !== null ? number_format($row['adj_close'], 2, '.', '') : null,
        $row['return_close'],
        $row['return_adj_close']
    ]);
}
fputcsv($fp, []); // 空行
fputcsv($fp, [
    'CAGR',
    '',
    '',
    '',
    is_null($cagrClose) ? null : round($cagrClose * 100, 2) . "%",
    is_null($cagrAdj) ? null : round($cagrAdj * 100, 2) . "%"]);
fclose($fp);
echo "CSV 已輸出：$csvPath\n";
