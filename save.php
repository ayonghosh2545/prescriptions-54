<?php
// save.php — সার্ভারে ডাটা সেভ ও অটো-ব্যাকআপ (PHP ৫.৬+)
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$H='beead79933cfffcaf1d65f4b67aa8d7e4bd0fce68c571d882b11a7ffb5d1199a'; // SHA-256 হ্যাশ (কোড নিজে এখানে লেখা নেই)
function out($a){echo json_encode($a);exit;}
$raw=file_get_contents('php://input');
if(strlen($raw)>1048576)out(['ok'=>false,'error'=>'size']);
$in=json_decode($raw,true);
if(!is_array($in)||!isset($in['code'],$in['data'])||!is_array($in['data'])||empty($in['data']['name']))out(['ok'=>false,'error'=>'bad']);
if(!hash_equals($H,hash('sha256',trim((string)$in['code'])))){sleep(1);out(['ok'=>false,'error'=>'code']);}
$dir=__DIR__;$bk=$dir.'/backups';
if(!is_dir($bk)){@mkdir($bk,0755);@file_put_contents($bk.'/.htaccess',"Require all denied\nDeny from all\n");}
if(is_file($dir.'/data.json'))@copy($dir.'/data.json',$bk.'/data-'.date('Ymd-His').'.json');
$f=glob($bk.'/data-*.json');sort($f);
while(count($f)>30){@unlink(array_shift($f));}
$ok=@file_put_contents($dir.'/data.json',json_encode($in['data'],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT),LOCK_EX);
out(['ok'=>$ok!==false,'error'=>$ok===false?'write':null]);
