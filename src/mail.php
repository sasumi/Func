<?php

/**
 * mail function
 */

namespace LFPhp\Func;

use Exception;

/**
 * @depends
 * Send email using SMTP
 * @param array(mail_to, subject, message, reply_to) $content 邮件内容
 * @param array(host, port, username, password, from_email, from_name, encryption) $server_config SMTP 配置
 */
function mail_send_by_smtp(array $content, array $server_config) {
	param_check_required($content, ['mail_to', 'subject', 'message']);
	$mail_to = $content['mail_to'];
	$reply_to = $content['reply_to'] ?: '';
	$subject = $content['subject'];
	$message = $content['message'] ?: '';
	$content_type = $content['content_type'] ?? 'text/plain';

	//smtp server config
	param_check_required($server_config, ['host', 'username', 'password', 'from_email', 'from_name']);
	$smtp_host = $server_config['host']; //SMTP 服务器地址
	$smtp_port = $server_config['port'] ?? 587; //SMTP 端口（通常为 25, 465 或 587）
	$smtp_user = $server_config['username']; //SMTP 用户名
	$smtp_password = $server_config['password']; //SMTP 密码
	$from_mail = $server_config['from_email']; //发件人邮箱
	$from_name = $server_config['from_name']; //发件人名称
	$encryption = $server_config['encryption'] ?? null; // 加密方式（如 'ssl', 'tls' 或 null）

	//邮件头
	$data_list = [
		'To' => $mail_to,
		'Subject' => $subject,
		'From' => "$from_name <$from_mail>",
		'Reply-To' => $reply_to,
		'Content-Type' => "$content_type; charset=UTF-8",
		'MIME-Version' => '1.0',
	];
	$data_list = array_clean_empty($data_list);

	$data_list[] = "\r\n$message";

	//邮件内容
	$raw = "";
	foreach ($data_list as $key => $value) {
		$raw .= "$key: $value\r\n";
	}

	//连接到 SMTP 服务器
	$socket = fsockopen(($encryption === 'ssl' ? "ssl://$smtp_host" : $smtp_host), $smtp_port, $errno, $errstr, 10);
	if (!$socket) {
		throw new Exception("连接失败：$errstr ($errno)");
	}

	//读取服务器响应
	function getServerResponse($socket) {
		$response = '';
		while ($str = fgets($socket, 515)) {
			$response .= $str;
			if (substr($str, 3, 1) == ' ') {
				break;
			}
		}
		return $response;
	}

	//发送命令到服务器
	function sendCommand($socket, $command) {
		fwrite($socket, $command . "\r\n");
		return getServerResponse($socket);
	}

	//SMTP 通信
	$response = getServerResponse($socket);
	if (strpos($response, '220') === false) {
		throw new Exception("连接失败：$response");
	}

	sendCommand($socket, "EHLO localhost");

	// 如果使用 STARTTLS，则启动加密
	if ($encryption === 'tls') {
		$response = sendCommand($socket, "STARTTLS");
		if (strpos($response, '220') === false) {
			throw new Exception("STARTTLS 启动失败：$response");
		}
		if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
			throw new Exception("无法启用加密连接");
		}
		// 重新发送 EHLO
		sendCommand($socket, "EHLO localhost");
	}

	sendCommand($socket, "AUTH LOGIN");
	sendCommand($socket, base64_encode($smtp_user));
	sendCommand($socket, base64_encode($smtp_password));
	sendCommand($socket, "MAIL FROM: <$from_mail>");
	sendCommand($socket, "RCPT TO: <$mail_to>");
	sendCommand($socket, "DATA");
	sendCommand($socket, $raw . "\r\n.");
	$response = sendCommand($socket, "QUIT");
	fclose($socket);

	if (strpos($response, '221') === false) {
		throw new Exception("邮件发送失败：$response");
	}
}
