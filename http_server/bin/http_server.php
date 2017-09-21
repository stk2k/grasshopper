<?php
function write_response($client_sock, $method, $data, $status, $message, $body)
{
    $res = "HTTP/1.1 {$status} {$message}\n";
    $res .= "Content-Type: text/html\n";
    $res .= "Method: $method\n";
    $res .= "Data: " . json_encode($data). "\n";
    $res .= "\n\n{$body}";
    socket_write($client_sock, $res);
}
function write_not_found($client_sock, $method, $data)
{
    write_response($client_sock, $method, $data, 404, 'Not Found', '<html><body><h1>Not Found</h1></body></html>');
}
function find_content($webroot, &$path)
{
    if ($path === '/')
    {
        $_path = '/index.html';
        if (is_file($webroot.$_path)) {
            $path = $_path;
            return @file_get_contents($webroot.$path);
        }
        return false;
    }
    if (!is_file($webroot.$path)){
        return false;
    }
    $file = $webroot.$path;
    $ext = substr($file,strlen($file)-4,4);
    switch($ext)
    {
        case '.html':
            return @file_get_contents($file);
            
    }
    return false;
}

$webroot = dirname(__DIR__) . '/html';

$sock = @socket_create_listen(8080);
if(!$sock){die("port in use\n");}
$client_sock = socket_accept($sock);
while(true){
    $buf = socket_read($client_sock, 1024);
    $tmp = explode("\r\n\r\n",$buf);
    $body = isset($tmp[1]) ? $tmp[1] : '';
    $data = [];
    parse_str($body, $data);
    if (preg_match('/GET ([^ ]+)/', $buf, $m)){
        $path = $m[1];
        $method = 'GET';
        $content = find_content($webroot, $path);
        if ($content){
            write_response($client_sock, $method, $data, 200, 'OK', $content);
        }
        else{
            write_not_found($client_sock, $method, $data);
        }
    }
    else if (preg_match('/POST ([^ ]+)/', $buf, $m)){
        $path = $m[1];
        $method = 'POST';
        $content = find_content($webroot, $path);
        if ($content){
            write_response($client_sock, $method, $data, 200, 'OK', $content);
        }
        else{
            write_not_found($client_sock, $method, $data);
        }
    }
    else if (preg_match('/HEAD ([^ ]+)/', $buf, $m)){
        $path = $m[1];
        $method = 'HEAD';
        $content = find_content($webroot, $path);
        if ($content){
            write_response($client_sock, $method, $data, 200, 'OK', '');
        }
        else{
            write_not_found($client_sock, $method, $data);
        }
    }
    socket_close($client_sock);
    $client_sock = socket_accept($sock);
}